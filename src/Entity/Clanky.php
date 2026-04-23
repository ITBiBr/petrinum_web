<?php

namespace App\Entity;

use App\Repository\ClankyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ClankyRepository::class)]
class Clanky implements FotoInterface, PrilohyInterface
{
    public function __toString(): string
    {
        return $this->getTitulek();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[NotBlank(message: 'Content must not be blank.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $obsah = null;

    #[ORM\Column(length: 255)]
    private ?string $titulek = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $obsahPokracovani = null;

    /**
     * @var Collection<int, Foto>
     */
    #[ORM\OneToMany(targetEntity: Foto::class, mappedBy: 'Clanky', cascade: ['remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $fotos;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\OneToMany(targetEntity: Menu::class, mappedBy: 'Clanky', cascade: ['remove'], orphanRemoval: true)]

    private Collection $menus;

    #[ORM\Column]
    private ?bool $isZobrazitTitulek = null;

    /**
     * @var Collection<int, Prilohy>
     */
    #[ORM\OneToMany(targetEntity: Prilohy::class, mappedBy: 'Clanky', cascade: ['remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $prilohies;

    public function __construct()
    {
        $this->isZobrazitTitulek = true;
        $this->fotos = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->prilohies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObsah(): ?string
    {
        return $this->obsah;
    }

    public function setObsah(string $obsah): static
    {
        $this->obsah = $obsah;

        return $this;
    }

    public function getTitulek(): ?string
    {
        return $this->titulek;
    }

    public function setTitulek(string $titulek): static
    {
        $this->titulek = $titulek;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

   public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getobsahPokracovani(): ?string
    {
        return $this->obsahPokracovani;
    }

    public function setObsahPokracovani(?string $obsahPokracovani): static
    {
        $this->obsahPokracovani = $obsahPokracovani;

        return $this;
    }

    /**
     * @return Collection<int, Foto>
     */
    public function getFotos(): Collection
    {
        return $this->fotos;
    }

    public function addFoto(Foto $foto): static
    {
        if (!$this->fotos->contains($foto)) {
            $this->fotos->add($foto);
            $foto->setClanky($this);
        }

        return $this;
    }

    public function removeFoto(Foto $foto): static
    {
        if ($this->fotos->removeElement($foto)) {
            // set the owning side to null (unless already changed)
            if ($foto->getClanky() === $this) {
                $foto->setClanky(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->setClanky($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        if ($this->menus->removeElement($menu)) {
            // set the owning side to null (unless already changed)
            if ($menu->getClanky() === $this) {
                $menu->setClanky(null);
            }
        }

        return $this;
    }

    public function isZobrazitTitulek(): ?bool
    {
        return $this->isZobrazitTitulek;
    }

    public function setIsZobrazitTitulek(bool $isZobrazitTitulek): static
    {
        $this->isZobrazitTitulek = $isZobrazitTitulek;

        return $this;
    }

    /**
     * @return Collection<int, Prilohy>
     */
    public function getPrilohies(): Collection
    {
        return $this->prilohies;
    }

    public function addPrilohy(Prilohy $prilohy): static
    {
        if (!$this->prilohies->contains($prilohy)) {
            $this->prilohies->add($prilohy);
            $prilohy->setClanky($this);
        }

        return $this;
    }

    public function removePrilohy(Prilohy $prilohy): static
    {
        if ($this->prilohies->removeElement($prilohy)) {
            // set the owning side to null (unless already changed)
            if ($prilohy->getClanky() === $this) {
                $prilohy->setClanky(null);
            }
        }

        return $this;
    }
}
