<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[ApiResource()]
class Etudiant extends User
{
    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: "etudiants")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    public function __construct()
    {
        parent::__construct();
        $this->setType('etudiant');
    }
    public function __toString()
    {
        return $this->getNom(); 
    }
    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): self
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }
}
