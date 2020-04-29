<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{


    public function __construct()
    {
    }

    /**
     * @Route("/profil",name="user.profil")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function profilConnectUser(Request $request):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $post = new Post();
        $form = $this->createForm(PostType::class,$post);


        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil",
            "user" => $this->getUser(),
            "formPost" => $form->createView()
        ]);
    }

    /**
     * @Route("/profil/show/{id}",name="user.show")
     * @param User $user
     * @return Response
     */
    public function profil(User $user):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil",
            'user' => $user,
            "formPost" => $form->createView()

        ]);
    }
    /**
     * @Route("/profil/edit/{id}",name="user.edit")
     * @return Response
     */
    public function editProfil(User $user):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->getUser()->getUsername() == $user->getUsername()){
            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                "user" =>$user
            ]);
        }

    }
}