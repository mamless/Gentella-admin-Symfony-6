<?php


namespace App\Controller;


use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class admincontroller extends AbstractController
{

    /**
     * @Route("/admin",name="app_admin_index")
     */
    public function index(){
        return $this->render("admin/main.html.twig");
    }

}