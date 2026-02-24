<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    use TargetPathTrait;
    #[Route(path: '/redirect-after-login', name: 'app_redirect_after_login')]
    public function redirectAfterLogin(Request $request): Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), 'main');

        if ($targetPath) {
            $this->removeTargetPath($request->getSession(), 'main');
            return $this->redirect($targetPath);
        }

        if ($this->isGranted('ROLE_MANAGER')) {
            return $this->redirectToRoute('app_user_index');
        }

        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->redirectToRoute('app_home');
    }
}
