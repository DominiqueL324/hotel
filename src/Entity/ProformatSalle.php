<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProformatSalleRepository")
 */
class ProformatSalle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Salle", inversedBy="proformatSalle", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $salle;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Proformat", inversedBy="jours", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proformat;

    /**
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getProformat(): ?Proformat
    {
        return $this->proformat;
    }

    public function setProformat(Proformat $proformat): self
    {
        $this->proformat = $proformat;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getCout(): ?int
    {
        return $this->cout;
    }

    public function setCout(int $cout): self
    {
        $this->cout = $cout;

        return $this;
    }
}
