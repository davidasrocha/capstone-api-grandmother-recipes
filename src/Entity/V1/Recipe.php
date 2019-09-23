<?php

namespace App\Entity\V1;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;

/**
 * @ORM\Entity(repositoryClass="App\Repository\V1\RecipeRepository")
 *
 * @Schema(
 *     description="Recipe entity",
 *     title="Recipe entity",
 *     @JsonContent(schema="Recipe")
 * )
 */
class Recipe
{
    /**
     * @var int $id
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Property(format="int32", description="Unique identifier of the recipe")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=155, nullable=false)
     *
     * @Property(format="string", description="Name to identifier the recipe")
     */
    private $name;

    /**
     * @var string $description
     *
     * @ORM\Column(type="text", length=155, nullable=true)
     *
     * @Property(format="string", description="A short description about the recipe")
     */
    private $description;

    /**
     * @var Collection $ingredients
     *
     * @ORM\OneToMany(targetEntity="App\Entity\V1\Ingredient", mappedBy="recipe", cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     *
     * @Property(
     *     format="array",
     *     property="ingredients",
     *     description="A list of the ingredients to prepare the recipe",
     *     @Items(type="string")
     * )
     */
    private $ingredients;

    /**
     * @var Collection $preparations
     *
     * @ORM\OneToMany(targetEntity="App\Entity\V1\Preparation", mappedBy="recipe", cascade={"persist"}, fetch="EAGER", orphanRemoval=true)
     *
     * @Property(
     *     format="array",
     *     property="preparations",
     *     description="A list of the steps to prepare the recipe",
     *     @Items(type="string")
     * )
     */
    private $preparations;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->preparations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return Collection
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * @param Collection $ingredients
     */
    public function setIngredients(Collection $ingredients): void
    {
        foreach ($ingredients as $ingredient) {
            $this->addIngredient($ingredient);
        }
    }

    /**
     * @param Ingredient $ingredient
     */
    public function addIngredient(Ingredient $ingredient)
    {
        if (!$ingredient instanceof Ingredient) {
            return;
        }

        if ($this->ingredients->contains($ingredient)) {
            return;
        }

        $ingredient->setRecipe($this);

        $this->ingredients->add($ingredient);
    }

    /**
     * @return Collection
     */
    public function getPreparations(): Collection
    {
        return $this->preparations;
    }

    /**
     * @param Collection $preparations
     */
    public function setPreparations(Collection $preparations): void
    {
        /**
         * @var Preparation $preparation
         */
        foreach ($preparations as $k => $preparation) {
            $this->addPreparation($preparation);
        }
    }

    /**
     * @param Preparation $preparation
     */
    public function addPreparation(Preparation $preparation)
    {
        if (!$preparation instanceof Preparation) {
            return;
        }

        if ($this->preparations->contains($preparation)) {
            return;
        }

        $step = $this->generateNewPreparationStep();
        $preparation->setStep($step);
        $preparation->setRecipe($this);

        $this->preparations->add($preparation);
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    private function generateNewPreparationStep(): int
    {
        $step = 1;

        /**
         * @var Preparation $preparation
         */
        foreach ($this->preparations as $preparation) {
            if ($preparation->getStep() >= $step) {
                $step = $preparation->getStep() + 1;
            }
        }

        return $step;
    }
}
