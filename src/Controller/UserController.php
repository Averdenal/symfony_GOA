<?php


namespace App\Controller;


use \App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{


    public function __construct()
    {
    }

    /**
     * @Route("/profil",name="user.profil")
     * @return Response
     */
    public function profilConnectUser():Response
    {
        if($this->getUser() != null){
            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                'user'=>$this->getUser()
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/profil/show/{id}",name="user.show")
     * @param User $user
     * @return Response
     */
    public function profil(User $user):Response
    {
        dump($user);
        dump($this->getUser());
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil",
            "userCo"=>$this->getUser(),
            'user' => $user

        ]);
    }
    /**
     * @Route("/profil/edit/{:id}",name="user.edit")
     * @return Response
     */
    public function editProfil():Response
    {
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil"
        ]);
    }
}