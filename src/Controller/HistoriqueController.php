<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Historique;
use App\Entity\HistoriqueModif;
use App\Repository\HistoriqueRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Provider\DateTime;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/historique")
 * @IsGranted("ROLE_USER")
 */
class HistoriqueController extends AbstractController
{
    /**
     * @Route("", name="historique_index", methods={"GET"})
     */
    public function index(HistoriqueRepository $repo)
    {
        return $this->render('historique/index.html.twig', [
            'historiques' =>  $repo->findAll(),
            'controller_name' => 'HistoriqueController',
        ]);
    }
}
