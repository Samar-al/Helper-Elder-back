<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                "label" => "Email",
                "attr" => [
                    "placeholder" => "email"
                ]
            ])
            ->add('roles',ChoiceType::class,[
                "choices"=>[
                    "Admin" => "ROLE_ADMIN",
                    "User" => "ROLE_USER"
                ],
                "expanded" => true,
                "multiple" => true
            ])
            ->add('password',RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Les deux mots de passes doivent être identiques",
                "first_options" => [
                    "label" => "Mot de passe",
                    "attr" => [
                        "placeholder" => "mot de passe"
                    ]
                ],
                "second_options" => [
                    "label" => "Répétez le mot de passe",
                    "attr" => [
                        "placeholder" => "Répétez le mot de passe"
                    ]
                ]
            ])
            ->add('firstname', TextType::class,[
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "Prénom"
                ]
            ])
            ->add('lastname', TextType::class,[
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "Nom"
                ]
            ])
            ->add('birthdate', DateType::class,[
                "label" => "Date de naissance",
                "attr" => [
                    "placeholder" => "Date de naissance"
                ]
            ])
            ->add('gender', ChoiceType::class,[
                "label" => "Sexe",
                "choices" => [
                    "masculin" => 1,
                    "feminin" => 2,    
                ]
               
            ])
            ->add('postalCode', TextType::class,[
                "label" => "Code postal",
                "attr" => [
                    "placeholder" => "Code postal"
                ]
            ])
            ->add('description', TextareaType::class,[
                "label" => "Description",
                "attr" => [
                    "placeholder" => "Présentez-vous"
                ]
            ])
            ->add('type', ChoiceType::class,[
                "choices" => [
                    "Helper" => 1,
                    "Elder" => 2,
                    "admin" => 3,
                ]
            ])
            ->add('picture', UrlType::class,[
                "label" => "Votre image *",
                "attr" => [
                    "placeholder" => "Votre image"
                ],
                "help"=> "* L'url d'une image"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
