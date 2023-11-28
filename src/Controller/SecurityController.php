<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function index(
        AuthenticationUtils $authenticationUtils
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logincheck', name: 'login_check', methods: ['POST', 'GET'])]
    public function check(): void
    {
    }

    #[Route('/logout', name: 'logout', methods: ['POST', 'GET'])]
    public function logout(): void
    {
    }
}
