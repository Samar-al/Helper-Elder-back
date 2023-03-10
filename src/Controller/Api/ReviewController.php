<?php

namespace App\Controller\Api;

use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

   

    /**
     * @Route("api/avis/ajouter", name="app_user_review", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addReview(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ReviewRepository $reviewRepository, Security $security): Response
    {
        
        // getting the json of our request
        $json = $request->getContent();
        
        try{
            $review = $serializer->deserialize($json, Review::class, 'json');
            
        }catch(NotEncodableValueException $e){
            
            return $this->json(["error" => "Json non valide"],Response::HTTP_BAD_REQUEST);
        }  

        // Validator will check that my inputs are well filled
        // if incomplete, will get a sql error when adding
        $errors = $validator->validate($review);
        
        //  iterate on the errors array
        
        if(count($errors) > 0){
            //  create a array of errors
            $errorsArray = [];
            foreach($errors as $error){
                // Error message when a fied is 
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($review->getUserGiver() != $this->security->getUser()){
            throw $this->createAccessDeniedException('Access denied: l\'id de la personne qui envoie le message n\'est pas celui de la personne connectée');
        }
        
        // Set the additional required datas from the review in database
        $userTaker = $review->getUserTaker();
        $review->setCreatedAt(new DateTime());
        $review->setUserGiver($this->security->getUser());
        $reviewRepository->add($review,true);
        $userGiver = $this->security->getUser();
        
        
        // Return a json with first place the datas and secondly a status code
        return $this->json(
            [$review, $userGiver,$userTaker ],
            Response::HTTP_CREATED,
            [
               // "Location" => $this->generateUrl("app_api_user_getUserById", ["id" => $review->getUserTaker()->getId()])
            ],
            [
                "groups" => "reviews"
            ]
        );
    } 

     /**
     * @Route("/api/avis-par-utilisateur/{id}", name="app_api_review_getOneById", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * 
     */
    public function getOne(ReviewRepository $reviewRepository, int $id, UserRepository $userRepository): Response
    {

        $reviewByUserId = $reviewRepository->findReviewByUserTakerId($id);
       
        $userGiver = [];
        $userTaker = [];
        foreach($reviewByUserId as $user){
            $userGiver[]=$userRepository->find($user["user_giver_id"]);
        }
        
        // Returns a Json with first argument data and 2nd argument the status code
        return $this->json(compact("reviewByUserId","userGiver"),Response::HTTP_OK,[],["groups" => "reviews"]);
    }

}
