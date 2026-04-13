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

    #[ORM\Column()]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'fotos')]
    private ?Akce $Akce = null;

    #[ORM\ManyToOne(inversedBy: 'fotos')]
    private ?Clanky $Clanky = null;


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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getAkce(): ?Akce
    {
        return $this->Akce;
    }

    public function setAkce(?Akce $Akce): static
    {
        $this->Akce = $Akce;

        return $this;
    }

    public function getClanky(): ?Clanky
    {
        return $this->Clanky;
    }

    public function setClanky(?Clanky $Clanky): static
    {
        $this->Clanky = $Clanky;

        return $this;
    }



}
