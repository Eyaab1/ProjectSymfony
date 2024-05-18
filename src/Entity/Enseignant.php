<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
#[ApiResource()]
class Enseignant extends User
{
    #[ORM\Column(length: 255)]
    private ?string $codeENS = null;

    #[ORM\Column]
    private ?int $nbAnneeExp = null;

    #[ORM\Column(length: 255)]
    private ?string $matiere = null;

    #[ORM\ManyToMany(targetEntity: Classe::class, mappedBy: "enseignants")]
    private Collection $classes;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->setType('enseignant');
    }

    public function getCodeENS(): ?string
    {
        return $this->codeENS;
    }

    public function setCodeENS(string $codeENS): self
    {
        $this->codeENS = $codeENS;
        return $this;
    }

    public function getNbAnneeExp(): ?int
    {
        return $this->nbAnneeExp;
    }

    public function setNbAnneeExp(int $nbAnneeExp): self
    {
        $this->nbAnneeExp = $nbAnneeExp;
        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }

    /**
     * @return Collection|Classe[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClasse(Classe $classe): self
    {
        if (!$this->classes->contains($classe)) {
            $this->classes[] = $classe;
        }

        return $this;
    }

    public function removeClasse(Classe $classe): self
    {
        $this->classes->removeElement($classe);

        return $this;
    }
}
