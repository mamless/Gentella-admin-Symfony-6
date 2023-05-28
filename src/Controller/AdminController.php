<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin",name="app_admin_index")
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('admin/main.html.twig');
    }
}
