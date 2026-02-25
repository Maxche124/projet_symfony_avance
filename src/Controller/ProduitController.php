<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\Product\DTO\ProductFlowData;
use App\Form\Product\Step\ProductTypeStepType;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductConfirmStepType;
use App\Repository\ProduitRepository;
use App\Service\ProduitCsvExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produit')]
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
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();

        $flowData = $session->get('product_flow_data', new ProductFlowData());
        $step = $request->query->getInt('step', 1);

        $formType = match ($step) {
            1 => ProductTypeStepType::class,
            2 => ProductDetailsStepType::class,
            3 => $flowData->type === 'physical'
                ? ProductLogisticsStepType::class
                : ProductLicenseStepType::class,
            4 => ((float)$flowData->price > 1000)
                ? ProductConfirmStepType::class
                : null,
            default => null
        };

        if (!$formType) {
            return $this->redirectToRoute('app_produit_new', ['step' => 1]);
        }

        $form = $this->createForm($formType, $flowData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session->set('product_flow_data', $flowData);

            $nextStep = $step + 1;

            if ($step === 4 || ($step === 3 && (float)$flowData->price <= 1000)) {

                $produit = new Produit();
                $produit->setName($flowData->name);
                $produit->setDescription($flowData->description);
                $produit->setPrice($flowData->price);

                $em->persist($produit);
                $em->flush();

                $session->remove('product_flow_data');

                return $this->redirectToRoute('app_produit_index');
            }

            return $this->redirectToRoute('app_produit_new', ['step' => $nextStep]);
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/{id}/edit', name: 'app_produit_edit')]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(
        Produit $produit,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $session = $request->getSession();

        if (!$session->has('product_flow_data_edit')) {

            $flowData = new ProductFlowData();

            $flowData->name = $produit->getName();
            $flowData->description = $produit->getDescription();
            $flowData->price = $produit->getPrice();

            $flowData->type = 'physical';

            $session->set('product_flow_data_edit', $flowData);
        }

        $flowData = $session->get('product_flow_data_edit');

        $step = $request->query->getInt('step', 1);

        $formType = match ($step) {
            1 => ProductTypeStepType::class,
            2 => ProductDetailsStepType::class,
            3 => $flowData->type === 'physical'
                ? ProductLogisticsStepType::class
                : ProductLicenseStepType::class,
            4 => ((float)$flowData->price > 1000)
                ? ProductConfirmStepType::class
                : null,
            default => null
        };

        if (!$formType) {
            return $this->redirectToRoute('app_produit_edit', [
                'id' => $produit->getId(),
                'step' => 1
            ]);
        }

        $form = $this->createForm($formType, $flowData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session->set('product_flow_data_edit', $flowData);

            $nextStep = $step + 1;

            if ($step === 4 || ($step === 3 && (float)$flowData->price <= 1000)) {

                $produit->setName($flowData->name);
                $produit->setDescription($flowData->description);
                $produit->setPrice($flowData->price);

                $em->flush();

                $session->remove('product_flow_data_edit');

                return $this->redirectToRoute('app_produit_index');
            }

            return $this->redirectToRoute('app_produit_edit', [
                'id' => $produit->getId(),
                'step' => $nextStep
            ]);
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
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

    #[Route('/produit/export/csv', name: 'app_produit_export_csv')]
    #[IsGranted('ROLE_MANAGER')]
    public function exportCsv(
        ProduitRepository $produitRepository,
        ProduitCsvExporter $csvExporter
    ) {
        $produits = $produitRepository->findAll();

        return $csvExporter->export($produits);
    }
}