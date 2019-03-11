<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueRepository")
 */
class Historique
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modified_date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\HistoriqueModif", mappedBy="historique", cascade={"persist", "remove"})
     */
    private $historiqueModif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="historiques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getModifiedDate(): ?\DateTimeInterface
    {
        return $this->modified_date;
    }

    public function setModifiedDate(\DateTimeInterface $modified_date): self
    {
        $this->modified_date = $modified_date;

        return $this;
    }

    public function getHistoriqueModif(): ?HistoriqueModif
    {
        return $this->historiqueModif;
    }

    public function setHistoriqueModif(HistoriqueModif $historiqueModif): self
    {
        $this->historiqueModif = $historiqueModif;

        // set the owning side of the relation if necessary
        if ($this !== $historiqueModif->getHistorique()) {
            $historiqueModif->setHistorique($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
