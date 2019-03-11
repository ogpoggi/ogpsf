<?php

namespace App\Controller;

use App\Entity\Support;
use App\Form\SupportType;
use App\Repository\SupportRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    /**
     * @Route("/support", name="support")
     */
    public function index(Request $request, ObjectManager $manager, SupportRepository $repo)
    {
        $support = new Support();
        $form = $this->createForm(SupportType::class, $support);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $support->setCreatedAt(new  \DateTime());
            $support->setUser($this->getUser());

            $manager->persist($support);
            $manager->flush();
        }

        $supports = $repo->findAll();

        return $this->render('support/index.html.twig', array(
            'form' => $form->createView(),
            'supports' => $supports
        ));
    }

    /**
     * @Route("/support/liste", name="support_list")
     */
    public function listSupport(SupportRepository $repo){

        $supports = $repo->findAll();
        return $this->render('support/index.html.twig', array(
            'supports' => $supports
        ));
    }
}
