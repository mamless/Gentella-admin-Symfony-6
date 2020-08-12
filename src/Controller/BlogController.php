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
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{

    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var HistoriqueRepository
     */
    private $historiqueRepository;
    /**
     * @var UploadHelper
     */
    private $uploadHelper;

    public function __construct(BlogPostRepository $blogPostRepository,EntityManagerInterface $entityManager, HistoriqueRepository $historiqueRepository, UploadHelper $uploadHelper)
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->entityManager = $entityManager;
        $this->historiqueRepository = $historiqueRepository;
        $this->uploadHelper = $uploadHelper;
    }


    /**
     * @Route("/admin/blog",name="app_admin_blogPosts")
     * @IsGranted("ROLE_WRITER")
     */
    public function blogPosts(){
        $blogPosts = $this->blogPostRepository->findAll();
        return $this->render("admin/blog/blog.html.twig",["blogPosts"=>$blogPosts]);
    }

    /**
     * @Route("/admin/blog/new",name="app_admin_new_blogPosts")
     * @IsGranted("ROLE_WRITER")
     */
    public function newBlogPost(Request $request){
        $historique = new Historique();
        $historique->setUser($this->getUser())
            ->setAction("Creation");
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostFormType::class,$blogPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /** @var  BlogPost $blogPost */
            $blogPost = $form->getData();
            $blogImage = $form["blogImage"]->getData();
            /* If want to add more specific img validation
             * if (!$this->uploadHelper->validateImg($blogImage)){

            }*/

            $image= $this->uploadHelper->uploadBlogImage($blogImage,$blogPost->getTitre());
            $blogPost->setDeleted(false)
                ->setCreator($this->getUser())
                ->setBlogImage($image->getFilename())
            ;
            if ($blogPost->getValid() == null){
                $blogPost->setValid(false);
            }
            $this->entityManager->persist($blogPost);
            $historique->setBlogPost($blogPost);
            $this->entityManager->persist($historique);
            $this->entityManager->flush();
            $this->addFlash("success","Blog ajouté");
            return $this->redirectToRoute("app_admin_blogPosts");

        }

        return $this->render("admin/blog/blogform.html.twig",["blogForm"=>$form->createView()]);
    }

    /**
     * @Route("/admin/blog/edit/{id}",name="app_admin_edit_blogPosts")
     * @IsGranted("ROLE_WRITER")
     */
    public function editBlogPost(BlogPost $blogPost,Request $request){
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
            ->setAction("Modification")
            ->setBlogPost($blogPost)
            ->setOldPost($oldPost);
        $this->entityManager->persist($historique);
        $form = $this->createForm(BlogPostEditFormType::class,$blogPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($form["blogImage"]->getData()){
                $blogImage = $form["blogImage"]->getData();
                $image= $this->uploadHelper->uploadBlogImage($blogImage,$blogPost->getTitre());
                $blogPost->setBlogImage($image->getFilename());
            }

            /* If want to add more specific img validation
             * if (!$this->uploadHelper->validateImg($blogImage)){

            }*/
            $this->entityManager->persist($blogPost);
            $this->entityManager->flush();
            $this->addFlash("success","Post modifié");
            return $this->redirectToRoute("app_admin_blogPosts");
        }

        return $this->render("admin/blog/blogform.html.twig",["blogForm"=>$form->createView()]);
    }

    /**
     * @Route("/admin/blog/changevalidite/{id}",name="app_admin_changevalidite_blogPost",methods={"post"})
     * @IsGranted("ROLE_EDITORIAL")
     */
    public function activate(BlogPost $blogPost){
        if ($blogPost->getValid())
            $action = "Desactiver";
        else
            $action = "Activer";
        $historique = new Historique();
        $historique->setAction("Desactiver")
            ->setBlogPost($blogPost)
            ->setUser($this->getUser())
            ->setAction($action);
        $blogPost = $this->blogPostRepository->changeValidite($blogPost);
        $this->entityManager->persist($historique);
        $this->entityManager->flush();
        return $this->json(["message"=>"success","value"=>$blogPost->getValid()]);
    }

    /**
     * @Route("/admin/blog/delete/{id}",name="app_admin_delete_blogPost")
     * @IsGranted("ROLE_EDITORIAL")
     */
    public function delete(BlogPost $blogPost){
        $historique = new Historique();
        $historique->setUser($this->getUser())
            ->setAction("Suppression")
            ->setBlogPost($blogPost);
        $blogPost->oldify();
        $blogPost = $this->blogPostRepository->delete($blogPost);

        $this->entityManager->persist($historique);
        $this->entityManager->flush();
        return $this->json(["message"=>"success","value"=>$blogPost->getDeleted()]);
    }

    /**
     * @Route("/admin/blog/groupaction",name="app_admin_groupaction_blogPost")
     * @IsGranted("ROLE_EDITORIAL ")
     */
    public function groupAction(Request $request){
        $action = $request->get("action");
        $ids = $request->get("ids");
        $historique = new Historique();
        $historique->setUser($this->getUser());
        $bloPosts = $this->blogPostRepository->findBy(["id"=>$ids]);
        if ($action=="desactiver" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($bloPosts as $blogPost) {
                $blogPost->setValid(false);
                $historique->setAction("Desactiver")
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        }else if ($action=="activer" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($bloPosts as $blogPost) {
                $blogPost->setValid(true);
                $historique->setAction("Activer")
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        }else if ($action=="supprimer" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($bloPosts as $blogPost) {
                $blogPost->setDeleted(true);
                $historique->setAction("Suppression")
                    ->setBlogPost($blogPost);
                $this->entityManager->persist($historique);
                $this->entityManager->persist($blogPost);
            }
        }
        else{
            return $this->json(["message"=>"error"]);
        }
        $this->entityManager->flush();
        return $this->json(["message"=>"success","nb"=>count($bloPosts)]);
    }

    /**
     * @Route("/admin/blog/historique/{id}",name="app_admin_historique_blogPost")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function historique(BlogPost $blogPost)
    {
        return $this->render("admin/blog/historique.html.twig",["blogPost"=>$blogPost]);
    }

    /**
     * @Route("/admin/blog/historique/undo/{id}",name="app_admin_historique_undo")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function undo(Historique $historique){
        $blogPost = $historique->getBlogPost();
        $newHistorique = new Historique();
        $newHistorique->setUser($this->getUser())
            ->setBlogPost($blogPost);
        if ($historique->getAction()=="Suppression"){
            $blogPost->setDeleted(false)
                ->setTitre(str_replace("-old-".$blogPost->getId(),"",$blogPost->getTitre()));
            $newHistorique->setAction("Annuler suppression");
        }elseif ($historique->getAction()=="Annuler suppression"){
            $blogPost->setDeleted(true)
                ->oldify();
            $newHistorique->setAction("Suppression");
        }elseif ($historique->getAction()=="Activer"){
            $blogPost->setValid(false);
            $newHistorique->setAction("Desactiver");
        }elseif ($historique->getAction()=="Desactiver"){
            $blogPost->setValid(true);
            $newHistorique->setAction("Activer");
        }elseif ($historique->getAction()=="Modification"){
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
                ->setAction("Modification");
            $blogPost->setContent($historique->getOldPost()->getContent())
                ->setCreatedAt($historique->getOldPost()->getCreatedAt())
                ->setAuthor($historique->getOldPost()->getAuthor())
                ->setTitre($historique->getOldPost()->getTitre())
                ->setCreator($historique->getOldPost()->getCreatedBy())
                ->setBlogImage($historique->getOldPost()->getImage())
                ->setPlubishedAt($historique->getOldPost()->getPublishedAt())
                ->setCategories($historique->getOldPost()->getCategories())
            ;
        }else{
            throw new NotFoundHttpException();
        }

        $this->entityManager->persist($newHistorique);
        $this->entityManager->persist($blogPost);
        $this->entityManager->flush();
        return $this->redirectToRoute("app_admin_historique_blogPost",["id"=>$historique->getBlogPost()->getId()]);
    }

    //TODO: add image upload support

    /**
     * @Route("/admin/blog/preview/{id}",name="app_admin_preview_blogpost")
     * @IsGranted("ROLE_ADMIN")
     */
    public function preview(BlogPost $blogPost){
        //TODO: preview page for admins
        return $this->json(["TODO"]);
    }

    /**
     * @Route("/admin/blog/historique/oldpost/{id}",name="app_admin_oldpost_blogPosts")
     * @IsGranted("ROLE_EDITORIAL")
     */
    public function oldPost(OldPost $oldPost){
        $form = $this->createForm(OldPostFormType::class,$oldPost);
        return $this->render("admin/blog/oldpostform.html.twig",["oldPostForm"=>$form->createView()]);
    }

    /**
     * @Route("/admin/blog/historiques",name="app_admin_allhistorique_blogPosts")
     * @IsGranted("ROLE_EDITORIAL")
     */
    public function historiques(){
        $historiques = $this->historiqueRepository->findAll() ;
        return $this->render("admin/blog/fullhistorique.html.twig",["historiques"=>$historiques]);
    }

}
