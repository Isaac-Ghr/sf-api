<?php

namespace App\Entity;

use App\Repository\NationaliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NationaliteRepository::class)]
#[UniqueEntity(fields: ["libelle"], message: "La nationalité {{ value }} existe déjà")]
class Nationalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['NL','NS','AS'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['NL','NS','AS'])]
    #[Assert\Length(min: 4, max: 50, minMessage: "Le nom de la nationalité doit comporter au moins {{ limit }} caractères", maxMessage: "Le nom de la nationalité doit comporter moins de {{ limit }} caractères")]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'nationalite', targetEntity: Auteur::class, orphanRemoval: false)]
    #[Groups(['NS'])]
    private Collection $auteurs;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
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
     * @return Collection<int, Auteur>
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
            $auteur->setNationalite($this);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        if ($this->auteurs->removeElement($auteur)) {
            // set the owning side to null (unless already changed)
            if ($auteur->getNationalite() === $this) {
                $auteur->setNationalite(null);
            }
        }

        return $this;
    }
}
