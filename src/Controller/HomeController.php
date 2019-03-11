<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{page<\d>?1}", name="home")
     */
    public function index(ProductRepository $productRepository, $page)
    {
        $limit = 9;

        $start = $page * $limit -$limit;

        $total = count($productRepository->findAll());

        $pages = ceil($total / $limit);

        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findBy([],['id'=>'DESC'],$limit,$start),
            'pages' => $pages,
            'page' => $page
        ]);
    }
}
