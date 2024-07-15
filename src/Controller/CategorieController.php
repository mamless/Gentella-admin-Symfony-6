<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategorieController extends BaseController
{
    public function __construct(private CategorieRepository $categorieRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/admin/categorie', name: 'app_admin_categories')]
    #[IsGranted('ROLE_LIST_CATEGORIE')]
    public function categories(): Response
    {
        $categories = $this->categorieRepository->findBy(["deleted"=>false]);
        return $this->render('admin/categorie/categorie.html.twig', ['categories' => $categories]);
    }

    #[Route(path: '/admin/categorie/new', name: 'app_admin_new_categorie')]
    #[IsGranted('ROLE_ADD_CATEGORIE')]
    public function newCategorie(Request $request)
    {

        $form = $this->createForm(CategorieFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Categorie $categorie */
            $categorie = $form->getData();
            $categorie->setValid(true)
                ->setDeleted(false)
                ->setCreatedBy($this->getUser());
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Categorie ajouté');

            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categorie/categorieform.html.twig', ['categorieForm' => $form]);
    }

    #[Route(path: '/admin/categorie/edit/{id}', name: 'app_admin_edit_categorie')]
    #[IsGranted('ROLE_EDIT_CATEGORIE')]
    public function editCategorie(Categorie $categorie, Request $request)
    {
        $this->errorNotFound($categorie);
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setValid(true)
                ->setDeleted(false)
                ->setModfiedBy($this->getUser());
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Categorie ajouté');

            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categorie/categorieform.html.twig', ['categorieForm' => $form]);
    }

    #[Route(path: '/admin/categorie/changevalidite/{id}', name: 'app_admin_changevalidite_categorie', methods: ['post'])]
    #[IsGranted('ROLE_ENABLE_CATEGORIE')]
    public function activate(Categorie $categorie): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $this->errorNotFound($categorie);
        $categorie->setModfiedBy($this->getUser());
        $categorie = $this->categorieRepository->changeValidite($categorie);
        return $this->json(['message' => 'success', 'value' => $categorie->getValid()]);
    }

    #[Route(path: '/admin/categorie/delete/{id}', name: 'app_admin_delete_categorie')]
    #[IsGranted('ROLE_ENABLE_CATEGORIE')]
    public function delete(Categorie $categorie): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $this->errorNotFound($categorie);
        if ($categorie->getCategories()->count()==0){
            $categorie->setModfiedBy($this->getUser());
            $categorie = $this->categorieRepository->delete($categorie);
        }else{
            return $this->json(['message' => 'warning', 'value' => $categorie->getDeleted()]);
        }
        return $this->json(['message' => 'success', 'value' => $categorie->getDeleted()]);
    }

    #[Route(path: '/admin/categorie/groupaction', name: 'app_admin_groupaction_categorie')]
    #[IsGranted('ROLE_AG_CATEGORIE')]
    public function groupAction(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids');
        $categories = $this->categorieRepository->findBy(['id' => $ids]);
        if ($action == 'desactiver' && $this->isGranted('ROLE_AG_ENABLE_CATEGORIE')) {
            foreach ($categories as $categorie) {
                $this->errorNotFound($categorie);
                $categorie->setModfiedBy($this->getUser());
                $categorie->setValid(false);
                $this->entityManager->persist($categorie);
            }
        } elseif ($action == 'activer' && $this->isGranted('ROLE_AG_ENABLE_CATEGORIE')) {
            foreach ($categories as $categorie) {
                $this->errorNotFound($categorie);
                $categorie->setModfiedBy($this->getUser());
                $categorie->setValid(true);
                $this->entityManager->persist($categorie);
            }
        } elseif ($action == 'supprimer' && $this->isGranted('ROLE_AG_DELETE_CATEGORIE')) {
            foreach ($categories as $categorie) {
                $this->errorNotFound($categorie);
                //Ignores cats with sub cats
                if ($categorie->getCategories()->count()==0) {
                    $categorie->setModfiedBy($this->getUser());
                    $categorie->setDeleted(true);
                    $this->entityManager->persist($categorie);
                }
            }
        } else {
            return $this->json(['message' => 'error']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'nb' => count($categories)]);
    }

    public function errorNotFound(Categorie $categorie)
    {
        if ($categorie->isDeleted()){
            throw $this->createNotFoundException("Categorie not found");
        }
    }

    // TODO: Blog table add needed fields
}
