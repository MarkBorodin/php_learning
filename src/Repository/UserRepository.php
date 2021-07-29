<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    private UserPasswordHasherInterface $userPasswordHasherInterface;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
        $this->userPasswordHasherInterface = $passwordHasher;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    public function getOne(int $userId): object
    {
        return parent::find($userId);
    }

    public function getAll(): array
    {
        return parent::findAll();
    }

    /**
     * @param $name
     * @param $email
     * @param $password
     * @param UserPasswordHasherInterface $passwordHasher
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUser($name, $email, $password)
    {
        $user = new User();

        $password = $this->userPasswordHasherInterface->hashPassword($user, $password);

        $user->setFullName($name);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $this->_em->persist($user);
        $this->_em->flush();
    }
}
