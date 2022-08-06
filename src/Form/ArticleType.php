<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        if ($options['add'] == true) {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir le nom du produit'
                ]

            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => "Saisir le contenu de l'article"
                ]
            ])

            ->add('slug', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir un slug pour cet article'
                ]

            ])

            ->add('cover', FileType::class, [
                'required' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpg",
                            "image/jpeg"

                        ],
                        'mimeTypesMessage' => 'Extensions autorisées: PNG, JPG, JPEG'
                    ])
                ]
            ])
            
            ->add('Enregistrer', SubmitType::class);
        ;
                    
    }elseif ($options['edit'] == true) {
        $builder
        ->add('title', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisir le nom du produit'
            ]

        ])
        ->add('content', TextareaType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => "Saisir le contenu de l'article"
            ]

        ])
        ->add('slug', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => 'Saisir un slug pour cet article'
            ]

        ])
        ->add('editCover', FileType::class, [
            'required' => false,
            'label' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        "image/png",
                        "image/jpg",
                        "image/jpeg"

                    ],
                    'mimeTypesMessage' => 'Extensions autorisées: PNG, JPG, JPEG'
                ])
            ]
        ])
        ->add('Modifier', SubmitType::class);
    ;
                }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'add' => false,
            'edit' => false
        ]);
    }
}
