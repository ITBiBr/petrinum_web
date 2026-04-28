<?php

namespace App\Entity;

use App\Repository\ZamestnanciKategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZamestnanciKategorieRepository::class)]
class ZamestnanciKategorie
{
    public function __toString(): string
    {
        return $this->getNazev();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nazev = null;

    #[ORM\Column]
    private ?int $poradi = null;

    /**
     * @var Collection<int, Zamestnanci>
     */
    #[ORM\OneToMany(targetEntity: Zamestnanci::class, mappedBy: 'ZamestnanciKategorie')]
    #[ORM\OrderBy(['poradi' => 'ASC'])]
    private Collection $zamestnancis;

    public function __construct()
    {
        $this->zamestnancis = new ArrayCollection();
        $this->setPoradi(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazev(): ?string
    {
        return $this->nazev;
    }

    public function setNazev(string $nazev): static
    {
        $this->nazev = $nazev;

        return $this;
    }

    public function getPoradi(): ?int
    {
        return $this->poradi;
    }

    public function setPoradi(int $poradi): static
    {
        $this->poradi = $poradi;

        return $this;
    }

    /**
     * @return Collection<int, Zamestnanci>
     */
    public function getZamestnancis(): Collection
    {
        return $this->zamestnancis;
    }

    public function addZamestnanci(Zamestnanci $zamestnanci): static
    {
        if (!$this->zamestnancis->contains($zamestnanci)) {
            $this->zamestnancis->add($zamestnanci);
            $zamestnanci->setZamestnanciKategorie($this);
        }

        return $this;
    }

    public function removeZamestnanci(Zamestnanci $zamestnanci): static
    {
        if ($this->zamestnancis->removeElement($zamestnanci)) {
            // set the owning side to null (unless already changed)
            if ($zamestnanci->getZamestnanciKategorie() === $this) {
                $zamestnanci->setZamestnanciKategorie(null);
            }
        }

        return $this;
    }
}
