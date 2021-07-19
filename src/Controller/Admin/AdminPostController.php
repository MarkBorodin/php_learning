<?php


namespace App\Controller\Admin;


use App\Controller\Admin\AdminBaseController;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostController extends AdminBaseController
{
    /**
     * @Route("/admin/post", name="admin_post")
     */
    public function index()
    {
        // get all posts
        $post = $this->getDoctrine()->getRepository(Post::class)->findAll();

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Posts';
        $forRender['post'] = $post;

        // return render
        return $this->render('admin/post/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/create", name="admin_post_create")
     */
    public function create(Request $request)
    {
        // create manager
        $em = $this->getDoctrine()->getManager();

        // create new post object
        $post = new Post();

        // create form
        $form = $this->createForm(PostType::class, $post);

        // handle request
        $form->handleRequest($request);

        // check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // set values
            $post->setCreateAtValue();
            $post->setUpdateAtValue();
            $post->setIsPublished();

            // save data using manage ($em)
            $em->persist($post);
            $em->flush();

            // add flash message
            $this->addFlash('success', 'post added');

            // return redirect
            return $this->redirectToRoute('admin_post');
        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Create post';
        $forRender['form'] = $form->createView();

//        return

    }
}