<?php

namespace App\Entity;

use App\Repository\FicheFraisRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheFraisRepository::class)]
class FicheFrais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $nbJustificatifs = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantValid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $mois = null;

    #[ORM\ManyToOne(inversedBy: 'fichesfrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'fichesFrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    /**
     * @var Collection<int, LigneFraisForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisForfait::class, mappedBy: 'ficheFrais')]
    private Collection $lignesFraisForfait;

    /**
     * @var Collection<int, LigneFraisHorsForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisHorsForfait::class, mappedBy: 'ficheFrais')]
    private Collection $lignesFraisHorsForfait;


    public function __construct()
    {
        $this->lignesFraisForfait = new ArrayCollection();
        $this->lignesFraisHorsForfait = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMois(): ?\DateTimeInterface
    {
        return $this->mois;
    }

    public function setMois(\DateTimeInterface $mois): static
    {
        $this->mois = $mois;

        return $this;
    }

    public function getNbJustificatifs(): ?int
    {
        return $this->nbJustificatifs;
    }

    public function setNbJustificatifs(int $nbJustificatifs): static
    {
        $this->nbJustificatifs = $nbJustificatifs;

        return $this;
    }

    public function getMontantValid(): ?string
    {
        return $this->montantValid;
    }

    public function setMontantValid(string $montantValid): static
    {
        $this->montantValid = $montantValid;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(\DateTimeInterface $dateModif): static
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisForfait>
     */
    public function getLignesFraisForfait(): Collection
    {
        return $this->lignesFraisForfait;
    }

    public function addLignesFraisForfait(LigneFraisForfait $lignesFraisForfait): static
    {
        if (!$this->lignesFraisForfait->contains($lignesFraisForfait)) {
            $this->lignesFraisForfait->add($lignesFraisForfait);
            $lignesFraisForfait->setFichesFrais($this);
        }

        return $this;
    }

    public function removeLignesFraisForfait(LigneFraisForfait $lignesFraisForfait): static
    {
        if ($this->lignesFraisForfait->removeElement($lignesFraisForfait)) {
            // set the owning side to null (unless already changed)
            if ($lignesFraisForfait->getFichesFrais() === $this) {
                $lignesFraisForfait->setFichesFrais(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisHorsForfait>
     */
    public function getLignesFraisHorsForfait(): Collection
    {
        return $this->lignesFraisHorsForfait;
    }

    public function addLignesFraisHorsForfait(LigneFraisHorsForfait $lignesFraisHorsForfait): static
    {
        if (!$this->lignesFraisHorsForfait->contains($lignesFraisHorsForfait)) {
            $this->lignesFraisHorsForfait->add($lignesFraisHorsForfait);
            $lignesFraisHorsForfait->setFicheFrais($this);
        }

        return $this;
    }

    public function removeLignesFraisHorsForfait(LigneFraisHorsForfait $lignesFraisHorsForfait): static
    {
        if ($this->lignesFraisHorsForfait->removeElement($lignesFraisHorsForfait)) {
            // set the owning side to null (unless already changed)
            if ($lignesFraisHorsForfait->getFicheFrais() === $this) {
                $lignesFraisHorsForfait->setFicheFrais(null);
            }
        }

        return $this;
    }

}
