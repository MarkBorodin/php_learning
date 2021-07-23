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

}