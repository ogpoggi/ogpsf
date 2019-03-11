<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueModifRepository")
 */
class HistoriqueModif
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $table_modif;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $champ_modif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $old_value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $new_value;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Historique", inversedBy="historiqueModif", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $historique;

    /**
     * @ORM\Column(type="integer")
     */
    private $record_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableModif(): ?string
    {
        return $this->table_modif;
    }

    public function setTableModif(string $table_modif): self
    {
        $this->table_modif = $table_modif;

        return $this;
    }

    public function getChampModif(): ?string
    {
        return $this->champ_modif;
    }

    public function setChampModif(string $champ_modif): self
    {
        $this->champ_modif = $champ_modif;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->old_value;
    }

    public function setOldValue(string $old_value): self
    {
        $this->old_value = $old_value;

        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->new_value;
    }

    public function setNewValue(string $new_value): self
    {
        $this->new_value = $new_value;

        return $this;
    }

    public function getHistorique(): ?Historique
    {
        return $this->historique;
    }

    public function setHistorique(Historique $historique): self
    {
        $this->historique = $historique;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecordId()
    {
        return $this->record_id;
    }

    /**
     * @param mixed $record_id
     */
    public function setRecordId($record_id): void
    {
        $this->record_id = $record_id;
    }

}
