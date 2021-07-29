<?php


namespace App\Controller\Admin;


use App\Entity\User;

use App\Form\UserType;
use App\Repository\UserRepositoryInterface;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminUserController extends AdminBaseController
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(UserRepositoryInterface $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @Route("/admin/user", name="admin_user")
     * @return Response
     */
    public function index()
    {
        // context for render
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Users';

        // get all users from db (can be like this):
        // $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        // $forRender['users'] = $users

        // or:
        $users = $this->userRepository->getAll();
        $forRender['users'] = $users;

        // return response
        return $this->render('admin/user/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/user/create", name="admin_user_create")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        // create user object
        $user = new User();
        // create form
        $form = $this->createForm(UserType::class, $user);
        // create manager
        $em = $this->getDoctrine()->getManager();
        // data from form
        $form->handleRequest($request);

        // if form is valid
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // get password hash
            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            // set password hash to user object
            $user->setPassword($password);
            // set role to user object
//            $user->setRoles(['ROLE_ADMIN']);
            // save data using manage ($em)
            $em->persist($user);
            $em->flush();

            // return redirect to 'admin_user'
            return $this->redirectToRoute('admin_user');
        }

        // get context from superclass
        $forRender = parent::renderDefault();
        // change title from superclass
        $forRender['title'] = 'user create form';
        // add form to context
        $forRender['form'] = $form->createView();
        // return render
        return $this->render('admin/user/form.html.twig', $forRender);

    }

    /**
     * @Route("/admin/user/update/{userId}", name="admin_user_update")
     * @param Request $request
     * @param int $userId
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, int $userId, UserPasswordHasherInterface $passwordHasher)
    {
        // create manager
        $em = $this->getDoctrine()->getManager();

        // get user by id
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        // create form and handleR request
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        // if form is valid
        if(($formUser->isSubmitted()) && ($formUser->isValid()))
        {
            // get password hash
            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            // set password hash to user object
            $user->setPassword($password);

            // save data using manage ($em)
            $em->persist($user);
            $em->flush();

            //message
            $this->addFlash('success', 'user profile updated');

            // redirect
            return $this->redirectToRoute('admin_user');
        }

        // context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Edit profile';
        $forRender['form'] = $formUser->createView();

        return $this->render('admin/user/form.html.twig', $forRender);
    }

    /**
     * @Route("/admin/user/delete/{userId}", name="admin_user_delete")
     * @param Request $request
     * @param int $userId
     */
    public function deleteAction(Request $request, int $userId)
    {

        // get user now
        $userNow = $this->security->getUser();

        // get manager
        $em = $this->getDoctrine()->getManager();

        // get post object by id
        $user = $this->userRepository->find($userId);

        if($userNow->getId() != $user->getId())
        {
            if ($request->request->get('delete')) {
                $em->remove($user);
                $em->flush();

                // add flash message
                $this->addFlash('success', 'user deleted!');
            }
        }

        // return render
        return $this->redirectToRoute('admin_user');
    }

}