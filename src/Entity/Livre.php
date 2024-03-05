<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
// use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['listeGenreSimple','AS','NS','LS','LL'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: "/((\d{1}-)((\d|-){9})|(97[89]-)((\d|-){11}))(-[X0-9]){1}/", message: "Cet ISBN n'est pas valide")]
    #[Groups(['listeGenreSimple','AS','NS','ES','LS','LL'])]
    private ?string $isbn = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du livre ne peut être nul")]
    #[Assert\Length(min: 2, max: 100, minMessage: "Le titre du livre doit comporter au moins {{ limit }} caractères", maxMessage: "Le titre du livre doit comporter moins de {{ limit }} caractères")]
    #[Groups(['listeGenreSimple','AS','NS','ES','LS','LL'])]
    private ?string $titre = null;

    #[ORM\Column]
    #[Assert\Range(min: 5, max: 400, notInRangeMessage: "Le prix doit être compris entre {{ min }} et {{ max }} euros")]
    #[Groups(['listeGenreSimple','AS','NS','ES','LS','LL'])]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ES','LS'])]
    private ?Auteur $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AS','LS'])]
    private ?Editeur $editeur = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AS','ES','NS','LS'])]
    private ?Genre $genre = null;

    #[ORM\OneToMany(mappedBy: 'livre', targetEntity: Pret::class)]
    #[Groups(['AS','NS','LS'])]
    private Collection $prets;

    #[ORM\Column]
    #[Assert\Regex(pattern: "/1[0-9]{3}/", message: "Cet ISBN n'est pas valide")]
    // #[Assert\Expression(expression: "value < ")]
    #[Groups(['AS','ES','NS','LS','LL'])]
    private ?int $annee = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AS','ES','NS','LS','LL'])]
    private ?string $langue = null;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function setEditeur(?Editeur $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, Pret>
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): static
    {
        if (!$this->prets->contains($pret)) {
            $this->prets->add($pret);
            $pret->setLivre($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): static
    {
        if ($this->prets->removeElement($pret)) {
            // set the owning side to null (unless already changed)
            if ($pret->getLivre() === $this) {
                $pret->setLivre(null);
            }
        }

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): static
    {
        $this->langue = $langue;

        return $this;
    }
}
