<?php

namespace App\Controller\Back;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back-office")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/service", name="app_back_tag_index", methods={"GET"})
     */
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('back/tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    /**
     * @Route("/service/ajouter", name="app_back_tag_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TagRepository $tagRepository): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setCreatedAt(new \DateTime('now'));
            $tagRepository->add($tag, true);

            return $this->redirectToRoute('app_back_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("service/{id}", name="app_back_tag_show", methods={"GET"})
     */
    public function show(Tag $tag): Response
    {
        return $this->render('back/tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("service/{id}/modifier", name="app_back_tag_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Tag $tag, TagRepository $tagRepository): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setUpdatedAt(new \DateTime);
            $tagRepository->add($tag, true);

            return $this->redirectToRoute('app_back_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * @Route("service/{id}/supprimer", name="app_back_tag_delete", methods={"POST"})
     */
    public function delete(Request $request, Tag $tag, TagRepository $tagRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $tagRepository->remove($tag, true);
        }

        return $this->redirectToRoute('app_back_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
