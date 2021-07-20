<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AdminBaseController
{
    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function index()
    {
        // get all categories
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Categories';
        $forRender['category'] = $category;

        // return render
        return $this->render('admin/category/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/category/create", name="admin_category_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        // create manager
        $em = $this->getDoctrine()->getManager();

        // create new category object
        $category = new Category();

        // create form
        $form = $this->createForm(CategoryType::class, $category);

        // handle request
        $form->handleRequest($request);

        // check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // set values
            $category->setCreateAtValue();
            $category->setUpdateAtValue();
            $category->setIsPublished();

            // save data using manager ($em)
            $em->persist($category);
            $em->flush();

            // add flash message
            $this->addFlash('success', 'category added');

            // return redirect
            return $this->redirectToRoute('admin_category');
        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Create category';
        $forRender['form'] = $form->createView();

        // return render
        return $this->render('admin/category/form.html.twig', $forRender);
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_category_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(int $id, Request $request)
    {
        // create manager
        $em = $this->getDoctrine()->getManager();

        // get category object by id
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        // create form to change category
        $form = $this->createForm(CategoryType::class, $category);

        // get data from request
        $form->handleRequest($request);

        //  check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // check if the save button was pressed
            if ($form->get('save')->isClicked())
            {
                // updated datetime update
                $category->setUpdateAtValue();

                // save data using manager ($em)
                $em->persist($category);
                $em->flush();

                // add flash message
                $this->addFlash('success', 'category updated');
            }

            // check if the delete button was pressed
            if ($form->get('delete')->isClicked())
            {
                // remove data using manager ($em)
                $em->remove($category);
                $em->flush();

                // add flash message
                $this->addFlash('success', 'category deleted');
            }

            // return redirect
            return $this->redirectToRoute('admin_category');

        }

        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Update category';
        $forRender['form'] = $form->createView();

        // return render
        return $this->render('admin/category/form.html.twig', $forRender);

    }

}