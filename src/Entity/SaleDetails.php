<?php

namespace App\Entity;

use App\Repository\SaleDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleDetailsRepository::class)]
class SaleDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $sale_id = null;

    #[ORM\Column]
    private ?int $menu_id = null;

    #[ORM\Column(length: 255)]
    private ?string $menu_name = null;

    #[ORM\Column]
    private ?int $menu_price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $edited_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = "Not Confirmed";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleId(): ?int
    {
        return $this->sale_id;
    }

    public function setSaleId(int $sale_id): self
    {
        $this->sale_id = $sale_id;

        return $this;
    }

    public function getMenuId(): ?int
    {
        return $this->menu_id;
    }

    public function setMenuId(int $menu_id): self
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    public function getMenuName(): ?string
    {
        return $this->menu_name;
    }

    public function setMenuName(string $menu_name): self
    {
        $this->menu_name = $menu_name;

        return $this;
    }

    public function getMenuPrice(): ?int
    {
        return $this->menu_price;
    }

    public function setMenuPrice(int $menu_price): self
    {
        $this->menu_price = $menu_price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->edited_at;
    }

    public function setEditedAt(\DateTimeImmutable $edited_at): self
    {
        $this->edited_at = $edited_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
