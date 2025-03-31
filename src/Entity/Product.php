<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[OA\Schema()]
class Product
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[OA\Property(type: "string", example: "Céréales bio")]
  #[Groups(['product:read'])]
  private ?string $name = null;

  #[ORM\Column]
  #[OA\Property(type: "number", format: "float", example: 19.99, description: "Prix du produit")]
  #[Groups(['product:read'])]
  private ?float $price = null;

  #[ORM\Column(length: 255)]
  #[OA\Property(type: "string", maxLength: 255, example: "Un bol de céréales", description: "Courte description du produit")]
  #[Groups(['product:read'])]
  private ?string $shortDescription = null;

  #[ORM\Column(type: Types::TEXT)]
  #[OA\Property(type: "string", example: "Ces céréales bio sont fabriquées à partir de flocons d'avoine...", description: "Description détaillée du produit")]
  #[Groups(['product:read'])]
  private ?string $longDescription = null;

  #[ORM\Column(nullable: true)]
  private ?float $deliveryCharges = null;

  /**
   * @var Collection<int, BasketProduct>
   */
  #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'productId', orphanRemoval: true)]
  private Collection $basketProducts;

  #[ORM\Column(nullable: true)]
  private ?int $stock = null;

  #[ORM\Column(length: 255)]
  #[OA\Property(type: "string", example: "https://greengoddies.com/images/céréales.jpg", description: "URL de l'image du produit")]
  #[Groups(['product:read'])]
  private ?string $image = null;

  public function __construct()
  {
    $this->basketProducts = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function getPrice(): ?float
  {
    return $this->price;
  }

  public function setPrice(float $price): static
  {
    $this->price = $price;

    return $this;
  }

  public function getShortDescription(): ?string
  {
    return $this->shortDescription;
  }

  public function setShortDescription(string $shortDescription): static
  {
    $this->shortDescription = $shortDescription;

    return $this;
  }

  public function getLongDescription(): ?string
  {
    return $this->longDescription;
  }

  public function setLongDescription(string $longDescription): static
  {
    $this->longDescription = $longDescription;

    return $this;
  }

  public function getDeliveryCharges(): ?float
  {
    return $this->deliveryCharges;
  }

  public function setDeliveryCharges(?float $deliveryCharges): static
  {
    $this->deliveryCharges = $deliveryCharges;

    return $this;
  }

  /**
   * @return Collection<int, BasketProduct>
   */
  public function getBasketProducts(): Collection
  {
    return $this->basketProducts;
  }

  public function addBasketProduct(BasketProduct $basketProduct): static
  {
    if (!$this->basketProducts->contains($basketProduct)) {
      $this->basketProducts->add($basketProduct);
      $basketProduct->setProduct($this);
    }

    return $this;
  }

  public function removeBasketProduct(BasketProduct $basketProduct): static
  {
    if ($this->basketProducts->removeElement($basketProduct)) {
      // set the owning side to null (unless already changed)
      if ($basketProduct->getProduct() === $this) {
        $basketProduct->setProduct(null);
      }
    }

    return $this;
  }

  public function getStock(): ?int
  {
    return $this->stock;
  }

  public function setStock(?int $stock): static
  {
    $this->stock = $stock;

    return $this;
  }

  public function getImage(): ?string
  {
    return $this->image;
  }

  public function setImage(string $image): static
  {
    $this->image = $image;

    return $this;
  }
}
