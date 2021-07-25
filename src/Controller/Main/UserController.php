<?php


namespace App\Controller\Main;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user/update/{userId}", name="user_update")
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
            return $this->redirectToRoute('user_update', ['userId' => $userId]);
        }

        // context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Edit profile';
        $forRender['form'] = $formUser->createView();

        return $this->render('main/form.html.twig', $forRender);
    }

    /**
     * @Route("/user/create", name="user_create")
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
            return $this->redirectToRoute('app_login');
        }

        // get context from superclass
        $forRender = parent::renderDefault();
        // change title from superclass
        $forRender['title'] = 'Registration';
        // add form to context
        $forRender['form'] = $form->createView();
        // return render
        return $this->render('main/form.html.twig', $forRender);

    }
}