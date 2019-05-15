<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProformatOffreRepository")
 */
class ProformatOffre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Offre", inversedBy="proformatOffre", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $offre;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Proformat", inversedBy="proformatOffre", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proformat;

    /**
     * @ORM\Column(type="integer")
     */
    private $nuite;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(Offre $offre): self
    {
        $this->offre = $offre;

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

    public function getNuite(): ?int
    {
        return $this->nuite;
    }

    public function setNuite(int $nuite): self
    {
        $this->nuite = $nuite;

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
