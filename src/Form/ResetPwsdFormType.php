<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPwsdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newpassword', RepeatedType::class, [
                'mapped' => false,
                'invalid_message' => 'Les nouveaux mot de passe doivent être identique.',
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(['message' => 'Ne doit pas être vide']),
                    //new Regex("'/^.{5,10}$/'","Il faut au moins 5 caractères")
                ],
                'first_options' => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmé mot de passe'],
            ])->add('initMdp', CheckboxType::class, [
                'label' => 'Change de mot de passe aprés premiere connexion ?',
                'attr' => ['class' => 'iCheck-helper',"checked"=>"checked"],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}