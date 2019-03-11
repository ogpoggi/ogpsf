<?php

namespace App\Controller;

use App\Entity\ProductTag;
use App\Form\ProductTagType;
use App\Repository\ProductTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tag")
 */
class ProductTagController extends AbstractController
{
    /**
     * @Route("", name="product_tag_index", methods={"GET"})
     */
    public function index(ProductTagRepository $productTagRepository): Response
    {
        return $this->render('product_tag/index.html.twig', [
            'product_tags' => $productTagRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_tag_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $productTag = new ProductTag();
        $form = $this->createForm(ProductTagType::class, $productTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productTag);
            $entityManager->flush();

            return $this->redirectToRoute('product_tag_index');
        }

        return $this->render('product_tag/new.html.twig', [
            'product_tag' => $productTag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_tag_show", methods={"GET"})
     */
    public function show(ProductTag $productTag): Response
    {
        return $this->render('product_tag/show.html.twig', [
            'product_tag' => $productTag,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_tag_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProductTag $productTag): Response
    {
        $form = $this->createForm(ProductTagType::class, $productTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_tag_index', [
                'id' => $productTag->getId(),
            ]);
        }

        return $this->render('product_tag/edit.html.twig', [
            'product_tag' => $productTag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_tag_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProductTag $productTag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productTag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($productTag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_tag_index');
    }
}
