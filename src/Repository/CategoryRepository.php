<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Parent_;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct($registry, Category::class);
    }

    public function getAllCategory(): array
    {
        // get all categories
        return parent::findAll();
    }

    public function getOneCategory(int $categoryId): object
    {
        // get one category object by id
        return parent::find($categoryId);
    }

    public function setCreateCategory(Category $category): object
    {
        // set values
        $category->setCreateAtValue();
        $category->setUpdateAtValue();
        $category->setIsPublished();

        // save data using manager
        $this->manager->persist($category);
        $this->manager->flush();

        return $category;
    }

    public function setUpdateCategory(Category $category): object
    {
        // set value
        $category->setUpdateAtValue();

        // save data using manager
        $this->manager->flush();

        return $category;
    }

    public function setDeleteCategory(Category $category)
    {
        // rm category
        $this->manager->remove($category);

        // save
        $this->manager->flush();
    }
}
