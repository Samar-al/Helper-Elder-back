<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



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
     *
     * Edit one user in the front-office
     * @IsGranted("ROLE_USER")
     */
    public function getMyId(User $user, UserRepository $userRepository, ConversationRepository $conversationRepository): JsonResponse
        {
            
            if($user != $this->security->getUser()){
                
                throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à accéder à ce profil');
            }

            $user = $userRepository->find($this->security->getUser());
           // Return a Json with data and status code
            return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]); 
        
        }

    /**
     * @Route("/api/mon-profil/{id}/modifier", name="app_api_user_edit", methods={"POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, User $user): Response
    {

        if($user != $this->security->getUser()){
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à modifier ce profil');
        }

        // Getting the JSON of our request
        $json = $request->getContent();

        try {
            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        } catch (NotEncodableValueException $e) {
            
            return $this->json(["error" => "JSON non valide"], Response::HTTP_BAD_REQUEST);
        }

        // Validate the user
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update the user
        $entityManager = $doctrine->getManager();
        $user->setUpdatedAt(new \DateTime('now'));

        $entityManager->flush();

         return $this->json(
            $user,
            Response::HTTP_OK,
            [
                "Location" => $this->generateUrl("app_api_user_myProfil", ["id" => $user->getId()])
            ],
            [
                "groups" => "users"
            ]
        );
    }
    /**
     * @Route("/api/mon-profil/{id}/supprimer", name="app_api_user_delete", methods={"POST"}, requirements={"id"="\d+"})
     * 
     * 
     */
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        if($user != $this->security->getUser()){
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à supprimer ce profil');
        }
        $entityManager->remove($user);
        $entityManager->flush();
        // ! modifier la route de redirection vers le profil de la personne en question quand cette route sera créée
        return $this->redirectToRoute('app_api_main_home', [], Response::HTTP_SEE_OTHER);
    }
    
}
