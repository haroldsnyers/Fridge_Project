<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FridgeRepository")
 */
class Fridge
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
     * @ORM\Column(type="integer")
     */
    private $nbr_floors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Floor", mappedBy="id_fridge", orphanRemoval=true)
     */
    private $fridge;

    public function __construct()
    {
        $this->fridge = new ArrayCollection();
    }

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

    public function getNbrFloors(): ?int
    {
        return $this->nbr_floors;
    }

    public function setNbrFloors(int $nbr_floors): self
    {
        $this->nbr_floors = $nbr_floors;

        return $this;
    }

    /**
     * @return Collection|Floor[]
     */
    public function getFridge(): Collection
    {
        return $this->fridge;
    }

    public function addFridge(Floor $fridge): self
    {
        if (!$this->fridge->contains($fridge)) {
            $this->fridge[] = $fridge;
            $fridge->setIdFridge($this);
        }

        return $this;
    }

    public function removeFridge(Floor $fridge): self
    {
        if ($this->fridge->contains($fridge)) {
            $this->fridge->removeElement($fridge);
            // set the owning side to null (unless already changed)
            if ($fridge->getIdFridge() === $this) {
                $fridge->setIdFridge(null);
            }
        }

        return $this;
    }
}
