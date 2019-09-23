<?php

namespace App\Controller\Api\V1;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations\Info;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiDocController extends AbstractController
{
    /**
     * @Route("/api/v1/doc", name="api_v1_doc", methods={"GET"})
     */
    public function index()
    {
        return $this->render('swagger/api-doc/index.html.twig');
    }

    /**
     * @Route("/api/v1/doc-json", name="api_v1_doc_json", methods={"GET"})
     *
     * @Info(
     *     title="Capstone API Grandmother Recipes",
     *     version="1.0"
     * )
     */
    public function docJson()
    {
        $openApi = \OpenApi\scan(dirname(realpath(__DIR__), 3));
        return $this->json(json_decode($openApi->toJson(), true));
    }
}
