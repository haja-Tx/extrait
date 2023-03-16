<?php

namespace App\Entity\Property;

use App\Entity\Traits\ThumbnailableTrait;
use App\Repository\Property\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[ORM\Table(name: 'property_picture')]
#[Vich\Uploadable]
class Picture
{
    use ThumbnailableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    private ?Property $property = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

}
