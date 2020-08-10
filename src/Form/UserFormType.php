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
        $this->translator = $options['translator'];

        $builder
            ->add("username", TextType::class, ["label" => $this->translator->trans('backend.user.username')])
            ->add("email", EmailType::class)
            ->add("nomComplet", TextType::class, ["label" => $this->translator->trans('backend.user.name')])
            ->add("justpassword", TextType::class, [
                "label" => $this->translator->trans('backend.user.password'),
                "required" => true,
                "mapped" => false,
                "constraints" => [
                    new NotBlank(["message" => $this->translator->trans('backend.global.must_not_be_empty')])
                ]
            ])
            ->add("role", EntityType::class, [
                "mapped" => false,
                "class" => Role::class,
                "required" => true,
                "placeholder" => $this->translator->trans('backend.role.choice_role'),
                "constraints" => [
                    new NotBlank(["message" => $this->translator->trans('backend.global.must_not_be_empty')]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
