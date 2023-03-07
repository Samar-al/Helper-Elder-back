<?php

namespace App\EventListener;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ReviewListener {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function CalculUserAverageRating(Review $review, LifecycleEventArgs $event): void
    {

        // checking the user's reviews
        $user = $review->getUserTaker();
        $reviews = $user->getReviewsTaker();
        
        if(!$reviews){

            return; 
         }

            $allNotes = null;
            
            // adding all notes of reviews
            foreach($reviews as $review){
                $allNotes += $review->getRate();
            }

            
            // Calculating the average of rates
            if(count($reviews) != 0){

                $rating = $allNotes / count($reviews);
                
                // Setting the avg_rating in database
                $user->setAvgRating(round($rating,1));
                
                // Flush
                $this->entityManager->flush();
            }

    }

}