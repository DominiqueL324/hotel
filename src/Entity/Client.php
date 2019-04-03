<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cni;

    /**
     * @ORM\Column(type="bigint")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Groupe", mappedBy="client")
     */
    private $groupes;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pays_residence;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nationalite;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $lieu_de_naissance;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $profession;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cni_made_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $born_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Identification", mappedBy="client")
     */
    private $identifications;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->identifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->addClient($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            $groupe->removeClient($this);
        }

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPaysResidence(): ?string
    {
        return $this->pays_residence;
    }

    public function setPaysResidence(?string $pays_residence): self
    {
        $this->pays_residence = $pays_residence;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getLieuDeNaissance(): ?string
    {
        return $this->lieu_de_naissance;
    }

    public function setLieuDeNaissance(?string $lieu_de_naissance): self
    {
        $this->lieu_de_naissance = $lieu_de_naissance;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getCniMadeAt(): ?\DateTimeInterface
    {
        return $this->cni_made_at;
    }

    public function setCniMadeAt(?\DateTimeInterface $cni_made_at): self
    {
        $this->cni_made_at = $cni_made_at;

        return $this;
    }

    public function getBornAt(): ?\DateTimeInterface
    {
        return $this->born_at;
    }

    public function setBornAt(?\DateTimeInterface $born_at): self
    {
        $this->born_at = $born_at;

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
            $identification->setClient($this);
        }

        return $this;
    }

    public function removeIdentification(Identification $identification): self
    {
        if ($this->identifications->contains($identification)) {
            $this->identifications->removeElement($identification);
            // set the owning side to null (unless already changed)
            if ($identification->getClient() === $this) {
                $identification->setClient(null);
            }
        }

        return $this;
    }
}
