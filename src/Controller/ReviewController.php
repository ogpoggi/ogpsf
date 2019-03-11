<?php

namespace App\Controller;

use App\Entity\HistoriqueModif;
use App\Entity\Review;
use App\Form\ReviewCommentType;
use App\Form\ReviewType;
use App\Repository\HistoriqueModifRepository;
use App\Repository\HistoriqueRepository;
use App\Repository\ReviewRepository;
use App\Service\HistoriqueHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/review")
 * @IsGranted("ROLE_USER")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("", name="review_index", methods={"GET"})
     */
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="review_new", methods={"GET","POST"})
     */
    public function newComment(Request $request): Response
    {
        //$ratingValue = $_GET["ratingValue"];
        $review = new Review();
        $form = $this->createForm(ReviewCommentType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setCreatedAt(new \DateTime());
            $review->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('review_index');
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_show", methods={"GET"})
     */
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="review_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Review $review, HistoriqueHelper $historiquehelper): Response
    {

        $old_review = "old review";

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();


            $new_review = "new review";
            $historique = $historiquehelper->new_historique($this->getUser());
            $historiquehelper->new_modif($table="review", $champ="atta", $old_review, $new_review, $historique, $review->getId());

            return $this->redirectToRoute('review_index', [
                'id' => $review->getId(),
            ]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="review_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Review $review): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('review_index');
    }
}
