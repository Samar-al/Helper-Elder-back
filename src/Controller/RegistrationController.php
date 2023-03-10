<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/api/inscription", name="app_register")
     */
    public function register(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, MailerInterface $mailer): Response
    {

       // getting the json of notre request
       $json = $request->getContent();
         
        
       try{
           
           $user = $serializer->deserialize($json, User::class, 'json');
           
       }catch(NotEncodableValueException $e){
           
           return $this->json(["error" => "Json non valide"],Response::HTTP_BAD_REQUEST);
       }  
      
       $errors = $validator->validate($user, null, ['registration']);
       
       if(count($errors) > 0){
          
           $errorsArray = [];
           foreach($errors as $error){
               
               $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
           }
           return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
       }
          
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new \DateTime('now')); 
            $userRepository->add($user, true);

             // generate a signed url and email it to the user
              $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                 (new TemplatedEmail())
                     ->from(new Address('helpers.elders@gmail.com', 'Admin'))
                     ->to($user->getEmail())
                     ->subject('Please Confirm your Email')
                     ->htmlTemplate('registration/confirmation_email.html.twig')
                );
            
            return $this->json(
                $user,
                Response::HTTP_CREATED);
     
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirect('http://localhost:8080/connexion');
    }


}
