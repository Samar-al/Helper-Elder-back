<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                "label" => "L'email",
                "attr" => [
                    "placeholder" => "L'email"
                ]
            ])
            ->add('roles',ChoiceType::class,[
                "choices"=>[
                    "user" => "ROLE_MANAGER",
                    "Admin" => "ROLE_ADMIN"
                ],
                "expanded" => true,
                "multiple" => true
            ])
            ->add('password',RepeatedType::class, [
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
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('birthdate')
            ->add('gender', ChoiceType::class,[
                "choices" => [
                    "masculin" => 1,
                    "feminin" => 2,
                    
                ]
               
            ])
            ->add('postalCode', TextType::class)
            ->add('description', TextareaType::class,[
                "label" => "description",
            ])
            ->add('type', ChoiceType::class,[
                "choices" => [
                    "Helper" => 1,
                    "Elder" => 2,
                    "admin" => 3,
                ]
               
            ])
            ->add('picture')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
