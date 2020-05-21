<?php


namespace App\Controller;


use App\Entity\AffGroupe;
use App\Entity\Comment;
use App\Entity\Friends;
use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\UserType;
use App\Repository\FriendsRepository;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{


    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    /**
     * @Route("/profil",name="user.profil")
     * @param Request $request
     * @return Response
     * @throws Exception
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profilConnectUser(Request $request):Response
    {

        if($this->security->isGranted('ROLE_USER')){
            $groups = [];
            $post = new Post();

            $form = $this->createForm(PostType::class,$post);
            foreach ($this->getDoctrine()->getRepository(AffGroupe::class)->findBy([
                'user'=>$this->getUser()
            ]) as $value){
                $groups[] = $value->getGroupe();
            }

            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                "user" => $this->getUser(),
                "formPost" => $form->createView(),
                "friends" => $this->findFriendsUser($this->getUser()),
                "groups"=> $groups,
                "adminGroups"=>$this->getDoctrine()->getRepository(Group::class)->findBy([
                    'createdBy'=>$this->getUser()
                ]),
            ]);
        }else{
            throw $this->createAccessDeniedException('Vous devez validez votre mail pour voir cette section');
        }

    }

    /**
     * @Route("/profil/show/{id}",name="user.show")
     * @param User $user
     * @return Response
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profil(User $user):Response
    {

        $yourFriend = $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$user);
        if($user != $this->getUser()){
            $groups = [];
            foreach ($this->getDoctrine()->getRepository(AffGroupe::class)->findBy([
                'user'=>$user
            ]) as $value){
                $groups[] = $value->getGroupe();
            }
            $user->setVisite($user->getVisite() + 1);
            $this->getDoctrine()->getManagerForClass(User::class)->flush();
            $post = new Post();
            $comment = new Comment();
            $form = $this->createForm(PostType::class,$post);
            $formComment = $this->createForm(CommentType::class,$comment);
            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                'user' => $user,
                "formPost" => $form->createView(),
                "formComment"=> $formComment->createView(),
                "friends" => $this->findFriendsUser($user),
                "yourFriend"=>$yourFriend,
                "groups"=>$groups,
                "adminGroups"=>$this->getDoctrine()->getRepository(Group::class)->findBy([
                    'createdBy'=>$user
                ])

            ]);
        }else{
            return $this->redirectToRoute('user.profil');
        }
    }
    /**
     * @Route("/profil/edit/",name="user.edit")
     * @return Response
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function editProfil(Request $request):Response
    {
        $user = $this->getUser();
        if($user == $this->getUser()){

            $form = $this->createForm(UserType::class,$user);
            $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $bannerUrl = 'assets/img/banners/';

            $pictureProfileUrl = 'assets/img/avatar/';

            $pictureBanner = $form->get('pictureProfil')->getData();
            if($pictureBanner){
                $repositoryPicture = $this->getDoctrine()->getRepository('App:Picture');
                $picture = $repositoryPicture->createPictureByFile($pictureBanner,$pictureProfileUrl,'Avatar');
                $user->setPictureProfil($picture);
            }

            $pictureBanner = $form->get('banner')->getData();
            if($pictureBanner){
                $repositoryPicture = $this->getDoctrine()->getRepository('App:Picture');
                $picture = $repositoryPicture->createPictureByFile($pictureBanner,$bannerUrl,'Banner');
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

    /**
     * @Route("/profil/post/{id}",name="post.showComment", methods={"GET"})
     * @param Post $post
     * @return Response
     * @throws Exception
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function showPostComment(Post $post)
    {
        if($post->getCreatedBy() == $this->getUser() ||
            $post->getProfile() == $this->getUser() ||
            $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getCreatedBy()) == 'ok') {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $groups = [];
            foreach ($this->getDoctrine()->getRepository(AffGroupe::class)->findBy([
                'user'=>$this->getUser()
            ]) as $value){
                $groups[] = $value->getGroupe();
            }

            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                "user" =>$post->getProfile(),
                "formComment" => $form->createView(),
                "post" => $post,
                "editComment" =>true,
                "yourFriend"=>"ok",
                "friends" => $this->findFriendsUser($post->getProfile()),
                "groups"=>$groups,
                "adminGroups"=>$this->getDoctrine()->getRepository(Group::class)->findBy([
                    'createdBy'=>$post->getProfile()
                ])
            ]);

        }
        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/add/Comment/{id}",name="post.addComment", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function addCommentPost(Request $request, Post $post)
    {
        if($post->getCreatedBy() == $this->getUser() ||
            $post->getProfile() == $this->getUser() ||
            $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getCreatedBy()) == 'ok') {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setCreatedAt(new DateTime())
                    ->setUser($this->getUser())
                    ->setPost($post);
                $post->addComment($comment);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->json(['status'=>'ok','comment'=>$comment->getContent()]);
            }
            return $this->json(['status'=>'nok']);
        }
        throw $this->createAccessDeniedException("n'est pas autorisé a ajouter un commentaire");
    }

    /**
     * @Route("/delete/comment/{id}",name="comment.delete",methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param Comment $comment
     * @return JsonResponse
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function deleteComment(Comment $comment)
    {
        if($comment->getUser() == $this->getUser()){
            $post = $comment->getPost();
            $post->removeComment($comment);
            $em = $this->getDoctrine()->getManagerForClass(Post::class);
            $em->persist($post);
            $em->flush();
            return $this->json('delete ok');
        }
        return $this->json('delete nok');
    }


    public function findFriendsUser($value)
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

    /**
     * @Route("/friend/add/{id}", name="friend.add")
     * @param User $user
     * @param FriendsRepository $repository
     * @return JsonResponse
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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