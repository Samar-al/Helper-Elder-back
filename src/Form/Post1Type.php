<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Post1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                "label" => "Titre de ton annonce",
                "attr" => [
                    "placeholder" => "Titre de ton annonce"
                ]
            ])
            ->add('content', TextareaType::class,[
                "label" => "Contenu de ton annonce",
                "attr" => [
                    "placeholder" => "Contenu"
                ],
                "help" => "maximum 500"
            ])
            ->add('hourlyRate', IntegerType::class,[
                "label" => "Taux horaire *",
                "attr" => [
                    "placeholder" => "Combien souhaitez-vous etre payé de l'heure"
                ],
                "help" => "* Ton taux horaire en euros"
            ])
            ->add('workType',ChoiceType::class,[
                "choices" => [
                    "Ponctuel" => true,
                    "Régulier" => false,
                ],
                "label" => "Ponctuel ou regulier", 
                ])
            ->add('postalCode',  TextType::class)
            ->add('radius', IntegerType::class)
            ->add('user')
            ->add('tag', EntityType::class, [
                "class"=> Tag::class,
                'label'=> "Service(s) *",
                "multiple" => true,
                "expanded" => true,
                "help" => "* Vous pouvez choisir plusieurs tag"
            ] )
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
