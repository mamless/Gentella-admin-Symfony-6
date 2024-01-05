<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\ProfileRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add('username', TextType::class, ['label' => $translator->trans('backend.user.username')])
            ->add('email', EmailType::class)
            ->add('nomComplet', TextType::class, ['label' => $translator->trans('backend.user.name')]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $user = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$user || null === $user->getId()) {
                $form->add('justpassword', RepeatedType::class, [
                    'mapped' => false,
                    'invalid_message' => 'Les nouveaux mot de passe doivent être identique.',
                    'type' => PasswordType::class,
                    'constraints' => [
                        new NotBlank(['message' => 'Ne doit pas être vide']),
                    ],
                    'first_options' => ['label' => 'Nouveau mot de passe'],
                    'second_options' => ['label' => 'Confirmé mot de passe'],
                ])->add('initMdp', CheckboxType::class, [
                    'label' => 'Change de mot de passe aprés premiere connexion ?',
                    'attr' => ['class' => 'iCheck-helper'],
                    'required' => false,
                ])
                ;
            }
        });
        $builder ->add('role', ChoiceType::class, ['required' => false, 'label' => 'Role',
            "mapped"=>false,
            'choices' => [
                'Choisir un role' => '',
                'Super Admin' => 'ROLE_SUPERUSER',
            ],
        ])
            ->add('profile', EntityType::class, [
                'mapped' => true,
                'class' => Profile::class,
                'required' => false,
                'label' => 'Choisissez un profile *',
                'query_builder' => fn (ProfileRepository $profileRepository) => $profileRepository->createQueryBuilder('p')
                    ->andWhere('p.deleted = false')
                    ->andWhere('p.valid = true '),
                'constraints' => [
                    //new NotBlank(['message' => 'Ne doit pas être vide']),
                ],
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
