<?php

namespace App\Repository;

use App\Entity\Friends;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friends|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friends|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friends[]    findAll()
 * @method Friends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friends::class);
    }

    public function findFriends($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user1 = :val')
            ->orWhere('f.user2 = :val and f.user1 != :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function isFriend(User $user1,User $user2){
        $f = new Friends();
        if(!$this->findOneBy(['user1'=>$user1,'user2'=>$user2,'status'=>[$f::statusaff[1],$f::statusaff[2]]])){
            if(!$this->findOneBy(['user2'=>$user1,'user1'=>$user2,'status'=>[$f::statusaff[1],$f::statusaff[2]]])){
                if($this->findOneBy(['user1'=>$user1,'user2'=>$user2,'status'=>$f::statusaff[0]])){
                    $yourFriend = 'aok';
                }elseif ($this->findOneBy(['user1'=>$user2,'user2'=>$user1,'status'=>$f::statusaff[0]])){
                    $yourFriend = 'vok';
                }
                else{
                    $yourFriend ='ko';
                }
            }else{
                $yourFriend = 'ok';
            };
        }else{
            $yourFriend = 'ok';
        }
        return $yourFriend;
    }
    /*
    public function findOneBySomeField($value): ?Friends
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
