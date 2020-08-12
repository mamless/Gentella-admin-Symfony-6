<?php


namespace App\Controller;


use App\Entity\Categorie;
use App\Form\CategorieFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends BaseController
{

    private $categorieRepository;
        private $entityManager;

    public function __construct(CategorieRepository $categorieRepository,EntityManagerInterface $entityManager)
    {
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/admin/categorie",name="app_admin_categories")
     * @IsGranted("ROLE_WRITER")
     */
    public function users(){
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/categorie/categorie.html.twig",["categories"=>$categories]);
    }

    /**
     * @Route("/admin/categorie/new",name="app_admin_new_categorie")
     * @IsGranted("ROLE_WRITER")
     */
    public function newCategorie(Request $request){
        $form = $this->createForm(CategorieFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /** @var  Categorie $categorie */
            $categorie = $form->getData();
            $categorie->setValid(true)
                ->setDeleted(false);
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash("success","Categorie ajouté");
            return $this->redirectToRoute("app_admin_categories");

        }
        return $this->render("admin/categorie/categorieform.html.twig",["categorieForm"=>$form->createView()]);
    }

    /**
     * @Route("/admin/categorie/edit/{id}",name="app_admin_edit_categorie")
     * @IsGranted("ROLE_WRITER")
     */
    public function editCategorie(Categorie $categorie,Request $request){
        $form = $this->createForm(CategorieFormType::class,$categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $categorie->setValid(true)
                ->setDeleted(false);
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            $this->addFlash("success","Categorie ajouté");
            return $this->redirectToRoute("app_admin_categories");
        }
        return $this->render("admin/categorie/categorieform.html.twig",["categorieForm"=>$form->createView()]);
    }

    /**
     * @Route("/admin/categorie/changevalidite/{id}",name="app_admin_changevalidite_categorie",methods={"post"})
     * @IsGranted("ROLE_WRITER")
     */
    public function activate(Categorie $categorie){
        $categorie = $this->categorieRepository->changeValidite($categorie);
        return $this->json(["message"=>"success","value"=>$categorie->getValid()]);
    }

    /**
     * @Route("/admin/categorie/delete/{id}",name="app_admin_delete_categorie")
     * @IsGranted("ROLE_EDITORIAL")
     */
    public function delete(Categorie $categorie){
        $categorie = $this->categorieRepository->delete($categorie);
        return $this->json(["message"=>"success","value"=>$categorie->getDeleted()]);
    }

    /**
     * @Route("/admin/categorie/groupaction",name="app_admin_groupaction_categorie")
     * @IsGranted("ROLE_WRITER")
     */
    public function groupAction(Request $request){
        $action = $request->get("action");
        $ids = $request->get("ids");
        $categories = $this->categorieRepository->findBy(["id"=>$ids]);
        if ($action=="desactiver" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($categories as $categorie) {
                $categorie->setValid(false);
                $this->entityManager->persist($categorie);
            }
        }else if ($action=="activer" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($categories as $categorie) {
                $categorie->setValid(true);
                $this->entityManager->persist($categorie);
            }
        }else if ($action=="supprimer" && $this->isGranted("ROLE_EDITORIAL")){
            foreach ($categories as $categorie) {
                $categorie->setDeleted(true);
                $this->entityManager->persist($categorie);
            }
        }
        else{
            return $this->json(["message"=>"error"]);
        }
        $this->entityManager->flush();
        return $this->json(["message"=>"success","nb"=>count($categories)]);
    }

    //TODO: review role/access control for writers
    //TODO: Blog table add needed fields

}
