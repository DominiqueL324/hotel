<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IdentificationRepository")
 */
class Identification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_personne;

    /**
     * @ORM\Column(type="datetime")
     */
    private $arrived_at;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $se_rendant_a;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lived_at;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $mode_reglement;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $marque_vehicule;

    /**
     * @ORM\Column(type="datetime")
     */
    private $made_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero_identification;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Reservation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $reservation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="identifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Offre", inversedBy="identifications")
     */
    private $offre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="identifications")
     */
    private $client;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cout;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paiement", mappedBy="identification")
     */
    private $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombrePersonne(): ?int
    {
        return $this->nombre_personne;
    }

    public function setNombrePersonne(int $nombre_personne): self
    {
        $this->nombre_personne = $nombre_personne;

        return $this;
    }

    public function getArrivedAt(): ?\DateTimeInterface
    {
        return $this->arrived_at;
    }

    public function setArrivedAt(\DateTimeInterface $arrived_at): self
    {
        $this->arrived_at = $arrived_at;

        return $this;
    }

    public function getSeRendantA(): ?string
    {
        return $this->se_rendant_a;
    }

    public function setSeRendantA(string $se_rendant_a): self
    {
        $this->se_rendant_a = $se_rendant_a;

        return $this;
    }

    public function getLivedAt(): ?\DateTimeInterface
    {
        return $this->lived_at;
    }

    public function setLivedAt(\DateTimeInterface $lived_at): self
    {
        $this->lived_at = $lived_at;

        return $this;
    }

    public function getModeReglement(): ?string
    {
        return $this->mode_reglement;
    }

    public function setModeReglement(string $mode_reglement): self
    {
        $this->mode_reglement = $mode_reglement;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getMarqueVehicule(): ?string
    {
        return $this->marque_vehicule;
    }

    public function setMarqueVehicule(?string $marque_vehicule): self
    {
        $this->marque_vehicule = $marque_vehicule;

        return $this;
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

    public function getNumeroIdentification(): ?string
    {
        return $this->numero_identification;
    }

    public function setNumeroIdentification(string $numero_identification): self
    {
        $this->numero_identification = $numero_identification;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

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

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCout(): ?float
    {
        return $this->cout;
    }

    public function setCout(?float $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setIdentification($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->contains($paiement)) {
            $this->paiements->removeElement($paiement);
            // set the owning side to null (unless already changed)
            if ($paiement->getIdentification() === $this) {
                $paiement->setIdentification(null);
            }
        }

        return $this;
    }
}
