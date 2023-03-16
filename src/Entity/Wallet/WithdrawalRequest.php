<?php

namespace App\Entity\Wallet;

use App\Entity\Financial\Transaction;
use App\Entity\Security\User;
use App\Repository\Wallet\WithdrawalRequestRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Monolog\DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: WithdrawalRequestRepository::class)]
class WithdrawalRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[AppAssert\WithdrawalAmount]
    private ?float $amount = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'withdrawalRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(2)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'withdrawalRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(2)]
    private ?WithdrawalMethod $method = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    private ?Transaction $transaction = null;

    public function __construct(){
        $this->status = 'init';
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMethod(): ?WithdrawalMethod
    {
        return $this->method;
    }

    public function setMethod(?WithdrawalMethod $method): self
    {
        $this->method = $method;

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

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

}
