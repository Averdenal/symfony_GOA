<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserSearchController extends AbstractController
{
    /**
     * @Route("/user/search", name="user_search")
     */
    public function index(UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        dump($_GET['term']);
        $reponse = [];
        $users = $userRepository->findByWord($_GET['term']);
        foreach ($users as $userUniq){
            $data = ["label" =>ltrim($userUniq->getLastname().' '.$userUniq->getFirstname().' '.$userUniq->getPseudo()),
                "value"=>$userUniq->getId(),
                "cat"=>"user"];
            if($data != ''){
                $reponse[] = $data;
            }

        }
        return $this->json($reponse,200);

    }
}
