<?php

namespace App\Entity;

use App\Repository\FotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FotoRepository::class)]
class Foto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nazev = null;

    #[ORM\Column(length: 255)]
    private ?string $soubor = null;

    #[ORM\ManyToOne(inversedBy: 'fotos')]
    private ?Galerie $Galerie = null;


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

    public function getSoubor(): ?string
    {
        return $this->soubor;
    }

    public function setSoubor(string $soubor): static
    {
        $this->soubor = $soubor;

        return $this;
    }

    public function getGalerie(): ?Galerie
    {
        return $this->Galerie;
    }

    public function setGalerie(?Galerie $Galerie): static
    {
        $this->Galerie = $Galerie;

        return $this;
    }



}
