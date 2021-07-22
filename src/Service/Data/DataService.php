<?php


namespace App\Service\Data;


use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;

class DataService
{
    private PostRepository $postRepository;

    /**
     * DataService constructor.
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getFilterResponse(Request $request): array
    {
        $categoryId = $request->get('categoryId');

//        return $this->postRepository->findBy(['category' => 'categoryId']);

        return $this->postRepository->getPostFilterJson((int)$categoryId);
    }
}