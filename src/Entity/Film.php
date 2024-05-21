<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, name: "title")]
    private ?string $title = null;

    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, name: "duration")]
    private ?int $duration = null;

    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, name: "realisateur_first_name")]
    private ?string $realisateur_firstName = null;

    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, name: "realisateur_last_name")]
    private ?string $realisateur_lastName = null;
    
    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, name: "release_year")]
    private ?int $release_year = null;

    /**
     * @var Collection<int, Category>
     */
    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'film')]
    #[ORM\JoinTable(name:"film_category")]
    private Collection $category;



    /**
     * @var Collection<int, Commentaire>
     */
    #[Groups(['filmGroup'])]
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'film', cascade:["remove"])]
    private Collection $commentaires;

    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;
    
    #[Vich\UploadableField(mapping: 'film_image', fileNameProperty: 'imageName', size: 'imageSize')]
    
    #[Groups(['filmGroup', 'filmUpdate'])]
    private ?File $imageFile = null;



    #[Groups(['filmGroup', 'filmUpdate'])]
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Like>
     */
    #[Groups(['filmGroup'])]
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'film')]
    private Collection $likes;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['filmGroup', 'filmUpdate'])]
    private ?string $description = null;

  

    public function __construct()
    {
        $this->category = new ArrayCollection();
        
        $this->commentaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
     
    }

    #[Groups(['filmGroup'])]
    public function getId(): ?int
    {
        return $this->id;
    }
    #[Groups(['filmGroup'])]
    public function getTitle(): ?string
    {
        return $this->title;
    }

    #[Groups(['filmGroup'])]
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
    #[Groups(['filmGroup'])]
    public function getDuration(): ?int
    {
        return $this->duration;
    }
    #[Groups(['filmGroup'])]
    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
    #[Groups(['filmGroup'])]
    public function getRealisateurFirstName(): ?string
    {
        return $this->realisateur_firstName;
    }
    #[Groups(['filmGroup'])]
    public function setRealisateurFirstName(string $realisateur_firstName): static
    {
        $this->realisateur_firstName = $realisateur_firstName;

        return $this;
    }
    #[Groups(['filmGroup'])]
    public function getRealisateurLastName(): ?string
    {
        return $this->realisateur_lastName;
    }
    #[Groups(['filmGroup'])]
    public function setRealisateurLastName(string $realisateur_lastName): static
    {
        $this->realisateur_lastName = $realisateur_lastName;

        return $this;
    }
    #[Groups(['filmGroup'])]
    public function getReleaseYear(): ?int
    {
        return $this->release_year;
    }
    #[Groups(['filmGroup'])]
    public function setReleaseYear(int $release_year): static
    {
        $this->release_year = $release_year;

        return $this;
    }

    /**
 * @return Collection<int, Category>
 */
    #[Groups(['filmGroup'])]
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
            $category->addFilm($this); 
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->removeElement($category)) {
            $category->removeFilm($this); 
        }

        return $this;
    }

    

    /**
     * @return Collection<int, Commentaire>
     */
    #[Groups(['filmGroup', 'commentGroup'])]
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setFilm($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getFilm() === $this) {
                $commentaire->setFilm(null);
            }
        }

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
            $like->setFilm($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getFilm() === $this) {
                $like->setFilm(null);
            }
        }

        return $this;
    }

    #[Groups(['filmGroup'])]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(['filmGroup'])]
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

}
