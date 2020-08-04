<?php


namespace App\Form;


use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username",TextType::class,["label"=>"Nom d'utilisateur"])
            ->add("email",EmailType::class)
            ->add("nomComplet",TextType::class)
            ->add("justpassword",TextType::class,[
                "label"=>"Mot de passe",
                "required"=>true,
                "mapped"=>false,
                "constraints"=>[
                    new NotBlank(["message"=>"Ne doit pas être vide"])
                ]
            ])
            ->add("role",EntityType::class,[
                "mapped"=>false,
                "class"=>Role::class,
                "required"=>true,
                "placeholder"=>"Choisissez un role",
                "constraints"=>[
                    new NotBlank(["message"=>"Ne doit pas être vide"]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>User::class
        ]);
    }



}
