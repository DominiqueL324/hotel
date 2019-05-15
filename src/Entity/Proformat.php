<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProformatRepository")
 */
class Proformat
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
    private $made_at;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $motif;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="proformats")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProformatRepas", mappedBy="proformat", cascade={"persist", "remove"})
     */
    private $proformatRepas;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProformatSalle", mappedBy="proformat", cascade={"persist", "remove"})
     */
    private $jours;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProformatOffre", mappedBy="proformat", cascade={"persist", "remove"})
     */
    private $proformatOffre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMadeAt(): ?\DateTimeInterface
    {
        return $this->made_at;
    }

    public function setMadeAt(\DateTimeInterface $made_at): self
    {
        $this->made_at = $made_at;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProformatRepas(): ?ProformatRepas
    {
        return $this->proformatRepas;
    }

    public function setProformatRepas(ProformatRepas $proformatRepas): self
    {
        $this->proformatRepas = $proformatRepas;

        // set the owning side of the relation if necessary
        if ($this !== $proformatRepas->getProformat()) {
            $proformatRepas->setProformat($this);
        }

        return $this;
    }

    public function getJours(): ?ProformatSalle
    {
        return $this->jours;
    }

    public function setJours(ProformatSalle $jours): self
    {
        $this->jours = $jours;

        // set the owning side of the relation if necessary
        if ($this !== $jours->getProformat()) {
            $jours->setProformat($this);
        }

        return $this;
    }

    public function getProformatOffre(): ?ProformatOffre
    {
        return $this->proformatOffre;
    }

    public function setProformatOffre(ProformatOffre $proformatOffre): self
    {
        $this->proformatOffre = $proformatOffre;

        // set the owning side of the relation if necessary
        if ($this !== $proformatOffre->getProformat()) {
            $proformatOffre->setProformat($this);
        }

        return $this;
    }
}
