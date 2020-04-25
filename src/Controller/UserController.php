<?php


namespace App\Controller;


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
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil"
        ]);
    }
    /**
     * @Route("/profil/show/{:login}",name="user.show")
     * @return Response
     */
    public function profil():Response
    {
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil"
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