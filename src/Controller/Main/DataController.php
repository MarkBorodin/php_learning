<?php


namespace App\Controller\Main;


use App\Service\Data\DataService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataController extends BaseController
{
    private DataService $dataService;

    /**
     * DataController constructor.
     * @param DataService $dataService
     */
    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function getFilterPostAction(Request $request): JsonResponse
    {
        $response = $this->dataService->getFilterResponse($request);

        return $this->json($response);
    }
}