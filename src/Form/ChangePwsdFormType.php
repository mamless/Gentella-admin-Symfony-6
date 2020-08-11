<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePwsdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translator = $options['translator'];

        $builder
            ->add("justpassword", PasswordType::class, [
                "label" => $this->translator->trans('backend.user.current_password'),
                "required" => true,
                "mapped" => false,
                "constraints" => [
                    new NotBlank(["message" => $this->translator->trans('backend.global.must_not_be_empty')]),
                    new UserPassword(["message" => $this->translator->trans('backend.user.remember_password')])
                ]
            ])
            ->add("newpassword", RepeatedType::class, [
                "mapped" => false,
                'invalid_message' => $this->translator->trans('backend.user.new_passwod_must_be'),
                "type" => PasswordType::class,
                "constraints" => [
                    new NotBlank(["message" => $this->translator->trans('backend.global.must_not_be_empty')])
                ],
                "first_options"  => ['label' => $this->translator->trans('backend.user.new_password')],
                "second_options"  => ['label' => $this->translator->trans('backend.user.confirm_password')]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');

        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
