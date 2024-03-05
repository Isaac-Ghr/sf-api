<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[UniqueEntity(fields: ["libelle"], message: "Le genre {{ value }} existe déjà")]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['listeGenreSimple','ES','NS','LS'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['listeGenreSimple','ES','NS','LS'])]
    #[Assert\Length( min:2, max:32, minMessage:"Le libelle saisi doit comporter au moins {{ limit }} caractères", maxMessage:"Le libelle saisi ne doit pas dépasser {{ limit }} caractères")]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: Livre::class, orphanRemoval: false)]
    // #[Groups(['listeGenreSimple'])]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setGenre($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getGenre() === $this) {
                $livre->setGenre(null);
            }
        }

        return $this;
    }
}
