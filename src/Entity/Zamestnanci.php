<?php

namespace App\Entity;

use App\Repository\ZamestnanciRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZamestnanciRepository::class)]
class Zamestnanci
{
    public function __construct()
    {
        $this->setPoradi(0);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $jmeno = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\ManyToOne(inversedBy: 'zamestnancis')]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?ZamestnanciKategorie $ZamestnanciKategorie = null;

    #[ORM\Column]
    private ?int $poradi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJmeno(): ?string
    {
        return $this->jmeno;
    }

    public function setJmeno(string $jmeno): static
    {
        $this->jmeno = $jmeno;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getZamestnanciKategorie(): ?ZamestnanciKategorie
    {
        return $this->ZamestnanciKategorie;
    }

    public function setZamestnanciKategorie(?ZamestnanciKategorie $ZamestnanciKategorie): static
    {
        $this->ZamestnanciKategorie = $ZamestnanciKategorie;

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
}
