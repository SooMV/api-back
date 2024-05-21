<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['filmGroup'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Film::class, mappedBy: 'category')]
    private Collection $film;

    public function __construct() {
        $this->film = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['filmGroup'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    #[Groups(['filmGroup'])]
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
    public function addFilm(Film $film): self
    {
        if (!$this->film->contains($film)) {
            $this->film[] = $film;
        }

        return $this;
    }
    public function removeFilm(Film $film): self
    {
        $this->film->removeElement($film);

        return $this;
    }
}
