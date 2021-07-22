<?php


namespace App\Controller\Main;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class HomeController extends BaseController
{
    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;

    /**
     * HomeController constructor.
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(PostRepository $postRepository, CategoryRepository $categoryRepository)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="home")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        // get context
        $forRender = parent::renderDefault();

        // get all categories and add the, to context
        $categories = $this->categoryRepository->getAllCategory();
        $forRender['categories'] = $categories;

        // get manager
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Post entity
        $postsRepository = $em->getRepository(Post::class);

        // Find all the data on the Appointments table, filter your query as you need
        $allPostsQuery = $postsRepository->createQueryBuilder('p')
            ->getQuery();

        // Paginate the results of the query
        $posts = $paginator->paginate(
        // Doctrine Query, not results
            $allPostsQuery,
        // Define the page parameter
            $request->query->getInt('page', 1),
        // Items per page
            1
        );

        // add to context
        $forRender['posts'] = $posts;

        // Render the twig view
        return $this->render('main/index.html.twig', $forRender);
    }

    public function indexByCategory(PaginatorInterface $paginator, Request $request)
    {

    }
}
