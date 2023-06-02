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
    #[IsGranted('ROLE_WRITER')]
    public function users(): Response
    {
        $categories = $this->categorieRepository->findAll();

        return $this->render('admin/categorie/categorie.html.twig', ['categories' => $categories]);
    }

    #[Route(path: '/admin/categorie/new', name: 'app_admin_new_categorie')]
    #[IsGranted('ROLE_WRITER')]
    public function newCategorie(Request $request)
    {
        $form = $this->createForm(CategorieFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Categorie $categorie */
            $categorie = $form->getData();
            $categorie->setValid(true)
                ->setDeleted(false);
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Categorie ajouté');

            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categorie/categorieform.html.twig', ['categorieForm' => $form]);
    }

    #[Route(path: '/admin/categorie/edit/{id}', name: 'app_admin_edit_categorie')]
    #[IsGranted('ROLE_WRITER')]
    public function editCategorie(Categorie $categorie, Request $request)
    {
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setValid(true)
                ->setDeleted(false);
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Categorie ajouté');

            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categorie/categorieform.html.twig', ['categorieForm' => $form]);
    }

    #[Route(path: '/admin/categorie/changevalidite/{id}', name: 'app_admin_changevalidite_categorie', methods: ['post'])]
    #[IsGranted('ROLE_WRITER')]
    public function activate(Categorie $categorie): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $categorie = $this->categorieRepository->changeValidite($categorie);

        return $this->json(['message' => 'success', 'value' => $categorie->getValid()]);
    }

    #[Route(path: '/admin/categorie/delete/{id}', name: 'app_admin_delete_categorie')]
    #[IsGranted('ROLE_WRITER')]
    public function delete(Categorie $categorie): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $categorie = $this->categorieRepository->delete($categorie);

        return $this->json(['message' => 'success', 'value' => $categorie->getDeleted()]);
    }

    #[Route(path: '/admin/categorie/groupaction', name: 'app_admin_groupaction_categorie')]
    #[IsGranted('ROLE_WRITER')]
    public function groupAction(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids');
        $categories = $this->categorieRepository->findBy(['id' => $ids]);
        if ($action == 'desactiver' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($categories as $categorie) {
                $categorie->setValid(false);
                $this->entityManager->persist($categorie);
            }
        } elseif ($action == 'activer' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($categories as $categorie) {
                $categorie->setValid(true);
                $this->entityManager->persist($categorie);
            }
        } elseif ($action == 'supprimer' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($categories as $categorie) {
                $categorie->setDeleted(true);
                $this->entityManager->persist($categorie);
            }
        } else {
            return $this->json(['message' => 'error']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'nb' => count($categories)]);
    }

    // TODO: review role/access control for writers
    // TODO: Blog table add needed fields
}
