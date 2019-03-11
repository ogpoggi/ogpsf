<?php
/**
 * Created by PhpStorm.
 * User: BPOGGI
 * Date: 08/03/2019
 * Time: 10:25
 */

namespace App\Service;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createBreadcrumbMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        // cet item sera toujours affiché
        $menu->addChild('Home', array('route' => 'home'));

        // crée le menu en fonction de la route
        /*switch($request->get('_route')){
            case 'Acme_create_post':
                $menu
                    ->addChild('label.create.post')
                    ->setCurrent(true)
                    // setCurrent est utilisé pour ajouter une classe css "current"
                ;
                break;
        }*/
        return $menu;
    }
}