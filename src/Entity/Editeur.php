<?php

namespace App\Entity;

use App\Repository\EditeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EditeurRepository::class)]
#[UniqueEntity(fields: ["nom"], message: "L'editeur {{ value }} existe déjà")]
class Editeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['EL','ES','NS','AS','LS'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['EL','ES','NS','AS','LS'])]
    #[Assert\Length(min: 4, max: 50, minMessage: "Le nom de l'éditeur doit comporter au moins {{ limit }} caractères", maxMessage: "Le nom de l'éditeur doit comporter moins de {{ limit }} caractères")]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'editeur', targetEntity: Livre::class)]
    #[Groups(['ES','NS','AS'])]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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
            $livre->setEditeur($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getEditeur() === $this) {
                $livre->setEditeur(null);
            }
        }

        return $this;
    }
}
