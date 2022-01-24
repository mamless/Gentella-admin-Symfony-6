<?php


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        dump('here');
        exit;
    }
}