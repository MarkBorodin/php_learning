<?php

namespace App\Controller\Main;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends BaseController
{
    private PostRepository $postRepository;

    /**
     * HomeController constructor.
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/comment/create/{postId}", name="comment_create")
     * @param Request $request
     * @param int $postId
     * @return Response
     */
    public function createAction(Request $request, int $postId): Response
    {
        // get manager
        $em = $this->getDoctrine()->getManager();

        // create new comment object
        $comment = new Comment();

        // get image from form
//        $content = $request->request->get('_content');
        $content = '123';
        $post = $this->postRepository->find($postId);

        $comment->setContent($content);
        $comment->setPost($post);
        $comment->setCreateAtValue();
        $comment->setIsPublished();

        $em->persist($comment);
        $em->flush();

        // add flash message
        $this->addFlash('success', 'comment added');

        return $this->redirectToRoute('home');
    }
}
