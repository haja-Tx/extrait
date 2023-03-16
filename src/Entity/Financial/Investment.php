<?php

namespace App\Entity\Financial;

use App\Entity\Property\Property;
use App\Entity\Security\User;
use App\Repository\Financial\InvestmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Property $property = null;

    #[ORM\Column]
    private ?int $partCount = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;


    public function __construct()
    {
        $this->status = 'init';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getPartCount(): ?int
    {
        return $this->partCount;
    }

    public function setPartCount(int $partCount): self
    {
        $this->partCount = $partCount;

        return $this;
    }

    public function increasePartsNumber(int $value): self
    {
        $this->partCount += $value;

        return $this;
    }

    public function decreasePartsNumber(int $value): self
    {
        $this->partCount -= $value;

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
}
