<?php


namespace App\Controller\Admin;


use App\Entity\User;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AdminBaseController
{
    /**
     * @Route("/admin/user", name="admin_user")
     * @return Response
     */
    public function index()
    {
        // get all users from db
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        // context for render
        $forRender = parent::renderDefault();
        $forRender['users'] = $users;
        $forRender['title'] = 'Users';
        // return response
        return $this->render('admin/user/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/user/create", name="admin_user_create")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher)
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
            $user->setRoles(['ROLE_ADMIN']);
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

}