<?php


namespace App\Controller\Main;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
            // sort by create_at
            ->orderBy('p.create_at', 'ASC')
            //get query
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

    /**
     * @Route("post/create", name="post_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request, Security $security)
    {
        // get user
        $user = $security->getUser();

        // create new post object
        $post = new Post();

        // create form
        $form = $this->createForm(PostType::class, $post);

        // handle request
        $form->handleRequest($request);

        // check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // get image from form
            $file = $form->get('image')->getData();

            // create post and save it
            $this->postRepository->setCreatePost($post, $file, $user);

            // add flash message
            $this->addFlash('success', 'post added');

            // return redirect
            return $this->redirectToRoute('home');
        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Create post';
        $forRender['form'] = $form->createView();

        // return render
        return $this->render('main/form.html.twig', $forRender);
    }

    /**
     * @Route("post/update/{id}", name="post_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(int $id, Request $request)
    {
        // get post object by id
        $post = $this->postRepository->getOnePost($id);

        // create form to change post
        $form = $this->createForm(PostType::class, $post);

        // get data from request
        $form->handleRequest($request);

        //  check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // check if the save button was pressed
            if ($form->get('save')->isClicked())
            {
                // get image from form
                $file = $form->get('image')->getData();

                // create post and save it
                $this->postRepository->setUpdatePost($post, $file);

                // add flash message
                $this->addFlash('success', 'post updated');
            }

            // check if the delete button was pressed
            if ($form->get('delete')->isClicked())
            {
                // delete post object
                $this->postRepository->setDeletePost($post);

                // add flash message
                $this->addFlash('success', 'post deleted');
            }

            // return redirect
            return $this->redirectToRoute('home');

        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Update post';
        $forRender['form'] = $form->createView();
        $forRender['post'] = $post;

        // return render
        return $this->render('admin/post/form.html.twig', $forRender);

    }

}
