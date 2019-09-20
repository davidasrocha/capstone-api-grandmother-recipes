<?php

namespace App\Serializer\Normalizer\Api\V1;

use App\Entity\V1\Ingredient;
use App\Entity\V1\Preparation;
use App\Entity\V1\Recipe;
use App\Repository\V1\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RecipeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $normalizer;

    /**
     * @var IngredientRepository
     */
    private $ingredientRepository;

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->ingredientRepository = $entityManager->getRepository(Ingredient::class);
    }

    /**
     * @param Recipe $object
     * @param null $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array()): array
    {
        $ingredients = $object
            ->getIngredients()
            ->map(
                function (Ingredient $ingredient) {
                    return "{$ingredient}";
                }
            )
            ->toArray();

        $preparations = $object
            ->getPreparations()
            ->map(
                function (Preparation $preparation) {
                    return "{$preparation}";
                }
            )
            ->toArray();

        /**
         * This approach help to solve the problem with circular reference
         */
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'ingredients' => $ingredients,
            'preparations' => $preparations,
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Recipe;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /**
         * @var Recipe $recipe
         */
        $recipe = new $type;
        $recipe->setName($data['name']);
        $recipe->setDescription($data['description']);

        if (array_key_exists('ingredients', $data)) {
            foreach ($data['ingredients'] as $ingredientName) {
                if (empty($ingredientName)) {
                    continue;
                }

                $ingredient = new Ingredient();
                $ingredient->setName($ingredientName);
                $recipe->addIngredient($ingredient);
            }
        }

        if (array_key_exists('preparations', $data)) {
            foreach ($data['preparations'] as $preparationDescription) {
                if (empty($preparationDescription)) {
                    continue;
                }

                $preparation = new Preparation();
                $preparation->setDescription($preparationDescription);
                $recipe->addPreparation($preparation);
            }
        }

        return $recipe;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Recipe::class;
    }
}
