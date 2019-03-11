<?php
/**
 * Created by PhpStorm.
 * User: BPOGGI
 * Date: 07/03/2019
 * Time: 11:32
 */

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HistoriqueModif;
use App\Entity\Historique;

class HistoriqueHelper
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /*public function getRepositories()
    {
        $historique = $this->manager->getRepository('Entity:Historique');
        $historique_modif = $this->manager->getRepository('Entity:HistoriqueModif');
        return array($historique, $historique_modif);
    }*/

    public function new_historique($user){
        $em = $this->em;
        $historique = new Historique();
        $historique->setUser($user);
        $historique->setModifiedDate(new  \DateTime());
        $em->persist($historique);
        $em->flush();

        return $historique;
    }

    public function new_modif($table, $champ, $old, $new, $historique, $record_id){
        $em = $this->em;
        $historique_modif = new HistoriqueModif();
        $historique_modif->setTableModif($table);
        $historique_modif->setChampModif($champ);
        $historique_modif->setOldValue($old);
        $historique_modif->setNewValue($new);
        $historique_modif->setHistorique($historique);
        $historique_modif->setRecordId($record_id);
        $em->persist($historique_modif);
        $em->flush();
    }
}