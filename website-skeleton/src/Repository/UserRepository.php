<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Znajdź użytkowników, którzy nie są jeszcze znajomymi danego użytkownika.
     *
     * @param User $user Aktualnie zalogowany użytkownik
     * @return User[] Lista użytkowników, których można dodać jako znajomych
     */
    public function findUsersToAddAsFriend(User $user): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('App\Entity\FriendShip', 'f1', 'WITH', 'f1.user = :user AND f1.friend = u')
            ->leftJoin('App\Entity\FriendShip', 'f2', 'WITH', 'f2.friend = :user AND f2.user = u')
            ->andWhere('u != :user')
            ->andWhere('f1.id IS NULL')
            ->andWhere('f2.id IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }


    /**
     * Zwraca listę znajomych dla danego użytkownika.
     *
     * @param User $user Użytkownik, dla którego chcemy pobrać listę znajomych.
     * @return array Lista znajomych.
     */
    public function findFriends(User $user): array
    {
        return $this->createQueryBuilder('f')
        ->where('(
            (f.user = :user AND f.friend != :user) OR 
            (f.friend = :user AND f.user != :user)
        )')
        ->andWhere('f.status = :status')
        ->setParameter('user', $user)
        ->setParameter('status', 'accepted')
        ->getQuery()
        ->getResult();
    }

    /**
     * Znajduje zaproszenia do znajomych dla danego użytkownika.
     *
     * @param User $user Użytkownik, dla którego znajdujemy zaproszenia.
     * @return array Zaproszenia do znajomych.
     */
    public function findFriendshipInvitations(User $user): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.friendOf', 'f')
            ->where('f.status = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
