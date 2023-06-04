<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePwsdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add('justpassword', PasswordType::class, [
                'label' => $translator->trans('backend.user.current_password'),
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => $translator->trans('backend.global.must_not_be_empty')]),
                    new UserPassword(['message' => $translator->trans('backend.user.remember_password')]),
                ],
            ])
            ->add('newpassword', RepeatedType::class, [
                'mapped' => false,
                'invalid_message' => $translator->trans('backend.user.new_passwod_must_be'),
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(['message' => $translator->trans('backend.global.must_not_be_empty')]),
                ],
                'first_options' => ['label' => $translator->trans('backend.user.new_password')],
                'second_options' => ['label' => $translator->trans('backend.user.confirm_password')],
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
