<?php

namespace App\Entity;

use App\Repository\BasketProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketProductRepository::class)]
class BasketProduct
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'basketProducts')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private ?User $user = null;

  #[ORM\ManyToOne(inversedBy: 'basketProducts')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Product $product = null;

  #[ORM\Column]
  private ?int $quantity = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  public function getProduct(): ?Product
  {
    return $this->product;
  }

  public function setProduct(?Product $product): static
  {
    $this->product = $product;

    return $this;
  }

  public function getQuantity(): ?int
  {
    return $this->quantity;
  }

  public function setQuantity(int $quantity): static
  {
    $this->quantity = $quantity;

    return $this;
  }
}
