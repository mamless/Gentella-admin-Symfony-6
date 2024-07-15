<?php


namespace App\Controller;


use App\Entity\AppFAQ;
use App\Form\AppFAQFormType;
use App\Repository\AppFAQRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AppFAQController extends BaseController
{

    /**
     * AppFAQController constructor.
     */
    public function __construct( private AppFAQRepository $appFAQRepository , private EntityManagerInterface $entityManager)
    {
    }


    #[Route(path: '/admin/parametre/FAQ', name: 'app_admin_faqs')]
    #[IsGranted('ROLE_LIST_FAQ')]
    public function faqs(): Response
    {
        $faqs = $this->appFAQRepository->findBy(["deleted"=>false]);
        return $this->render("admin/params/faq/faq.html.twig",["faqs"=>$faqs]);
    }


    #[Route(path: '/admin/parametre/FAQ/new', name: 'app_admin_new_faq')]
    #[IsGranted('ROLE_ADD_FAQ')]
    public function newFAQ(Request $request): Response
    {
        $form = $this->createForm(AppFAQFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /** @var  AppFAQ $appFAQ */
            $appFAQ = $form->getData();
            $appFAQ->setValid(true)
                ->setDeleted(false)
                ->setCreatedBy($this->getUser());
            $this->entityManager->persist($appFAQ);
            $this->entityManager->flush();
            $this->addFlash("success","FAQ ajouté");
            return $this->redirectToRoute("app_admin_faqs");

        }
        return $this->render("admin/params/faq/faqform.html.twig",["Form"=>$form->createView()]);
    }

    #[Route(path: '/admin/user/parametre/FAQ/{id}', name: 'app_admin_edit_faq')]
    #[IsGranted('ROLE_EDIT_FAQ')]
    public function editFAQ(AppFAQ $appFAQ,Request $request): Response
    {
        $this->errorNotFound($appFAQ);
        $form = $this->createForm(AppFAQFormType::class,$appFAQ);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $appFAQ->setModfiedBy($this->getUser());
            $this->entityManager->persist($appFAQ);
            $this->entityManager->flush();
            $this->addFlash("success","FAQ modifié");
            return $this->redirectToRoute("app_admin_faqs");

        }
        return $this->render("admin/params/faq/faqform.html.twig",["Form"=>$form->createView()]);
    }


    #[Route(path: '/admin/parametre/FAQ/changevalidite/{id}', name: 'app_admin_changevalidite_faq',methods: "POST")]
    #[IsGranted('ROLE_ENABLE_FAQ')]
    public function activate(AppFAQ $appFAQ): JsonResponse
    {
        $this->errorNotFound($appFAQ);
        $appFAQ->setValid(!$appFAQ->getValid())
            ->setModfiedBy($this->getUser());
        $this->entityManager->persist($appFAQ);
        $this->entityManager->flush();
        return $this->json(["message"=>"success","value"=>$appFAQ->getValid()]);
    }


    #[Route(path: '/admin/parametre/FAQ/delete/{id}', name: 'app_admin_delete_faq')]
    #[IsGranted('ROLE_DELETE_FAQ')]
    public function delete(AppFAQ $appFAQ): JsonResponse
    {
        $this->errorNotFound($appFAQ);
        $appFAQ->setDeleted(true)
            ->setModfiedBy($this->getUser());
        $this->entityManager->persist($appFAQ);
        $this->entityManager->flush();
        return $this->json(["message"=>"success","value"=>$appFAQ->getDeleted()]);
    }


    #[Route(path: '/admin/parametre/FAQ/groupaction', name: 'app_admin_groupaction_faq')]
    #[IsGranted('ROLE_AG_FAQ')]
    public function groupAction(Request $request): JsonResponse
    {
        $action = $request->get("action");
        $ids = $request->get("ids");
        $faqs = $this->appFAQRepository->findBy(["id"=>$ids]);
        if ($action=="desactiver" && $this->isGranted('ROLE_AG_ENABLE_FAQ')){
            foreach ($faqs as $faq) {
                $this->errorNotFound($faq);
                $faq->setValid(false)
                    ->setModfiedBy($this->getUser());
                $this->entityManager->persist($faq);
            }
        }else if ($action=="activer" && $this->isGranted('ROLE_AG_ENABLE_FAQ')){
            foreach ($faqs as $faq) {
                $this->errorNotFound($faq);
                $faq->setValid(true)
                    ->setModfiedBy($this->getUser());
                $this->entityManager->persist($faq);
            }
        }else if ($action=="supprimer" && $this->isGranted('ROLE_AG_DELETE_FAQ')){
            foreach ($faqs as $faq) {
                $this->errorNotFound($faq);
                $faq->setDeleted(true)
                    ->setModfiedBy($this->getUser());
                $this->entityManager->persist($faq);
            }
        }
        else{
            return $this->json(["message"=>"error"]);
        }
        $this->entityManager->flush();
        return $this->json(["message"=>"success","nb"=>count($faqs)]);
    }

    public function errorNotFound(AppFAQ $appFAQ)
    {
        if ( $appFAQ->isDeleted()){
            throw $this->createNotFoundException("appFAQ not found");
        }
    }
}
