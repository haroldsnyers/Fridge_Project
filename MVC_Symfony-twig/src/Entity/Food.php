<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FoodRepository")
 */
class Food
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiration_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Floor", inversedBy="floor")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_floor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_of_purchase;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_food_path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unit_qty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

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

    public function getIdFloor(): ?Floor
    {
        return $this->id_floor;
    }

    public function setIdFloor(?Floor $id_floor): self
    {
        $this->id_floor = $id_floor;

        return $this;
    }

    public function getDateOfPurchase(): ?\DateTimeInterface
    {
        return $this->date_of_purchase;
    }

    public function setDateOfPurchase(\DateTimeInterface $date_of_purchase): self
    {
        $this->date_of_purchase = $date_of_purchase;

        return $this;
    }

    public function getImageFoodPath(): ?string
    {
        return $this->image_food_path;
    }

    public function setImageFoodPath(string $image_food_path): self
    {
        $this->image_food_path = $image_food_path;

        return $this;
    }

    public function getUnitQty(): ?string
    {
        return $this->unit_qty;
    }

    public function setUnitQty(string $unit_qty): self
    {
        $this->unit_qty = $unit_qty;

        return $this;
    }

}
