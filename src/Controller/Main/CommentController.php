<?php

namespace App\Controller\Main;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends BaseController
{
    private PostRepository $postRepository;
    private CommentRepository $commentRepository;
    /**
     * @var Security
     */
    private $security;

    /**
     * HomeController constructor.
     * @param PostRepository $postRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->security = $security;
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

        // get _context from form
        $content = $request->request->get('_context');
        $post = $this->postRepository->find($postId);

        // get user from request
        $user = $this->security->getUser();

        // get data
        $comment->setContent($content);
        $comment->setPost($post);
        $comment->setCreateAtValue();
        $comment->setIsPublished();
        $comment->setUser($user);

        // save object
        $em->persist($comment);
        $em->flush();

        // add flash message
        $this->addFlash('success', 'comment added');

        return $this->redirectToRoute('home');
    }

}
