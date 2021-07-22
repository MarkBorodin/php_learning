<?php

namespace App\Repository;

use App\Entity\Post;
use App\Service\FileManagerServiceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var FileManagerServiceInterface
     */
    private $fm;

    /**
     * PostRepository constructor.
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     * @param FileManagerServiceInterface $fileManagerService
     */
    public function __construct(ManagerRegistry $registry,
                                EntityManagerInterface $manager,
                                FileManagerServiceInterface $fileManagerService)
    {
        $this->em = $manager;
        $this->fm = $fileManagerService;
        parent::__construct($registry, Post::class);
    }

    /**
     * @return array
     */
    public function getAllPost(): array
    {
        // get all pasts
        return parent::findAll();
    }

    /**
     * @param int $postId
     * @return object
     */
    public function getOnePost(int $postId): object
    {
        // get one post object by id
        return parent::find($postId);
    }

    /**
     * @param Post $post
     * @param UploadedFile $file
     * @return object
     */
    public function setCreatePost(Post $post, UploadedFile $file): object
    {
        if ($file)
        {
            // upload file
            $fileName = $this->fm->imagePostUpload($file);

            // set image name to post object
            $post->setImage($fileName);
        }

        // set values
        $post->setCreateAtValue();
        $post->setUpdateAtValue();
        $post->setIsPublished();

        // call manager and save object
        $this->em->persist($post);
        $this->em->flush();

        return $post;
    }

    /**
     * @param Post $post
     * @param UploadedFile $file
     * @return object
     */
    public function setUpdatePost(Post $post, $file): object
    {
        // get image name from post object
        $fileName = $post->getImage();

        // if image uploaded
        if ($file)
        {
            // if old image
            if ($fileName)
            {
                // remove old image
                $this->fm->removePostImage($fileName);
            }
            // upload new image and get its name
            $fileName = $this->fm->imagePostUpload($file);

            // set new image name
            $post->setImage($fileName);
        }

        // update updated date
        $post->setUpdateAtValue();

        // save object
        $this->em->flush();

        return $post;
    }

    /**
     * @param Post $post
     */
    public function setDeletePost(Post $post)
    {
        // get file
        $fileName  = $post->getImage();

        // if file exists
        if($fileName)
        {
            // rm file
            $this->fm->removePostImage($fileName);
        }

        // rm post
        $this->em->remove($post);

        // save object
        $this->em->flush();
    }

    // query example

    // get posts by category filter

    /**
     * @param int $categoryId
     * @return Post[]
     */
    public function getPostsByCategoryFilter(int $categoryId): array
    {
        // filter categories by id and return them
        return $this->findBy(['id' => $categoryId]);
    }

    /**
     * @param int $categoryId
     */
    public function getPostFilterJson(int $categoryId): array
    {
        // construct query to db
        $db = $this->createQueryBuilder('p')
            ->select('p.id', 'p.title', 'p.content', 'p.image', 'p.update_at', 'p.create_at', 'p.is_published', 'pc.title as titleCategory' )
            ->innerJoin('p.category', 'pc')
            ->where('p.id = :categoryId')
            ->setParameter('categoryId', $categoryId);

        $query = $db->getQuery();
        return $query->execute();
    }
}
