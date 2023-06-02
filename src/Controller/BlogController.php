<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Historique;
use App\Entity\OldPost;
use App\Form\BlogPostEditFormType;
use App\Form\BlogPostFormType;
use App\Form\OldPostFormType;
use App\Repository\BlogPostRepository;
use App\Repository\HistoriqueRepository;
use App\Services\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BlogController extends BaseController
{
    public function __construct(private BlogPostRepository $blogPostRepository, private EntityManagerInterface $entityManager, private HistoriqueRepository $historiqueRepository, private UploadHelper $uploadHelper)
    {
    }

    #[Route(path: '/admin/blog', name: 'app_admin_blogPosts')]
    #[IsGranted('ROLE_WRITER')]
    public function blogPosts(): Response
    {
        $blogPosts = $this->blogPostRepository->findAll();

        return $this->render('admin/blog/blog.html.twig', ['blogPosts' => $blogPosts]);
    }

    #[Route(path: '/admin/blog/new', name: 'app_admin_new_blogPosts')]
    #[IsGranted('ROLE_WRITER')]
    public function newBlogPost(Request $request)
    {
        $historique = new Historique();
        $historique->setUser($this->getUser())
            ->setAction('Creation');
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostFormType::class, $blogPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPost $blogPost */
            $blogPost = $form->getData();
            $blogImage = $form['blogImage']->getData();
            /* If want to add more specific img validation
             * if (!$this->uploadHelper->validateImg($blogImage)){

            }*/

            $image = $this->uploadHelper->uploadBlogImage($blogImage, $blogPost->getTitre());
            $blogPost->setDeleted(false)
                ->setCreator($this->getUser())
                ->setBlogImage($image->getFilename())
            ;
            if ($blogPost->getValid() == null) {
                $blogPost->setValid(false);
            }
            $this->entityManager->persist($blogPost);
            $historique->setBlogPost($blogPost);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();
            $this->addFlash('success', 'Blog ajouté');

            return $this->redirectToRoute('app_admin_blogPosts');
        }

        return $this->render('admin/blog/blogform.html.twig', ['blogForm' => $form]);
    }

    #[Route(path: '/admin/blog/edit/{id}', name: 'app_admin_edit_blogPosts')]
    #[IsGranted('ROLE_WRITER')]
    public function editBlogPost(BlogPost $blogPost, Request $request)
    {
        $oldPost = new OldPost();
        $oldPost->setContent($blogPost->getContent())
            ->setTitre($blogPost->getTitre())
            ->setAuthor($blogPost->getAuthor())
            ->setCreatedAt($blogPost->getCreatedAt())
            ->setCreatedBy($blogPost->getCreator())
            ->setPublishedAt($blogPost->getPlubishedAt())
            ->setImage($blogPost->getBlogImage())
            ->setCategories($blogPost->getCategories());
        $this->entityManager->persist($oldPost);
        $historique = new Historique();
        $historique->setUser($this->getUser())
            ->setAction('Modification')
            ->setBlogPost($blogPost)
            ->setOldPost($oldPost);
        $this->entityManager->persist($historique);
        $form = $this->createForm(BlogPostEditFormType::class, $blogPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['blogImage']->getData()) {
                $blogImage = $form['blogImage']->getData();
                $image = $this->uploadHelper->uploadBlogImage($blogImage, $blogPost->getTitre());
                $blogPost->setBlogImage($image->getFilename());
            }

            /* If want to add more specific img validation
             * if (!$this->uploadHelper->validateImg($blogImage)){

            }*/
            $this->entityManager->persist($blogPost);
            $this->entityManager->flush();
            $this->addFlash('success', 'Post modifié');

            return $this->redirectToRoute('app_admin_blogPosts');
        }

        return $this->render('admin/blog/blogform.html.twig', ['blogForm' => $form]);
    }

    #[Route(path: '/admin/blog/changevalidite/{id}', name: 'app_admin_changevalidite_blogPost', methods: ['post'])]
    #[IsGranted('ROLE_EDITORIAL')]
    public function activate(BlogPost $blogPost): JsonResponse
    {
        if ($blogPost->getValid()) {
            $action = 'Desactiver';
        } else {
            $action = 'Activer';
        }
        $historique = new Historique();
        $historique->setAction('Desactiver')
            ->setBlogPost($blogPost)
            ->setUser($this->getUser())
            ->setAction($action);
        $blogPost = $this->blogPostRepository->changeValidite($blogPost);
        $this->entityManager->persist($historique);
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'value' => $blogPost->getValid()]);
    }

    #[Route(path: '/admin/blog/delete/{id}', name: 'app_admin_delete_blogPost')]
    #[IsGranted('ROLE_WRITER')]
    public function delete(BlogPost $blogPost): JsonResponse
    {
        $historique = new Historique();
        $historique->setUser($this->getUser())
            ->setAction('Suppression')
            ->setBlogPost($blogPost);
        $blogPost->oldify();
        $blogPost = $this->blogPostRepository->delete($blogPost);

        $this->entityManager->persist($historique);
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'value' => $blogPost->getDeleted()]);
    }

    #[Route(path: '/admin/blog/groupaction', name: 'app_admin_groupaction_blogPost')]
    #[IsGranted('ROLE_EDITORIAL ')]
    public function groupAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids');
        $historique = new Historique();
        $historique->setUser($this->getUser());
        $bloPosts = $this->blogPostRepository->findBy(['id' => $ids]);
        if ($action == 'desactiver' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($bloPosts as $blogPost) {
                $blogPost->setValid(false);
                $historique->setAction('Desactiver')
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        } elseif ($action == 'activer' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($bloPosts as $blogPost) {
                $blogPost->setValid(true);
                $historique->setAction('Activer')
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        } elseif ($action == 'supprimer' && $this->isGranted('ROLE_EDITORIAL')) {
            foreach ($bloPosts as $blogPost) {
                $blogPost->setDeleted(true);
                $historique->setAction('Suppression')
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        } else {
            return $this->json(['message' => 'error']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'nb' => count($bloPosts)]);
    }

    #[Route(path: '/admin/blog/historique/{id}', name: 'app_admin_historique_blogPost')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function historique(BlogPost $blogPost): Response
    {
        return $this->render('admin/blog/historique.html.twig', ['blogPost' => $blogPost]);
    }

    #[Route(path: '/admin/blog/historique/undo/{id}', name: 'app_admin_historique_undo')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function undo(Historique $historique): RedirectResponse
    {
        $blogPost = $historique->getBlogPost();
        $newHistorique = new Historique();
        $newHistorique->setUser($this->getUser())
            ->setBlogPost($blogPost);
        if ($historique->getAction() == 'Suppression') {
            $blogPost->setDeleted(false)
                ->setTitre(str_replace('-old-'.$blogPost->getId(), '', $blogPost->getTitre()));
            $newHistorique->setAction('Annuler suppression');
        } elseif ($historique->getAction() == 'Annuler suppression') {
            $blogPost->setDeleted(true)
                ->oldify();
            $newHistorique->setAction('Suppression');
        } elseif ($historique->getAction() == 'Activer') {
            $blogPost->setValid(false);
            $newHistorique->setAction('Desactiver');
        } elseif ($historique->getAction() == 'Desactiver') {
            $blogPost->setValid(true);
            $newHistorique->setAction('Activer');
        } elseif ($historique->getAction() == 'Modification') {
            $oldPost = new OldPost();
            $oldPost->setContent($blogPost->getContent())
                ->setTitre($blogPost->getTitre())
                ->setAuthor($blogPost->getAuthor())
                ->setCreatedAt($blogPost->getCreatedAt())
                ->setCreatedBy($blogPost->getCreator())
                ->setPublishedAt($blogPost->getPlubishedAt())
                ->setImage($blogPost->getBlogImage())
                ->setCategories($blogPost->getCategories());
            $this->entityManager->persist($oldPost);
            $newHistorique->setOldPost($oldPost)
                ->setAction('Modification');
            $blogPost->setContent($historique->getOldPost()->getContent())
                ->setCreatedAt($historique->getOldPost()->getCreatedAt())
                ->setAuthor($historique->getOldPost()->getAuthor())
                ->setTitre($historique->getOldPost()->getTitre())
                ->setCreator($historique->getOldPost()->getCreatedBy())
                ->setBlogImage($historique->getOldPost()->getImage())
                ->setPlubishedAt($historique->getOldPost()->getPublishedAt())
                ->setCategories($historique->getOldPost()->getCategories())
            ;
        } else {
            throw new NotFoundHttpException();
        }

        $this->entityManager->persist($newHistorique);
        $this->entityManager->persist($blogPost);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_historique_blogPost', ['id' => $historique->getBlogPost()->getId()]);
    }

    // TODO: add image upload support
    #[Route(path: '/admin/blog/preview/{id}', name: 'app_admin_preview_blogpost')]
    #[IsGranted('ROLE_ADMIN')]
    public function preview(BlogPost $blogPost): JsonResponse
    {
        // TODO: preview page for admins
        return $this->json(['TODO']);
    }

    #[Route(path: '/admin/blog/historique/oldpost/{id}', name: 'app_admin_oldpost_blogPosts')]
    #[IsGranted('ROLE_WRITER')]
    public function oldPost(OldPost $oldPost): Response
    {
        $form = $this->createForm(OldPostFormType::class, $oldPost);

        return $this->render('admin/blog/oldpostform.html.twig', ['oldPostForm' => $form]);
    }

    #[Route(path: '/admin/blog/historiques', name: 'app_admin_allhistorique_blogPosts')]
    #[IsGranted('ROLE_WRITER')]
    public function historiques(): Response
    {
        $historiques = $this->historiqueRepository->findAll();

        return $this->render('admin/blog/fullhistorique.html.twig', ['historiques' => $historiques]);
    }
}
