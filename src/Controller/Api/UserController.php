<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("api/profil/{id}", name="app_api_user_getUserById", methods={"GET"}, requirements={"id"="\d+"} )
     * Present one user
     */
    public function getUserById(User $user): JsonResponse
    {
       
        // dd($user) ;
        // Return a Json with data and status code
         return $this->json($user,Response::HTTP_OK,[],["groups" => "users"]);   
    }

    /**
     * @Route("api/profil/{id}/modifier", name="app_api_user_edit", methods={"GET", "POST"}, requirements={"id"="\d+"} )
     * Modify one user in the front-office
     */
    public function edit(User $user): JsonResponse
        {
        return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]);
    }
}
