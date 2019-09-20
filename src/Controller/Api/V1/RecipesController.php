<?php

namespace App\Controller\Api\V1;

use App\Entity\V1\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    /**
     * @Route("/api/v1/recipes", name="api_v1_recipes")
     */
    public function index()
    {
        $recipes = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

        return $this->json([
            'recipes' => $recipes
        ]);
    }

    /**
     * @Route("/api/v1/recipes/create", name="api_v1_recipes_create", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     */
    public function create(Request $request, SerializerInterface $serializer)
    {
        $content = $request->getContent();

        $recipe = $serializer->deserialize(
            $content,
            Recipe::class,
            'json',
            [
                'allow_extra_attributes' => false
            ]
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return $this->json(
            $recipe,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/api/v1/recipes/{id}/update", name="api_v1_recipes_update", methods={"POST","PUT"})
     *
     * @param int $id
     * @param Request $request
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     */
    public function update(int $id, Request $request, SerializerInterface $serializer)
    {
        /**
         * @var Recipe $recipe
         */
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id);

        $content = $request->getContent();

        /**
         * @var Recipe $recipeData
         */
        $recipeData = $serializer->deserialize(
            $content,
            Recipe::class,
            'json',
            [
                'allow_extra_attributes' => false
            ]
        );

        $recipe->setName($recipeData->getName());
        $recipe->setDescription($recipeData->getDescription());

        $recipe->getIngredients()->clear();
        $recipe->setIngredients($recipeData->getIngredients());

        $recipe->getPreparations()->clear();
        $recipe->setPreparations($recipeData->getPreparations());

        $em = $this->getDoctrine()->getManager();
        $em->persist($recipe);
        $em->flush();

        return $this->json(
            $recipe,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/api/v1/recipes/{id}/remove", name="api_v1_recipes_remove", methods={"POST","DELETE"})
     *
     * @param int $id
     *
     * @return Response
     */
    public function remove(int $id)
    {
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($recipe);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
