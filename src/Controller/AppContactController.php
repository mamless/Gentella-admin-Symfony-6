<?php


namespace App\Controller;


use App\Entity\AppContact;
use App\Repository\AppContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AppContactController extends BaseController
{

    public function __construct(private EntityManagerInterface $entityManager,private AppContactRepository $appContactRepository)
    {
    }


    #[Route(path: '/admin/contact', name: 'app_admin_contacts')]
    #[IsGranted('ROLE_WRITER')]
    public function contacts(): Response
    {
        $contacts = $this->appContactRepository->findBy(["deleted"=>false],["sendedAt"=>"ASC"]);
        return $this->render("admin/app_contact/inbox.html.twig",["contacts"=>$contacts]);
    }


    #[Route(path: '/admin/contact/read/{id}', name: 'app_admin_read_contact')]
    #[IsGranted('ROLE_WRITER')]
    public function readContact(AppContact $appContact): JsonResponse
    {
        $appContact->setIsRead(true);
        $this->entityManager->persist($appContact);
        $this->entityManager->flush();
        return $this->json($appContact,200);
    }


    #[Route(path: '/admin/contact/delete/{id}', name: 'app_admin_delete_contact')]
    #[IsGranted('ROLE_WRITER')]
    public function deleteContact(AppContact $appContact): JsonResponse
    {
        $appContact->setDeleted(true);
        return $this->json(["deleted"=>true],200);
    }


}
