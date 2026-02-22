<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Exceptions\InvalidAmountFormat;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/compte')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CompteController extends AbstractController
{
    #[Route(name: 'app_compte_index', methods: ['GET'])]
    #[IsGranted('ROLE_MANAGER')]
    public function index(CompteRepository $compteRepository): Response
    {
        return $this->render('compte/index.html.twig', [
            'comptes' => $compteRepository->findAll(),
        ]);
    }

    #[Route('/mes-comptes',name: 'app_compte_client', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesComptes(CompteRepository $compteRepository): Response
    {
        $client = $this->getUser()->getClient();

        $comptes = $compteRepository->findBy(['owner' => $client]);

        return $this->render('compte/index.html.twig', [
            'comptes' => $comptes,
        ]);
    }

    #[Route('/{id}/epargner', name: 'app_compte_epargner', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function epargner(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        // managers can operate on any account; users only on their own
        if (!$this->isGranted('ROLE_MANAGER') && $compte->getOwner() !== $this->getUser()->getClient()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $montant = (float) $request->request->get('montant');
            $MAX_MONTANT = 999_000_000;
            try {
                if ($montant <= 0) {
                    throw new \InvalidArgumentException('Le montant doit être supérieur à 0.');
                }
                if ($montant > $MAX_MONTANT) {
                    throw new \InvalidArgumentException('Le montant maximum autorisé est de 999 000 000 €.');
                }
                $compte->crediter($montant);
                $entityManager->flush();
                $this->addFlash('success', 'Le compte a été crédité de ' . number_format($montant, 2, ',', ' ') . ' €');
                
                return $this->redirectToRoute('app_compte_client');
            } catch (InvalidAmountFormat $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->render('compte/epargner.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/{id}/debiter', name: 'app_compte_debiter', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function debiter(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_MANAGER') && $compte->getOwner() !== $this->getUser()->getClient()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $montant = (float) $request->request->get('montant');
            try {
                $compte->debiter($montant);
                $entityManager->flush();
                $this->addFlash('success', 'Le compte a été débité de ' . $montant . ' €');
                return $this->redirectToRoute('app_compte_client');
            } catch (InvalidAmountFormat $error) {
                if (!$compte->getDecouvertStatus() && $montant > $compte->getSolde()) {
                    $this->addFlash('error', 'Vous ne pouvez pas débiter cette somme : le découvert n\'est pas autorisé sur ce compte');
                } else {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('compte/debiter.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/new', name: 'app_compte_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_show', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function show(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        if($request->isMethod('POST')) {
            $montant = (float) $request->request->get('montant');
            $action = $request->request->get('action');

            try {
                if($action === 'credit') {
                    $compte->crediter($montant);
                    $this->addFlash('success', 'Le compte a été crédité de '.$montant.' €');
                } elseif ($action === 'debit') {
                    $compte->debiter($montant);
                    $this->addFlash('success', 'Le compte a été débité de '.$montant.' €');
                }
                $entityManager->flush();
            } catch (InvalidAmountFormat $error) {
                $this->addFlash('error', $error->getMessage());
            } catch (\Exception $error) {
                $this->addFlash('error', 'Une erreur technique est survenu: '.$error->getMessage());
            }
        }

        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_compte_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function delete(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
