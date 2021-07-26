<?php


namespace App\Controller\Admin;


use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminCommentController extends AdminBaseController
{
    private PostRepository $postRepository;
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;
    /**
     * @var Security
     */
    private $security;

    /**
     * HomeController constructor.
     * @param PostRepository $postRepository
     * @param CommentRepository $commentRepository
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository, UserRepository $userRepository, Security $security)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @Route("admin/comment/create/{postId}", name="admin_comment_create")
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

        return $this->redirectToRoute('admin_post');
    }


    /**
     * @Route("admin/comment/delete/{id}", name="admin_comment_delete")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction(int $id, Request $request): Response
    {
        // get manager
        $em = $this->getDoctrine()->getManager();

        // get post object by id
        $comment = $this->commentRepository->find($id);

        if ($request->request->get('delete'))
        {
            $em->remove($comment);
            $em->flush();

            // add flash message
            $this->addFlash('success', 'comment deleted!');
        }

        // return render
        return $this->redirectToRoute('admin_post');

    }
}
