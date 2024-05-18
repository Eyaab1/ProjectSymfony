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

     /**
     * @ORM\ManyToMany(targetEntity=Classe::class, mappedBy="enseignants")
     */
    private Collection $classes;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
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
     * @return Collection<int, Classe>
     */
    public function getIdClasses(): Collection
    {
        return $this->classes;
    }

    public function addIdClass(Classe $idClass): self
    {
        if (!$this->classes->contains($idClass)) {
            $this->classes->add($idClass);
        }

        return $this;
    }

    public function removeIdClass(Classe $idClass): self
    {
        $this->classes->removeElement($idClass);

        return $this;
    }
}