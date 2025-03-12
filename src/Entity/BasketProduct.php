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

    #[ORM\ManyToOne(inversedBy: 'basketProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Basket $basketId = null;

    #[ORM\ManyToOne(inversedBy: 'basketProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productId = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasketId(): ?Basket
    {
        return $this->basketId;
    }

    public function setBasketId(?Basket $basketId): static
    {
        $this->basketId = $basketId;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(?Product $productId): static
    {
        $this->productId = $productId;

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
