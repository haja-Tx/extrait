<?php

namespace App\Entity\Property;

use App\Entity\Financial\Investment;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\ThumbnailableTrait;
use Doctrine\Common\Collections\Collection;
use App\Repository\Property\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[Vich\Uploadable]
class Property
{

    use ThumbnailableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $estimatePrice = null;

    #[ORM\Column]
    private ?float $partPrice = null;

    #[ORM\Column]
    private ?float $rateOfReturn = null;

    #[ORM\Column]
    private ?float $plusValue = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Specificity::class)]
    private Collection $specificities;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Picture::class, cascade:["persist"])]
    private Collection $pictures;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Investment::class, orphanRemoval: true, fetch: "EAGER")]
    private Collection $investments;
    
    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Feature::class,cascade:["persist"], orphanRemoval: true)]
    private Collection $features;

    #[ORM\Column(nullable: true)]
    private ?float $partSellingPrice = null;

    
    public function __construct()
    {
        $this->specificities = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->investments = new ArrayCollection();
        $this->features = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEstimatePrice(): ?float
    {
        return $this->estimatePrice;
    }

    public function setEstimatePrice(float $estimatePrice): self
    {
        $this->estimatePrice = $estimatePrice;

        return $this;
    }

    public function getPartPrice(): ?float
    {
        return $this->partPrice;
    }

    public function setPartPrice(float $partPrice): self
    {
        $this->partPrice = $partPrice;

        return $this;
    }

    public function getRateOfReturn(): ?float
    {
        return $this->rateOfReturn;
    }

    public function setRateOfReturn(float $rateOfReturn): self
    {
        $this->rateOfReturn = $rateOfReturn;

        return $this;
    }

    public function getPlusValue(): ?float
    {
        return $this->plusValue;
    }

    public function setPlusValue(float $plusValue): self
    {
        $this->plusValue = $plusValue;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Specificity>
     */
    public function getSpecificities(): Collection
    {
        return $this->specificities;
    }

    public function addSpecificity(Specificity $specificity): self
    {
        if (!$this->specificities->contains($specificity)) {
            $this->specificities->add($specificity);
            $specificity->setProperty($this);
        }

        return $this;
    }

    public function removeSpecificity(Specificity $specificity): self
    {
        if ($this->specificities->removeElement($specificity)) {
            // set the owning side to null (unless already changed)
            if ($specificity->getProperty() === $this) {
                $specificity->setProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProperty($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getProperty() === $this) {
                $picture->setProperty(null);
            }
        }

        return $this;
    }

    
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Investment>
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    public function addInvestment(Investment $investment): self
    {
        if (!$this->investments->contains($investment)) {
            $this->investments->add($investment);
            $investment->setProperty($this);
        }

        return $this;
    }

    /** 
     * @return Collection<int, Feature>
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
            $feature->setProperty($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): self
    {
        if ($this->investments->removeElement($investment)) {
            // set the owning side to null (unless already changed)
            if ($investment->getProperty() === $this) {
                $investment->setProperty(null);
            }
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getProperty() === $this) {
                $feature->setProperty(null);
            }
        }

        return $this;
    }

    public function getPartSellingPrice(): ?float
    {
        return $this->partSellingPrice;
    }

    public function setPartSellingPrice(?float $partSellingPrice): self
    {
        $this->partSellingPrice = $partSellingPrice;

        return $this;
    }
}
