<?php

namespace App\Controller\Api;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/api/service", name="app_tag")
     */
    public function list(TagRepository $tagRepository): JsonResponse
    {
        $tags = $tagRepository->findAll();
            return $this->json($tags,Response::HTTP_OK,[],["groups" => "tags"]);
    }

    
}
