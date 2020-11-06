<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="commande")
     */
    private $ligneCommandes;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="commandes")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommandes(LigneCommande $ligneCommandes): self
    {
        if (!$this->ligneCommandes->contains($ligneCommandes)) {
            $this->ligneCommandes[] = $ligneCommandes;
            $ligneCommandes->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommandes(LigneCommande $ligneCommandes): self
    {
        if ($this->ligneCommandes->contains($ligneCommandes)) {
            $this->ligneCommandes->removeElement($ligneCommandes);
            // set the owning side to null (unless already changed)
            if ($ligneCommandes->getCommande() === $this) {
                $ligneCommandes->setCommande(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
