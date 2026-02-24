<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produit')]
#[IsGranted('ROLE_MANAGER')]
final class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'asc');

        $allowedFields = ['id', 'name', 'price'];
        $direction = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';

        if (!in_array($sort, $allowedFields)) {
            $sort = 'id';
        }

        $produits = $produitRepository->findBy([], [$sort => $direction]);

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/new', name: 'app_produit_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $session = $request->getSession();

        $step = $session->get('product_step', 1);
        $productData = $session->get('product_data', []);

        $form = match($step) {

            1 => $this->createForm(ProductTypeStepType::class, $productData),

            2 => $this->createForm(ProductDetailsStepType::class, $productData),

            3 => isset($productData['type']) && $productData['type'] === 'physical'
                ? $this->createForm(ProductLogisticsStepType::class, $productData)
                : $this->createForm(ProductLicenseStepType::class, $productData),

            4 => $this->createForm(ProductConfirmStepType::class, $productData),

            default => $this->createForm(ProductTypeStepType::class, $productData)
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productData = array_merge(
                $productData,
                $form->getData()
            );

            $session->set('product_data', $productData);

            if ($step === 1 && isset($productData['price']) && $productData['price'] > 1000) {
                $session->set('product_step', 3);
            } else {
                $session->set('product_step', $step + 1);
            }

            if ($step === 4) {

                $product = new Produit();

                $product->setName($productData['name']);
                $product->setDescription($productData['description']);
                $product->setPrice($productData['price']);

                $entityManager->persist($product);
                $entityManager->flush();

                $session->remove('product_step');
                $session->remove('product_data');

                return $this->redirectToRoute('app_produit_index');
            }

            return $this->redirectToRoute('app_produit_new');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'total_steps' => 4
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}