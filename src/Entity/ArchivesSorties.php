<?php

namespace App\Entity;

use App\Repository\ArchivesSortiesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchivesSortiesRepository::class)
 */
class ArchivesSorties
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomSortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateFinInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudoOrganisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomOrganisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomOrganisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomLieu;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $cpVille;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomVille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbParticipants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(string $nomSortie): self
    {
        $this->nomSortie = $nomSortie;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->dateFinInscription;
    }

    public function setDateFinInscription(\DateTimeInterface $dateFinInscription): self
    {
        $this->dateFinInscription = $dateFinInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPseudoOrganisateur(): ?string
    {
        return $this->pseudoOrganisateur;
    }

    public function setPseudoOrganisateur(string $pseudoOrganisateur): self
    {
        $this->pseudoOrganisateur = $pseudoOrganisateur;

        return $this;
    }

    public function getNomOrganisateur(): ?string
    {
        return $this->nomOrganisateur;
    }

    public function setNomOrganisateur(string $nomOrganisateur): self
    {
        $this->nomOrganisateur = $nomOrganisateur;

        return $this;
    }

    public function getPrenomOrganisateur(): ?string
    {
        return $this->prenomOrganisateur;
    }

    public function setPrenomOrganisateur(string $prenomOrganisateur): self
    {
        $this->prenomOrganisateur = $prenomOrganisateur;

        return $this;
    }

    public function getNomLieu(): ?string
    {
        return $this->nomLieu;
    }

    public function setNomLieu(string $nomLieu): self
    {
        $this->nomLieu = $nomLieu;

        return $this;
    }

    public function getCpVille(): ?string
    {
        return $this->cpVille;
    }

    public function setCpVille(string $cpVille): self
    {
        $this->cpVille = $cpVille;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(string $nomVille): self
    {
        $this->nomVille = $nomVille;

        return $this;
    }

    public function getNbParticipants(): ?int
    {
        return $this->nbParticipants;
    }

    public function setNbParticipants(?int $nbParticipants): self
    {
        $this->nbParticipants = $nbParticipants;

        return $this;
    }
}
