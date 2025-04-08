<?php

namespace App\Entity;

use App\Repository\OrderItemsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemsRepository::class)]
class OrderItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    private ?Orders $orderId = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    private ?Products $productId = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $priceOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Orders
    {
        return $this->orderId;
    }

    public function setOrderId(?Orders $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getProductId(): ?Products
    {
        return $this->productId;
    }

    public function setProductId(?Products $productId): static
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

    public function getPriceOrder(): ?int
    {
        return $this->priceOrder;
    }

    public function setPriceOrder(int $priceOrder): static
    {
        $this->priceOrder = $priceOrder;

        return $this;
    }
}
