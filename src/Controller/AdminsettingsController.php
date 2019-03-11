<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/adminsettings")
 * @IsGranted("ROLE_USER")
 */
class AdminsettingsController extends AbstractController
{
    /**
     * @Route("", name="adminsettings")
     */
    public function index(){

        $routes = array();
        foreach ($this->container->get('router')->getRouteCollection()->all() as  $name=>$route) {
            $routes[$name] = $name;

        }
        return $this->render('adminsettings/index.html.twig', [
            'routes' => $routes,
            'controller_name' => 'AdminsettingsController',
        ]);
    }

    /**
     * @Route("/infoph", name="infoph")
     */
    public function phpinfo(){
        $infoph = phpinfo();

        return $this->render('adminsettings/phpinfo.html.twig', [
            'infoph' => $infoph,
            'controller_name' => 'AdminsettingsController',
        ]);
    }
}
