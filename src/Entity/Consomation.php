<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsomationRepository")
 */
class Consomation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="consomations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="consomations")
     */
    private $client;

    /**
     * @ORM\Column(type="datetime")
     */
    private $made_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Repas", inversedBy="consomations")
     */
    private $repas;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getCout(): ?int
    {
        return $this->cout;
    }

    public function setCout(int $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    public function getRepas(): ?Repas
    {
        return $this->repas;
    }

    public function setRepas(?Repas $repas): self
    {
        $this->repas = $repas;

        return $this;
    }
}
