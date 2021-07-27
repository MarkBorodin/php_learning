<?php


namespace App\Controller\Admin;


use App\Controller\Admin\AdminBaseController;
use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Service\FileManagerServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminPostController extends AdminBaseController
{
    private $categoryRepository;
    private $postRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, PostRepositoryInterface $postRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/admin/post", name="admin_post")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        // get categoryId from form
        $categoryId = $request->request->get('category');

        // create context
        $forRender = parent::renderDefault();

        // get all categories and add them to context
        $categories = $this->categoryRepository->getAllCategory();
        $forRender['categories'] = $categories;

        // get manager
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Post entity (it is needed for pagination)
        $postsRepository = $em->getRepository(Post::class);

        // query if a category was selected
        if($categoryId) {
            // Find all the data on the Appointments table, filter your query as you need
            $allPostsQuery = $postsRepository->createQueryBuilder('p')
                ->where("p.category IN (:categoryId)")
                ->setParameter('categoryId', $categoryId)
                // sort by create_at
                ->orderBy('p.create_at', 'ASC')
                //get query
                ->getQuery();

            // get selected category
            $category = $this->categoryRepository->findBy(['id' => $categoryId]);
            // add it to context
            $forRender['category'] = $category;
        }

        // query if no category has been selected
        else
        {
            // Find all the data on the Appointments table, filter your query as you need
            $allPostsQuery = $postsRepository->createQueryBuilder('p')
                // sort by create_at
                ->orderBy('p.create_at', 'ASC')
                //get query
                ->getQuery();
        }

        // Paginate the results of the query
        $posts = $paginator->paginate(
        // Doctrine Query, not results
            $allPostsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );

        // add to context
        $forRender['posts'] = $posts;

        // return render
        return $this->render('admin/post/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/create", name="admin_post_create")
     * @param Request $request
     * @param Security $security
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
            return $this->redirectToRoute('admin_post');
        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Create post';
        $forRender['form'] = $form->createView();

        // return render
        return $this->render('admin/post/form.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/update/{id}", name="admin_post_update")
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
            return $this->redirectToRoute('admin_post');

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