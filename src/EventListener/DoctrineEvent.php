<?php
/**
 * Created by PhpStorm.
 * User: BPOGGI
 * Date: 08/03/2019
 * Time: 14:45
 */

namespace App\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineEvent implements EventSubscriber
{

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return array('prePersist', 'preUpdate', 'postPersist', 'postUpdate');//les événements écoutés
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        //Si c'est bien une entité Contact qui va être "persisté"
        if ($entity instanceof Contact) {
            $entity->updateGmapData();//on met à jour les coordonnées via l'appel à google map
        }
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $changeset = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        //Si c'est bien une entité Contact qui va être modifié
        if ($entity instanceof Contact) {
            //Si il y'a eu une mise a jour sur les propriétés en relation avec l'adresse (ici "address", "city" et "postalCode")
            if (array_key_exists("address", $changeset) || array_key_exists("city", $changeset) || array_key_exists("postalCode", $changeset)) {
                $entity->updateGmapData();//on met à jour les coordonnées via l'appel à google map
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args){}

    public function postUpdate(LifecycleEventArgs $args){}
}