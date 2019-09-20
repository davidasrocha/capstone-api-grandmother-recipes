<?php

namespace App\Entity\V1;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\V1\PreparationRepository")
 */
class Preparation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $description
     *
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $description;

    /**
     * @var int $step
     *
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    private $step;

    /**
     * @var Recipe $recipe
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\V1\Recipe", inversedBy="preparations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->step;
    }

    /**
     * @param int $step
     */
    public function setStep(int $step): void
    {
        $this->step = $step;
    }

    /**
     * @return Recipe
     */
    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    /**
     * @param Recipe $recipe
     */
    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function __toString()
    {
        return $this->description;
    }
}
