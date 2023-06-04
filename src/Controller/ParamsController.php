<?php


namespace App\Controller;


use App\Entity\Params;
use App\Form\ParamsFormType;
use App\Repository\ParamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParamsController extends BaseController
{


    public function __construct(private ParamsRepository $paramsRepository,private EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/admin/parametre/generale', name: 'app_admin_parametre_generale')]
    #[IsGranted('ROLE_ADMINISTRATOR')]
    public function parametres(Request $request): Response
    {
        $params = $this->paramsRepository->findOneBy(["realId"=>1]);
        if ($params == null){
            $params = new Params();
            $params->setRealId(1)
                ->init();
            $this->entityManager->persist($params);
            $this->entityManager->flush();
        }
        $form = $this->createForm(ParamsFormType::class,$params);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($params);
            $this->entityManager->flush();
            $this->addFlash("success","Parametre modifié");
            $form = $this->createForm(ParamsFormType::class,$params);
        }
        return $this->render("admin/params/generale/generale.html.twig",["Form"=>$form->createView()]);
    }
}
