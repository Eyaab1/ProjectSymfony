<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
#[ApiResource()]
class Administrateur extends User
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(choices: ["agent", "administrateur"], message: 'Choose a valid role (agent or administrateur).')]
    private ?string $role = null;
    public function __construct()
    {
        $this->setType('administrateur');
    }
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
}