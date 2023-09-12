<?php

namespace App\Entity;

use App\Repository\SalesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesRepository::class)]
class Sales
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $table_id = null;

    #[ORM\Column(length: 255)]
    private ?string $table_name = null;

    #[ORM\Column(length: 255)]
    private ?string $user_name = null;

    #[ORM\Column(type: Types::DECIMAL, options:["default" => 0], precision: 5, scale: 2, nullable: true)]
    private ?string $total_price = "0";

    #[ORM\Column(type: Types::DECIMAL, options:["default" => 0], precision: 5, scale: 2, nullable: true)]
    private ?string $total_received = "0";

    #[ORM\Column(type: Types::DECIMAL, options:["default" => 0], precision: 5, scale: 2, nullable: true)]
    private ?string $_change = "0" ;

    #[ORM\Column(length: 255, options:["default" => " "], nullable: true)]
    private ?string $payment_type = " ";

    #[ORM\Column(length: 255, options:["default" => "unpaid"], nullable: true)]
    private ?string $sale_status = "unpaid" ;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column]
    private ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableId(): ?string
    {
        return $this->table_id;
    }

    public function setTableId(string $table_id): self
    {
        $this->table_id = $table_id;

        return $this;
    }

    public function getTableName(): ?string
    {
        return $this->table_name;
    }

    public function setTableName(string $table_name): self
    {
        $this->table_name = $table_name;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getTotalReceived(): ?string
    {
        return $this->total_received;
    }

    public function setTotalReceived(string $total_received): self
    {
        $this->total_received = $total_received;

        return $this;
    }

    public function getChange(): ?string
    {
        return $this->_change;
    }

    public function setChange(string $_change): self
    {
        $this->_change = $_change;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->payment_type;
    }

    public function setPaymentType(string $payment_type): self
    {
        $this->payment_type = $payment_type;

        return $this;
    }

    public function getSaleStatus(): ?string
    {
        return $this->sale_status;
    }

    public function setSaleStatus(string $sale_status): self
    {
        $this->sale_status = $sale_status;

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
