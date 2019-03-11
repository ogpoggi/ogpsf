<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductLike;
use App\Entity\Review;
use App\Form\ImageProductType;
use App\Form\ProductType;
use App\Form\ReviewType;
use App\Repository\ImageRepository;
use App\Repository\ProductLikeRepository;
use App\Repository\ProductRepository;
use App\Service\HistoriqueHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 * @IsGranted("ROLE_USER")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, ObjectManager $manager, ImageRepository $imageRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());
//            foreach ($product->getImages() as $image){
//                $image->setProduct($product);
//                $manager->persist($image);
//            }
            $product->setUser($this->getUser());
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'images' => $imageRepository->findAll()
        ]);
    }

    /**
     * @Route("/{slug}/{id}", name="product_show", methods={"GET","POST"})
     */
    public function show($slug,ProductRepository $repo, Request $request): Response
    {
        $product = $repo->findOneBySlug($slug);

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $review->setAuthor($this->getUser());
                $review->setProduct($product);
//                dump($form);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($review);
                $entityManager->flush();

        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER') and user == product.getUser()", message="This product is not yours")
     */
    public function edit(Request $request, Product $product, ObjectManager $manager, $slug, ProductRepository $repo, HistoriqueHelper $historiqueHelper): Response
    {

        $product = $repo->findOneBySlug($slug);
        $old_product = "old product";

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($request->request->get('edit')) {
                $this->getDoctrine()->getManager()->flush();

                $new_product = "new roduct";
                $historique = $historiqueHelper->new_historique($this->getUser());
                $historiqueHelper->new_modif($table="user", $champ="atta", $old_product, $new_product, $historique, $product->getId());

                return $this->redirectToRoute('product_index', [
                    'id' => $product->getId(),
                ]);
            }
            $manager->persist($product);
            $manager->flush();
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     *
     * @Route("/like/{id}/product", name="product_like")
     * @param Product $product
     * @param ObjectManager $manager
     * @param ProductLikeRepository $likeRepo
     * @return Response
     */
    public function like(Product $product, ObjectManager $manager,ProductLikeRepository $likeRepo): Response {
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ], 403);

        if ($product->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                'product' => $product,
                'user' => $user,
            ]);
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like bien supprimé',
                'likes' => $likeRepo->count(['product' => $product])
            ], 200);
        }

        $like = new ProductLike();
        $like->setProduct($product)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        /*return $this->json([
            'code' => 200,
            'message' => 'Ca marche bien',
            'likes' => $likeRepo->count(['product' => $product])
        ], 200);*/
        return $this->redirectToRoute('home');
    }
}
