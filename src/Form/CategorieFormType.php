<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieFormType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('CategorieParente',EntityType::class,[
                "class"=>Categorie::class,
                "query_builder"=>function(CategorieRepository $categorieRepository){
                    return $categorieRepository->createQueryBuilder('c')
                        ->orderBy('c.libelle', 'ASC')
                        ->andWhere("c.deleted = false");
                },
                "required"=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
    // TODO: Find a way to remove the edited cat. from the select

}
