<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserSearchController extends AbstractController
{
    /**
     * @Route("/user/search", name="user_search")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $reponse = [];
        $users = $userRepository->findByWord($_GET['term']);
        $groups = $groupRepository->findByWord($_GET['term']);
        foreach ($users as $userUniq){
            $data = ["label" =>ltrim($userUniq->getLastname().' '.$userUniq->getFirstname().' '.$userUniq->getPseudo()),
                "value"=>$userUniq->getId(),
                "cat"=>"users"];
            if($data != ''){
                $reponse[] = $data;
            }

        }
        foreach ($groups as $group){
            $data = ["label" =>ltrim($group->getName()),
                "value"=>$group->getId(),
                "cat"=>"groups"];
            if($data != ''){
                $reponse[] = $data;
            }

        }
        return $this->json($reponse,200);

    }
}
