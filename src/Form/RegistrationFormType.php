<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                "widget"=>'single_text',
                "attr" => [
                    "placeholder" => "Date de naissance"
                ]
            ])
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
                    
                ]
            ])
            ->add('_token', HiddenType::class, [
                'mapped' => false,
                'data' => 'no_csrf', // this is just a dummy value to disable CSRF protection
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
