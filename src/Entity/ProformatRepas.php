<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProformatRepasRepository")
 */
class ProformatRepas
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Proformat", inversedBy="proformatRepas", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proformat;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Repas", inversedBy="proformatRepas", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $repas;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRepas(): ?Repas
    {
        return $this->repas;
    }

    public function setRepas(Repas $repas): self
    {
        $this->repas = $repas;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

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
