<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameProduct = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionProduct = null;

    #[ORM\Column]
    private ?int $priceProduct = null;

    #[ORM\Column]
    private ?int $quantityProduct = null;

    #[ORM\Column(length: 255)]
    private ?string $imageUrlProduct = null;

    #[ORM\Column(length: 200)]
    private ?string $typeProducts = null;

    #[ORM\ManyToOne(inversedBy: 'id')]
    private ?Categories $categories = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'idProduct')]
    private Collection $reviews;

    #[ORM\ManyToOne(inversedBy: 'idProduct')]
    private ?Users $users = null;

    /**
     * @var Collection<int, OrderItems>
     */
    #[ORM\OneToMany(targetEntity: OrderItems::class, mappedBy: 'productId')]
    private Collection $orderItems;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $productWeight = null;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?Users $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNameProduct(): ?string
    {
        return $this->nameProduct;
    }

    public function setNameProduct(string $nameProduct): static
    {
        $this->nameProduct = $nameProduct;

        return $this;
    }

    public function getDescriptionProduct(): ?string
    {
        return $this->descriptionProduct;
    }

    public function setDescriptionProduct(string $descriptionProduct): static
    {
        $this->descriptionProduct = $descriptionProduct;

        return $this;
    }

    public function getPriceProduct(): ?int
    {
        return $this->priceProduct;
    }

    public function setPriceProduct(int $priceProduct): static
    {
        $this->priceProduct = $priceProduct;

        return $this;
    }

    public function getQuantityProduct(): ?int
    {
        return $this->quantityProduct;
    }

    public function setQuantityProduct(int $quantityProduct): static
    {
        $this->quantityProduct = $quantityProduct;

        return $this;
    }

    public function getImageUrlProduct(): ?string
    {
        return $this->imageUrlProduct;
    }

    public function setImageUrlProduct(string $imageUrlProduct): static
    {
        $this->imageUrlProduct = $imageUrlProduct;

        return $this;
    }

    public function getTypeProducts(): ?string
    {
        return $this->typeProducts;
    }

    public function setTypeProducts(string $typeProducts): static
    {
        $this->typeProducts = $typeProducts;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setIdProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getIdProduct() === $this) {
                $review->setIdProduct(null);
            }
        }

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProductId($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProductId() === $this) {
                $orderItem->setProductId(null);
            }
        }

        return $this;
    }

    public function getProductWeight(): ?string
    {
        return $this->productWeight;
    }

    public function setProductWeight(?string $productWeight): static
    {
        $this->productWeight = $productWeight;

        return $this;
    }
}
