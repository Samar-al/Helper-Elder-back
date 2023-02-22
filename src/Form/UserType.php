<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Form builder
        $builder
            ->add('email',EmailType::class,[
                "label" => "Adresse email",
                "attr" => [
                    "placeholder" => "Adresse email"
                ]
            ])
            ->add('firstname',TextType::class,[ 
                "label" => "Votre Prénom",
                "attr" => [
                "placeholder" => "Votre Prénom"
                ]
            ])
            ->add('lastname',TextType::class,[ 
                "label" => "Votre Nom",
                "attr" => [
                "placeholder" => "Votre Nom"
                ]
            ])
            ->add('postalCode',TextType::class,[ 
                "label" => "Votre code postal",
                "attr" => [
                "placeholder" => "Votre code postal"
                ]
            ])
            ->add('description',TextareaType::class,[ 
                "label" => "Présentation",
                "attr" => [
                "placeholder" => "Votre présentation"
                ]
            ])
            ->add('type',ChoiceType::class,[
                "choices"=>[
                    "1" => 1,
                    "2" => 2,
                ]
            ])  ;
/*            if(!$options["edit"]){
                $builder
                ->add('password', RepeatedType::class, [
                    "type" => PasswordType::class,
                    "invalid_message" => "Les deux mots de passes doivent être identiques",
                    "first_options" => [
                        "label" => "Le mot de passe",
                        "attr" => [
                            "placeholder" => "Le mot de passe"
                        ]
                    ],
                    "second_options" => [
                        "label" => "Répétez le mot de passe",
                        "attr" => [
                            "placeholder" => "Répétez le mot de passe"
                        ]
                    ]
                ]);
            }
        ; */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
