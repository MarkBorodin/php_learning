<?php


namespace App\Controller\Admin;


use App\Controller\Admin\AdminBaseController;
use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Service\FileManagerServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    public function index()
    {
        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Posts';
        $forRender['post'] = $this->postRepository->getAllPost();
        $forRender['check_category'] = $this->categoryRepository->getAllCategory();

        // return render
        return $this->render('admin/post/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/create", name="admin_post_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
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
            $this->postRepository->setCreatePost($post, $file);

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