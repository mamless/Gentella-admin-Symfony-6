<?php

namespace App\Form;

use App\Entity\OldPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OldPostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt',null,[
                "attr"=>[
                    "readonly"=>true
                ],
                "widget"=>"single_text"
            ])
            ->add('publishedAt',null,[
                "attr"=>[
                    "readonly"=>true
                ],
                "widget"=>"single_text"
            ])
            ->add('titre',null,[
                "attr"=>[
                    "readonly"=>true
                ]
            ])
            ->add('content',null,[
                "attr"=>[
                    "readonly"=>true,
                    "class"=>"summernote"
                ]
            ])
            ->add('image',null,[
                "attr"=>[
                    "readonly"=>true
                ]
            ])
            ->add('categories',null,[
                "attr"=>[
                    "disabled"=>""
                ]
            ])
            ->add('createdBy',null,[
                "attr"=>[
                    "disabled"=>""
                ]
            ])
            ->add('author',null,[
                "attr"=>[
                    "disabled"=>""
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OldPost::class,
        ]);
    }
}
