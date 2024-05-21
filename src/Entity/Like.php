<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    #[Groups(['likeGroup'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Film $film = null;

    #[ORM\ManyToOne(targetEntity: Commentaire::class, inversedBy: 'likes')]
    private ?Commentaire $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(name:"film_id", referencedColumnName:"id", onDelete:"CASCADE")]
    private ?User $user = null;

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }

    public function getCommentaire(): ?Commentaire
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaire $commentaire): static
    {
        $this->commentaire = $commentaire;

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

   

  

   
}
