<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/main.html.twig');
    }

    //Routing defined for "/" in routes.yaml
    public function realIndex(): RedirectResponse
    {
        return $this->redirectToRoute("app_admin_index");
    }
}
