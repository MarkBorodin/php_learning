<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AdminBaseController
{
    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function index()
    {
        // create and fill context
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Categories';
        $forRender['category'] = $this->categoryRepository->getAllCategory(); // get all categories

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
        // create new category object
        $category = new Category();

        // create form
        $form = $this->createForm(CategoryType::class, $category);

        // handle request
        $form->handleRequest($request);

        // check and process form
        if(($form->isSubmitted()) && ($form->isValid()))
        {
            // create category
            $this->categoryRepository->setCreateCategory($category);

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
        // get category object by id
        $category = $this->categoryRepository->getOneCategory($id);

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
                // update category
                $this->categoryRepository->setUpdateCategory($category);

                // add flash message
                $this->addFlash('success', 'category updated');
            }

            // check if the delete button was pressed
            if ($form->get('delete')->isClicked())
            {
                // delete category
                $this->categoryRepository->setDeleteCategory($category);

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