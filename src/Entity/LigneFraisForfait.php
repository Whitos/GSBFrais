<?php

namespace App\Entity;

use App\Repository\LigneFraisForfaitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFraisForfaitRepository::class)]
class LigneFraisForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisForfait')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FicheFrais $fichesFrais = null;

    #[ORM\ManyToOne(inversedBy: 'lignesFraisForfait')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FraisForfait $fraisForfaits = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getFichesFrais(): ?FicheFrais
    {
        return $this->fichesFrais;
    }

    public function setFichesFrais(?FicheFrais $fichesFrais): static
    {
        $this->fichesFrais = $fichesFrais;

        return $this;
    }

    public function getFraisForfaits(): ?FraisForfait
    {
        return $this->fraisForfaits;
    }

    public function setFraisForfaits(?FraisForfait $fraisForfaits): static
    {
        $this->fraisForfaits = $fraisForfaits;

        return $this;
    }
}
