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
            
            // J'initialise une variable allNotes à 0
            $allNotes = null;

            // Je foreach sur les reviews et j'additione toutes les notes dans allNotes
            foreach($reviews as $review){
                $allNotes += $review->getRate();
            }

            // Je divise le total des notes par le nombre de note
            $rating = $allNotes / count($reviews);

            // Je set la note du film par mon résultat
            $user->setAvgRating(round($rating,1));

            // Je flush
            $this->entityManager->flush();

    }
}