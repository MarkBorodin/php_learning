<?php

namespace App\Controller\Main;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\UserType;
use App\Message\MyEmailMessage;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends BaseController
{
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private Security $security;
    private $bus;
//    private $projectDir;

    /**
     * HomeController constructor.
     * @param PostRepository $postRepository
     * @param CommentRepository $commentRepository
     * @param UserRepository $userRepository
     * @param Security $security
     * @param MessageBusInterface $bus
     */
    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository,
                                UserRepository $userRepository, Security $security, MessageBusInterface $bus)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->bus = $bus;
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
        $userId = $this->security->getUser();
        $user = $this->userRepository->find($userId);

        // set data
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


        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../../.env');
        $emailTo = $_ENV['ADMIN_EMAIL'];
        $this->bus->dispatch(new MyEmailMessage($emailTo));

        return $this->redirectToRoute('home');
    }

}
