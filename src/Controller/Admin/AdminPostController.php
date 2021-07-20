<?php


namespace App\Controller\Admin;


use App\Controller\Admin\AdminBaseController;
use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileManagerServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        // get all posts categories (for check)
        $checkCategory = $this->getDoctrine()->getRepository(Category::class)->findAll();

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Posts';
        $forRender['post'] = $post;
        $forRender['check_category'] = $checkCategory;

        // return render
        return $this->render('admin/post/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/create", name="admin_post_create")
     * @param Request $request
     * @param FileManagerServiceInterface $fileManagerService
     * @return RedirectResponse|Response
     */
    public function create(Request $request, FileManagerServiceInterface $fileManagerService)
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
            // get image from form
            $image = $form->get('image')->getData();

            if ($image)
            {
                // rename file, get new name and move file in file directory
                $fileName = $fileManagerService->imagePostUpload($image);

                // set image in post object
                $post->setImage($fileName);
            }

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
        // create manager
        $em = $this->getDoctrine()->getManager();

        // get post object by id
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

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
                // updated datetime update
                $post->setUpdateAtValue();

                // save data using manager ($em)
                $em->persist($post);
                $em->flush();

                // add flash message
                $this->addFlash('success', 'post updated');
            }

            // check if the delete button was pressed
            if ($form->get('delete')->isClicked())
            {
                // remove data using manager ($em)
                $em->remove($post);
                $em->flush();

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

        // return render
        return $this->render('admin/post/form.html.twig', $forRender);

    }

}