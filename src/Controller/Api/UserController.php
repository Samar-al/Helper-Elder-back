<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    
     /**
     * @Route("api/profil/{id}", name="app_api_user_getUserById", methods={"GET"}, requirements={"id"="\d+"} )
     * Present one user
     */
    public function getUserById(User $user): JsonResponse
    {
        if (empty($user)) {
            return $this->json(["error" => "Cet utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
        } 
        // Return a Json with data and status code
         return $this->json($user,Response::HTTP_OK,[],["groups" => "users"]);   
    }

    /**
     * @Route("api/mon-profil/{id}", name="app_api_user_myProfil", methods={"GET"}, requirements={"id"="\d+"} )
     * Edit one user in the front-office
     */
    public function getMyId(User $user): JsonResponse
        {
            
         // Return a Json with data and status code
         return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]);
      
    }
    
}
