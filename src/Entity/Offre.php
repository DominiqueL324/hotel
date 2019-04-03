<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OffreRepository")
 */
class Offre
{

    const CLIM = [
             0 => 'Climatisée',
             1 => 'Non Climatisée'
        ];
    const WIFI = [
             0 => 'Wifi',
             1 => 'Pas de Wifi'
        ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="integer")
     */
    private $superficie;

    /**
     * @ORM\Column(type="integer")
     */
    private $etage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $clim;

    /**
     * @ORM\Column(type="boolean")
     */
    private $wifi;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $porte;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dispo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Identification", mappedBy="offre")
     */
    private $identifications;

    public function __construct()
    {
        $this->identifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSuperficie(): ?int
    {
        return $this->superficie;
    }

    public function setSuperficie(int $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getEtage(): ?int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function getClim(): ?bool
    {
        return $this->clim;
    }

    public function getClimType(): string
    {
        return self::CLIM[$this->clim];
    }

    public function setClim(bool $clim): self
    {
        $this->clim = $clim;

        return $this;
    }

    public function getWifi(): ?bool
    {
        return $this->wifi;
    }

    public function setWifi(bool $wifi): self
    {
        $this->wifi = $wifi;

        return $this;
    }

    public function getWifiType(): string
    {
        return self::WIFI[$this->wifi];
    }

    public function getPorte(): ?string
    {
        return $this->porte;
    }

    public function setPorte(string $porte): self
    {
        $this->porte = $porte;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDispo(): ?bool
    {
        return $this->dispo;
    }

    public function setDispo(bool $dispo): self
    {
        $this->dispo = $dispo;

        return $this;
    }

    /**
     * @return Collection|Identification[]
     */
    public function getIdentifications(): Collection
    {
        return $this->identifications;
    }

    public function addIdentification(Identification $identification): self
    {
        if (!$this->identifications->contains($identification)) {
            $this->identifications[] = $identification;
            $identification->setOffre($this);
        }

        return $this;
    }

    public function removeIdentification(Identification $identification): self
    {
        if ($this->identifications->contains($identification)) {
            $this->identifications->removeElement($identification);
            // set the owning side to null (unless already changed)
            if ($identification->getOffre() === $this) {
                $identification->setOffre(null);
            }
        }

        return $this;
    }
}
