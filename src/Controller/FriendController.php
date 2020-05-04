<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Entity\User;
use App\Repository\FriendsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController
{
    /**
     * @Route("/friend/add/{id}", name="friend.add")
     * @param User $user
     * @param FriendsRepository $repository
     * @return JsonResponse
     */
    public function addFriends(User $user,FriendsRepository $repository):Response
    {
        $addFriendByMe = $repository->findOneBy(['user1'=>$this->getUser(),'user2'=>$user]);
        $addFriendByHim = $repository->findOneBy(['user2'=>$this->getUser(),'user1'=>$user]);
        $f = new Friends();

        if(!$addFriendByMe && !$addFriendByHim){
            if($this->getUser()!= $user){
                $f->setUser2($user);
                $this->getUser()->addFriend($f);
                $this->getDoctrine()->getManager()->flush();
                return $this->json('add Friend');
            }else{
                return $this->json('Friends avec toi? chelou non ?');
            }

        }else{
            if($addFriendByHim && $addFriendByHim->getStatus() == $f::statusaff[0]){
                $status = $f::statusaff;
                $addFriendByHim->setStatus($status[1]);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($addFriendByHim);
                $entityManager->flush();
                return $this->json('Friend active');
            }
            return $this->json('impossible');
        }

    }
    /**
     * @Route("/friend/delete/{id}", name="friend.delete",methods={"DELETE"})
     * @param User $user
     * @param FriendsRepository $repository
     * @return JsonResponse
     */
    public function deleteFriends(User $user,FriendsRepository $repository):Response
    {
        $addFriendByMe = $repository->findOneBy(['user1'=>$this->getUser(),'user2'=>$user]);
        $addFriendByHim = $repository->findOneBy(['user2'=>$this->getUser(),'user1'=>$user]);
        if($addFriendByMe){
            $this->getUser()->removeFriend($addFriendByMe);
        }
        if($addFriendByHim){
            $this->getUser()->removeFriendsAddMe($addFriendByHim);
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->json('delete ok');
    }
}
