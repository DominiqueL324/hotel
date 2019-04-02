<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupeRepository")
 */
class Groupe
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
    private $nom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nombre_membres;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", inversedBy="groupes")
     */
    private $client;

    public function __construct()
    {
        $this->client = new ArrayCollection();
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

    public function getNombreMembres(): ?int
    {
        return $this->nombre_membres;
    }

    public function setNombreMembres(?int $nombre_membres): self
    {
        $this->nombre_membres = $nombre_membres;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(Client $client): self
    {
        if (!$this->client->contains($client)) {
            $this->client[] = $client;
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->client->contains($client)) {
            $this->client->removeElement($client);
        }

        return $this;
    }
}
