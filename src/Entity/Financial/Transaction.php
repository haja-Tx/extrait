<?php

namespace App\Entity\Financial;

use App\Entity\Security\User;
use App\Repository\Financial\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{

    public const TYPE_DEPOSIT = 'deposit'; // Dépot
    public const TYPE_PART_PURCHASES = 'part_purchases'; // Achat de briques
    public const TYPE_PART_SALES = 'part_sales'; // Vente de briques
    public const TYPE_REVENUE = 'revenue'; // Revenue d'argent
    public const TYPE_WITHDRAWAL = 'withdrawal'; // Retrait
    public const TYPE_FIXING_ADJUSTMENT = 'fixing_adjustment'; // Ajustement correctif
    public const TYPE_PROPERTY_SALES = 'property_sales'; // Plus value de vente

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 5)]
    private ?string $direction = null;

    #[ORM\Column(nullable: true)]
    private array $description = [];

    #[ORM\Column(length: 75)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(?array $description): self
    {
        $this->description = $description;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
