<?php

namespace App\Controller\Api\V1;

use App\Entity\V1\Recipe;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations\Delete;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Post;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Put;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    /**
     * @Route("/api/v1/recipes", name="api_v1_recipes", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @Get(
     *     path="/api/v1/recipes",
     *     summary="Return a list of the recipes",
     *     @\OpenApi\Annotations\Response(
     *          response="200",
     *          description="Return a list of the recipes",
     *          @JsonContent(
     *              @Property(
     *                  type="array",
     *                  property="recipes",
     *                  @Items(ref="#/components/schemas/Recipe")
     *              )
     *          )
     *     )
     * )
     *
     * @Post(
     *     path="/api/v1/recipes",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Put(
     *     path="/api/v1/recipes",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Delete(
     *     path="/api/v1/recipes",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
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
     *
     * @Post(
     *     path="/api/v1/recipes/create",
     *     summary="Add a new recipe",
     *     @RequestBody(
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     ),
     *     @\OpenApi\Annotations\Response(
     *          response="201",
     *          description="",
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     )
     * )
     *
     * @Get(
     *     path="/api/v1/recipes/create",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Put(
     *     path="/api/v1/recipes/create",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Delete(
     *     path="/api/v1/recipes/create",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
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
            JsonResponse::HTTP_CREATED
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
     *
     * @Post(
     *     path="/api/v1/recipes/{id}/update",
     *     summary="Update a recipe",
     *     @Parameter(
     *          name="id",
     *          in="path",
     *          description="Unique identifier of the recipe",
     *          required=true,
     *          @Schema(
     *              type="int",
     *              format="int32",
     *              example="1"
     *          )
     *     ),
     *     @RequestBody(
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     ),
     *     @\OpenApi\Annotations\Response(
     *          response="200",
     *          description="",
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     )
     * )
     * @Put(
     *     path="/api/v1/recipes/{id}/update",
     *     summary="Update a recipe",
     *     @Parameter(
     *          name="id",
     *          in="path",
     *          description="Unique identifier of the recipe",
     *          required=true,
     *          @Schema(
     *              type="int",
     *              format="int32",
     *              example="1"
     *          )
     *     ),
     *     @RequestBody(
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     ),
     *     @\OpenApi\Annotations\Response(
     *          response="200",
     *          description="",
     *          @JsonContent(ref="#/components/schemas/Recipe")
     *     )
     * )
     *
     * @Get(
     *     path="/api/v1/recipes/{id}/update",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Delete(
     *     path="/api/v1/recipes/{id}/update",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
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
     *
     * @Post(
     *     path="/api/v1/recipes/{id}/remove",
     *     summary="Remove a recipe",
     *     @Parameter(
     *          name="id",
     *          in="path",
     *          description="Unique identifier of the recipe",
     *          required=true,
     *          @Schema(
     *              type="int",
     *              format="int32",
     *              example="1"
     *          )
     *     ),
     *     @\OpenApi\Annotations\Response(response="204", description="Doesn't return content")
     * )
     * @Delete(
     *     path="/api/v1/recipes/{id}/remove",
     *     summary="Remove a recipe",
     *     @Parameter(
     *          name="id",
     *          in="path",
     *          description="Unique identifier of the recipe",
     *          required=true,
     *          @Schema(
     *              type="int",
     *              format="int32",
     *              example="1"
     *          )
     *     ),
     *     @\OpenApi\Annotations\Response(response="204", description="Doesn't return content")
     * )
     *
     * @Get(
     *     path="/api/v1/recipes/{id}/remove",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
     * @Put(
     *     path="/api/v1/recipes/{id}/remove",
     *     summary="Method not allowed to resource",
     *     @\OpenApi\Annotations\Response(response="405", description="Method not allowed to resource")
     * )
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
