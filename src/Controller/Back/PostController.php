<?php

namespace App\Controller\Back;

use App\Entity\Post;
use App\Form\Post1Type;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/back-office")
 */
class PostController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

     /**
     * @Route("/", name="app_back_post_home", methods={"GET"})
     */
    public function home(PostRepository $postRepository): Response
    {
        return $this->render('back/post/home.html.twig',);
    }

    /**
     * @Route("/annonce", name="app_back_post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('back/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
 
    /**
     * @Route("/search", name="post_search")
     */
    public function search(Request $request, PostRepository $postRepository, UserRepository $userRepository, TagRepository $tagRepository)
    {
        $query = $request->query->get('query');
        
        // Search for posts that match the query
        $posts = $postRepository->searchPosts($query);
      
        // Search for users that match the query
        $users = $userRepository->searchUsers($query);

        // Search for tags that match the query
        $tags = $tagRepository->searchTags($query);

        return $this->render('base.html.twig', [
            'query' => $query,
            'posts' => $posts,
            'users' => $users,
            'tags' => $tags,
        ]);
    }


    /**
     * @Route("/annonce/ajouter", name="app_back_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(Post1Type::class, $post);
        $form->handleRequest($request);
         
        if ($form->isSubmitted() && $form->isValid()) {

            $post->setSlug($post->getSlug());
            $post->setCreatedAt(new \DateTime());
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_back_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="app_back_post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('back/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("annonce/{id}/edit", name="app_back_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(Post1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime());
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_back_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("annonce/{id}/supprimer", name="app_back_post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_back_post_index', [], Response::HTTP_SEE_OTHER);
    }

   
}
