<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User  implements UserInterface
{

    const ROLE = [
         'ROLE_ADMIN' => 'Administrateur',
         'ROLE_RECEP' => 'Receptionniste',
         'ROLE_COMPT' => 'Comptable'
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
     * @ORM\Column(type="string", length=255)
     */
    private $matricule;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $born_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu_naissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

   /**
     * @ORM\Column(type="array")
     * 
     */
    private $roles;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

     public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Identification", mappedBy="user")
     */
    private $identifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="user")
     */
    private $locations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Repas", mappedBy="user")
     */
    private $repas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consomation", mappedBy="user")
     */
    private $consomations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Proformat", mappedBy="user")
     */
    private $proformats;

    public function __construct()
    {
        $this->identifications = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->repas = new ArrayCollection();
        $this->consomations = new ArrayCollection();
        $this->proformats = new ArrayCollection();
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

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
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

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

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

    public function getLieuNaissance(): ?string
    {
        return $this->lieu_naissance;
    }

    public function setLieuNaissance(?string $lieu_naissance): self
    {
        $this->lieu_naissance = $lieu_naissance;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /*public function getRoleType(): string
    {
        return self::ROLE[$this->roles];
    }*/
     public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        $tab = array();
        return $this->roles;
    }

    public function setRoles(string $roles)
    {
        $tab = array($roles);
        $this->roles = $tab;
        return $this;
    }
    public function eraseCredentials()
    {
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
            $identification->setUser($this);
        }

        return $this;
    }

    public function removeIdentification(Identification $identification): self
    {
        if ($this->identifications->contains($identification)) {
            $this->identifications->removeElement($identification);
            // set the owning side to null (unless already changed)
            if ($identification->getUser() === $this) {
                $identification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Location[]
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setUser($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->contains($location)) {
            $this->locations->removeElement($location);
            // set the owning side to null (unless already changed)
            if ($location->getUser() === $this) {
                $location->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Repas[]
     */
    public function getRepas(): Collection
    {
        return $this->repas;
    }

    public function addRepa(Repas $repa): self
    {
        if (!$this->repas->contains($repa)) {
            $this->repas[] = $repa;
            $repa->setUser($this);
        }

        return $this;
    }

    public function removeRepa(Repas $repa): self
    {
        if ($this->repas->contains($repa)) {
            $this->repas->removeElement($repa);
            // set the owning side to null (unless already changed)
            if ($repa->getUser() === $this) {
                $repa->setUser(null);
            }
        }

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
            $consomation->setUser($this);
        }

        return $this;
    }

    public function removeConsomation(Consomation $consomation): self
    {
        if ($this->consomations->contains($consomation)) {
            $this->consomations->removeElement($consomation);
            // set the owning side to null (unless already changed)
            if ($consomation->getUser() === $this) {
                $consomation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Proformat[]
     */
    public function getProformats(): Collection
    {
        return $this->proformats;
    }

    public function addProformat(Proformat $proformat): self
    {
        if (!$this->proformats->contains($proformat)) {
            $this->proformats[] = $proformat;
            $proformat->setUser($this);
        }

        return $this;
    }

    public function removeProformat(Proformat $proformat): self
    {
        if ($this->proformats->contains($proformat)) {
            $this->proformats->removeElement($proformat);
            // set the owning side to null (unless already changed)
            if ($proformat->getUser() === $this) {
                $proformat->setUser(null);
            }
        }

        return $this;
    }
}
