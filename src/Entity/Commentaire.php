<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $published_at = null;


    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Film $film = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commentaires')]
    private ?User $user = null;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'commentaire', cascade:['remove'])]
    private Collection $likes;

    #[ORM\Column(type:'text', nullable: true)]
    #[Groups(['commentGroup'])]
    private ?string $content = null;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    #[Groups(['commentGroup'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['commentGroup'])]
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    #[Groups(['commentGroup'])]
    public function setPublishedAt(\DateTimeImmutable $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }


    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCommentaire($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCommentaire() === $this) {
                $like->setCommentaire(null);
            }
        }

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

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
}
