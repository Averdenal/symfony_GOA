<?php


namespace App\Controller;


use App\Entity\AffGroupe;
use App\Entity\Comment;
use App\Entity\Friends;
use App\Entity\Group;
use App\Entity\Post;
use App\Entity\Reaction;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/",name="user.profil")
     * @param Request $request
     * @return Response
     * @throws Exception
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profilConnectUser(Request $request):Response
    {

        if($this->isGranted('ROLE_USER')){
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
                "posts" => $this->getDoctrine()->getRepository(Post::class)->findBy(['profile'=> $this->getUser()])
            ]);
        }
            throw $this->createAccessDeniedException('Vous devez validez votre mail pour voir cette section');

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
        if($user != $this->getUser()) {
            $groups = [];
            foreach ($this->getDoctrine()->getRepository(AffGroupe::class)->findBy([
                'user' => $user
            ]) as $value) {
                $groups[] = $value->getGroupe();
            }
            $user->setVisite($user->getVisite() + 1);
            $this->getDoctrine()->getManagerForClass(User::class)->flush();
            $post = new Post();
            $comment = new Comment();
            $form = $this->createForm(PostType::class, $post);
            $formComment = $this->createForm(CommentType::class, $comment);
            if ($yourFriend == 'ok'){
                $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(['profile'=> $user->getId()]);
            }else{
                $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(['profile'=> $user->getId(),'privat'=> true]);
            }
            return $this->render('users/profile.html.twig', [
                "current_menu" => "profil",
                'user' => $user,
                "formPost" => $form->createView(),
                "formComment" => $formComment->createView(),
                "friends" => $this->findFriendsUser($user),
                "yourFriend" => $yourFriend,
                "groups" => $groups,
                "adminGroups" => $this->getDoctrine()->getRepository(Group::class)->findBy([
                    'createdBy' => $user
                ]),
                "posts" => $posts,

            ]);
        }
            return $this->redirectToRoute('user.profil');
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
            $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getCreatedBy()) == 'ok'||
            $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getProfile()) == 'ok') {
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
            $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getCreatedBy()) == 'ok'
        || $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$post->getProfile())== 'ok') {
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
                return $this->json('Friends avec toi même? chelou non ?');
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

    /**
     * @Route("/add/post/profil/{id}",name="post.addPostProfilUser",methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function addProfilPost(Request $request,User $user)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->getUser() == $user || $this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$user) == 'ok'){
            $post = new Post();
            $form = $this->createForm(PostType::class,$post);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $post->setCreatedAt(new DateTime('now'))
                    ->setCreatedBy($this->getUser())
                    ->setProfile($user);
                $this->getDoctrine()->getManagerForClass('App:Post')->persist($post);
                $this->getDoctrine()->getManagerForClass('App:Post')->flush();
            }
            if($user == $this->getUser()){
                return $this->redirectToRoute('user.profil');
            }else{
                return $this->redirectToRoute('user.show',['id'=> $user->getId()]);
            }
        }
    }

    /**
     * @Route("/delete/post/{id}",name="post.deletePost",methods={"DELETE"})
     * @param Post $post
     * @return JsonResponse
     */
    public function deletePost(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($post->getCreatedBy() == $this->getUser() || $post->getProfile() == $this->getUser() || $post->getGroupe()->getCreatedBy() == $this->getUser()){
            $entityManager = $this->getDoctrine()->getManagerForClass(Post::class);
            $entityManager->remove($post);
            $entityManager->flush();
        }
        if($post->getProfile() != null){
            return $this->json(['source'=>'Profil','id'=>$post->getProfile()->getId()]);
        }elseif ($post->getGroupe() != null){
            return $this->json(['source'=>'Group','id'=>$post->getGroupe()->getId()]);
        }else{
            //get page a prévoir
            return $this->json(0);
        }

    }

    /**
     * @Route("/add/Reaction",name="post.addReaction",methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @return JsonResponse
     */
    public function addReaction(Request $request)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($request->request->get('post'));
        $membre = false;
        $friend = $this->getDoctrine()->getRepository(Friends::class)->isFriend($post->getCreatedBy(),$this->getUser());
        foreach ($this->getUser()->getAffGroupes() as $groups){
            if($groups->getUser() == $this->getUser()){
                $membre = true;
            }
        }
        if( $friend == 'ok' || $membre){
            $reaction = $this->getDoctrine()->getRepository(Reaction::class)->findOneBy([
                'user'=>$this->getUser(),
                'post'=>$post]);
            $identic = false;

            $entityManager = $this->getDoctrine()->getManager();

            if($reaction){
                if( $reaction->getReact() == $request->request->get('reaction')){
                    $identic = true;
                }
                $post->removeReaction($reaction);
                $entityManager->persist($post);
                $entityManager->flush();
            }
            if(!$identic){
                $r = new Reaction();
                $r->setPost($post);
                $r->setUser($this->getUser());
                $r->setReact($request->request->get('reaction'));

                $post->addReaction($r);
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->json('ok');
            }else{
                return $this->json('null reaction');
            }
        }
        throw $this->createAccessDeniedException();
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