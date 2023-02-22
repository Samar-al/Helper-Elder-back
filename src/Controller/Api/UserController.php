<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("api/profil/{id}", name="app_api_user_getUserById", methods={"GET"}, requirements={"id"="\d+"} )
     * Present one user
     */
    public function getUserById(User $user): JsonResponse
    {
         // Return a Json with data and status code
         return $this->json($user,Response::HTTP_OK,[],["groups" => "users"]);   
    }

    /**
     * @Route("api/profil/{id}/modifier", name="app_api_user_edit", methods={"GET"}, requirements={"id"="\d+"} )
     * Edit one user in the front-office
     */
    public function show(User $user): JsonResponse
        {
            
         return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]);
      
    }

    /**
     * @Route("api/profil/{id}/modifier", name="app_api_user_update", methods={"POST"}, requirements={"id"="\d+"} )
     * Edit one user in the front-office
     */
    public function edit(User $user, Request $request, UserRepository $userRepository, ValidatorInterface $validator, SerializerInterface $serializer): Response
        {
            $json = $request->getContent();
            try{
                // 
                $user = $serializer->deserialize($json, User::class, 'json');
    
            }catch(NotEncodableValueException $e){
    
                return $this->json(["error" => "Json non valide"],Response::HTTP_BAD_REQUEST);
            }  
            // 
            $errors = $validator->validate($user);

            if(count($errors) > 0){
                // Je créer un tableau avec mes erreurs
                $errorsArray = [];
                foreach($errors as $error){
                    // A l'index qui correspond au champs mal remplis, j'y injecte le/les messages d'erreurs
                    $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Set modification in Database
            $userRepository->add($user, true);

            return $this->json($user, Response::HTTP_ACCEPTED,[
                        "Location" => $this->generateUrl("app_api_user_getUserById", ["id" => $user->getId()])
                        ], 
                        [
                            "groups" => "users"
                        ]
            );
        }

}
