<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityPageController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig', [
            'pageTitle' => 'Sign In — Antigo UI/UX Advisory',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        // hasPendingSubmission defaults to false; can also be previewed via ?pending=1
        $hasPendingSubmission = $request->query->getBoolean('pending', false);

        return $this->render('security/register.html.twig', [
            'pageTitle' => 'Create Client Account — Antigo UI/UX Advisory',
            'hasPendingSubmission' => $hasPendingSubmission,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
    }
}
