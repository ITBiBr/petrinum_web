<?php

namespace App\Entity;

use App\Repository\AkceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: AkceRepository::class)]
class Akce implements FotoInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Perex = null;
    #[ORM\Column(length: 255)]
    private ?string $titulek = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;
    #[ORM\Column]
    private ?\DateTime $datum = null;

    #[ORM\Column]
    private ?\DateTime $datumZobrazeniOd = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datumDo = null;

    #[NotBlank(message: 'Content must not be blank.')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $obsah = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $obsahPokracovani= null;

    /**
     * @var Collection<int, Foto>
     */
    #[ORM\OneToMany(targetEntity: Foto::class, mappedBy: 'Akce', cascade: ['remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $fotos;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    /**
     * @var Collection<int, Stitky>
     */
    #[ORM\ManyToMany(targetEntity: Stitky::class, mappedBy: 'Akce')]
    private Collection $stitkies;

    public function __construct()
    {
        $this->fotos = new ArrayCollection();
        $this->datum = new \DateTime();
        $this->stitkies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerex(): ?string
    {
        return $this->Perex;
    }

    public function setPerex(?string $Perex): static
    {
        $this->Perex = $Perex;

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

    public function getDatum(): ?\DateTime
    {
        return $this->datum;
    }

    public function setDatum(\DateTime $datum): static
    {
        $this->datum = $datum;

        return $this;
    }

    public function getObsah(): ?string
    {
        return $this->obsah;
    }

    public function setObsah(?string $obsah): static
    {
        $this->obsah = $obsah;

        return $this;
    }

    public function getObsahPokracovani(): ?string
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
            $foto->setAktuality($this);
        }

        return $this;
    }

    public function removeFoto(Foto $foto): static
    {
        if ($this->fotos->removeElement($foto)) {
            // set the owning side to null (unless already changed)
            if ($foto->getAktuality() === $this) {
                $foto->setAktuality(null);
            }
        }

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

    /**
     * @return Collection<int, Stitky>
     */
    public function getStitkies(): Collection
    {
        return $this->stitkies;
    }

    public function addStitky(Stitky $stitky): static
    {
        if (!$this->stitkies->contains($stitky)) {
            $this->stitkies->add($stitky);
            $stitky->addAkce($this);
        }

        return $this;
    }

    public function removeStitky(Stitky $stitky): static
    {
        if ($this->stitkies->removeElement($stitky)) {
            $stitky->removeAkce($this);
        }

        return $this;
    }

    public function getDatumZobrazeniOd(): ?\DateTime
    {
        return $this->datumZobrazeniOd;
    }

    public function setDatumZobrazeniOd(\DateTime $datumZobrazeniOd): static
    {
        $this->datumZobrazeniOd = $datumZobrazeniOd;

        return $this;
    }

    public function getDatumDo(): ?\DateTime
    {
        return $this->datumDo;
    }

    public function setDatumDo(?\DateTime $datumDo): static
    {
        $this->datumDo = $datumDo;

        return $this;
    }
}
