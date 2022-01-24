<?php


namespace App\Form;


use App\Entity\Permission;
use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RoleFormType extends AbstractType
{
    /**
     * @var mixed
     */
    private $translator;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translator = $options['translator'];

        $builder
            ->add("roleName", TextType::class, ["label" => $this->translator->trans('backend.role.name')])
            ->add("libelle", TextType::class, ["label" => $this->translator->trans('backend.role.libelle')])
            ->add("permission", EntityType::class, [
                'class' => Permission::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
                'multiple'=>true,
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');
        $resolver->setDefaults([
            'data_class' => Role::class
        ]);
    }
}
