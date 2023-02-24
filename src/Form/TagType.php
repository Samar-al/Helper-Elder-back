<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                "label" => "Nom du service",
                "attr" => [
                    "placeholder" => "Nom du service"
                ]
            ])
            ->add('description', TextareaType::class,[
                "label" => "Description du service",
                "attr" => [
                    "placeholder" => "Description du service"
                ]
            ])
            ->add('logo', TextType::class,[
                "label" => "Logo",
                "attr" => [
                    "placeholder" => "Envoyer votre logo"
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
