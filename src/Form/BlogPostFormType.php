<?php

namespace App\Form;

use App\Entity\BlogPost;
use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;


class BlogPostFormType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;


    /**
     * BlogPostFormType constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('blogImage', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
                "attr"=>["placeholder"=>"Choisir une image"],
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
            ->add('categories',EntityType::class,[
                "multiple"=>true,
                "class"=>Categorie::class,
                "query_builder"=>function(CategorieRepository $categorieRepository){
                    return $categorieRepository->createQueryBuilder('c')
                        ->orderBy('c.libelle', 'ASC')
                        ->andWhere("c.deleted = false");
                },
                "required"=>true,
                "attr"=>["data-live-search"=>"true","data-size"=>"3"]
            ])
            ->add('author',EntityType::class,[
                "class"=>User::class,
                "query_builder"=>function(UserRepository $userRepository){
                    return $userRepository->createQueryBuilder('u')
                        ->andWhere("u.deleted = false")
                        ->andWhere("u.admin = true");
                },
                "required"=>true,
                "attr"=>["data-live-search"=>"true","data-size"=>"3"],
                "label"=>"Auteur",
                "placeholder"=>"Choisissez un auteur"
            ])
            ->add('plubishedAt',null,[
                "widget"=>"single_text"
            ])
            ->add('content',TextareaType::class,[
                "label"=>"Contenu",
                "attr"=>["class"=>"summernote","rows"=>"6"]
            ]);
            if($this->security->isGranted("ROLE_EDITORIAL")){
                $builder->add("valid",CheckboxType::class,[
                    "label"=>"Activé ?",
                    "attr"=>["class"=>"iCheck-helper"],
                    "required"=>false
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
