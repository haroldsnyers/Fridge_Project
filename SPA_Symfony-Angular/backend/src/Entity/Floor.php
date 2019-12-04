<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FloorRepository")
 */
class Floor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $qty_food;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fridge", inversedBy="fridge")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_fridge;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Food", mappedBy="id_floor", orphanRemoval=true)
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->floor = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?array
    {
        return $this->type;
    }

    public function setType(array $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQtyFood(): ?string
    {
        return $this->qty_food;
    }

    public function setQtyFood(string $qty_food): self
    {
        $this->qty_food = $qty_food;

        return $this;
    }

    public function getIdFridge(): ?Fridge
    {
        return $this->id_fridge;
    }

    public function setIdFridge(?Fridge $id_fridge): self
    {
        $this->id_fridge = $id_fridge;

        return $this;
    }

    /**
     * @return Collection|Food[]
     */
    public function getFloor(): Collection
    {
        return $this->floor;
    }

    public function addFloor(Food $floor): self
    {
        if (!$this->floor->contains($floor)) {
            $this->floor[] = $floor;
            $floor->setIdFloor($this);
        }

        return $this;
    }

    public function removeFloor(Food $floor): self
    {
        if ($this->floor->contains($floor)) {
            $this->floor->removeElement($floor);
            // set the owning side to null (unless already changed)
            if ($floor->getIdFloor() === $this) {
                $floor->setIdFloor(null);
            }
        }

        return $this;
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

    // needed for form
    public function __toString()
    {
        $name = strval($this->getName());
        $type = strval($this->getType());
        $string = $name."-".$type;
        return $string;
    }
}
