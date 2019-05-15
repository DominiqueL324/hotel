<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RepasRepository")
 */
class Repas
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $heure;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="repas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Consomation", mappedBy="repas")
     */
    private $consomations;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ProformatRepas", mappedBy="repas", cascade={"persist", "remove"})
     */
    private $proformatRepas;

    public function __construct()
    {
        $this->consomations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(?string $heure): self
    {
        $this->heure = $heure;

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

    /**
     * @return Collection|Consomation[]
     */
    public function getConsomations(): Collection
    {
        return $this->consomations;
    }

    public function addConsomation(Consomation $consomation): self
    {
        if (!$this->consomations->contains($consomation)) {
            $this->consomations[] = $consomation;
            $consomation->addRepa($this);
        }

        return $this;
    }

    public function removeConsomation(Consomation $consomation): self
    {
        if ($this->consomations->contains($consomation)) {
            $this->consomations->removeElement($consomation);
            $consomation->removeRepa($this);
        }

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
        if ($this !== $proformatRepas->getRepas()) {
            $proformatRepas->setRepas($this);
        }

        return $this;
    }

}
