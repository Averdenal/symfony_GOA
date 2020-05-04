<?php


namespace App\Controller;


use App\Entity\Friends;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Form\UserType;
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
//dd($this->findFriendsUser($this->getUser()));
        return $this->render('users/profile.html.twig',[
            "current_menu" => "profil",
            "user" => $this->getUser(),
            "formPost" => $form->createView(),
            "friends" => $this->findFriendsUser($this->getUser())
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
        $yourFriend = $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$user);
        if($user != $this->getUser()){
            $post = new Post();
            $form = $this->createForm(PostType::class,$post);
            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                'user' => $user,
                "formPost" => $form->createView(),
                "friends" => $this->findFriendsUser($user),
                "yourFriend"=>$yourFriend

            ]);
        }else{
            return $this->redirectToRoute('user.profil');
        }
    }
    /**
     * @Route("/profil/edit/{id}",name="user.edit")
     * @return Response
     */
    public function editProfil(Request $request,User $user):Response
    {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($user == $this->getUser()){

            $form = $this->createForm(UserType::class,$user);
            $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $bannerUrl = '../assets/img/banners';
            $bannerUrlPublic = 'build/img/banners/';

            $pictureProfileUrl = '../assets/img/banners';
            $pictureProfileUrlPublic = 'build/img/banners/';

            $pictureBanner = $form->get('pictureProfil')->getData();
            if($pictureBanner){
                $repositoryPicture = $this->getDoctrine()->getRepository('App:Picture');
                $picture = $repositoryPicture->createPictureByFile($pictureBanner,$pictureProfileUrl,$pictureProfileUrlPublic,'Avatar');
                $user->setPictureProfil($picture);
            }

            $pictureBanner = $form->get('banner')->getData();
            if($pictureBanner){
                $repositoryPicture = $this->getDoctrine()->getRepository('App:Picture');
                $picture = $repositoryPicture->createPictureByFile($pictureBanner,$bannerUrl,$bannerUrlPublic,'Banner');
                $user->setBanner($picture);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
            return $this->render('users/editProfile.html.twig',[
                "current_menu" => "profil",
                "user" =>$user,
                "form" => $form->createView()
            ]);

        }


    }

    private function findFriendsUser($value)
    {
        $friends = $this->getDoctrine()->getRepository('App:Friends')->findFriends($value);
        $friendsValide = [];
        foreach ($friends as $friend){
            $f = new Friends();
            if($friend->getUser1() == $value){
                $f->SetUser1($friend->getUser1());
                $f->setUser2($friend->getUser2());
            }else{
                $f->SetUser1($friend->getUser2());
                $f->setUser2($friend->getUser1());
            }

            $f->setStatus($friend->getStatus());
            $friendsValide[] = $f;
        }
        $friendsValide = array_unique($friendsValide,SORT_REGULAR);
        $tab = [];
        foreach ($friendsValide as $friendValide){
            $user = new User();
            $user = $this->getDoctrine()->getRepository(User::class)->find($friendValide->getUser2()->getId());
            $tab[] = ['status'=>$friendValide->getStatus(),
                    'user'=>$user
                    ];
        }
        return $tab;

    }
}