<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Friends;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\UserEmailType;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @Route("/abs/{id}",name="demo.demo")
     * @IsGranted("ROLE_ADMIN")
     */
    public function getDelete(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

    }

    /**
     * @Route("/valide/{id}",name="user.valide")
     * @param User $user
     */
    public function valideUser(User $user)
    {
        if($_GET['token'] == $user->getToken()){
            $user->setToken('');
            $user->addRole("ROLE_USER");
            $em = $this->getDoctrine()->getManagerForClass(User::class);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home');
        }else{
            throw $this->createAccessDeniedException('le token n\'est pas correct ou le compte est déjà actif');
        }

    }

    /**
     * @Route("/resetPwd",name="user.needResetPassword")
     * @param MailerInterface $mailer
     * @throws TransportExceptionInterface
     */
    public function forgotPassword(Request $request, MailerInterface $mailer)
    {
        if ($request->get('email')) {
            $em= $this->getDoctrine()->getRepository(User::class);
            $user = $em->findOneBy([
                'email' =>$request->get('email')
            ]);
            $manager = $this->getDoctrine()->getManager();
            $token = 'c'.md5($user->getEmail().random_bytes(4).$user->getPseudo());
            $user->setToken($token);
            $manager->persist($user);
            $manager->flush();

            $email = (new Email())
                ->from('no-reply@eliptium.fr')
                ->to($user->getEmail())
                ->subject('Reset password - Getting Out Again!')
                ->html($this->renderView('emails/forgetPassword.html.twig',[
                    'token' => $user->getToken(),
                    'username' => $user->getPseudo(),
                    'address' => $user->getEmail(),
                    'user' => $user->getId()

                ]));
            $mailer->send($email);
            return $this->render('users/forgetPassword.html.twig',[
                "message" => "Demande envoyer, tu vas reçevoir un email pour le changement"
            ]);
        }

        return $this->render('users/forgetPassword.html.twig',[
            'titlePage' => 'Un trous de mémoire ?'
        ]);
    }

    /**
     * @Route("/changepwd",name="user.forgetpwd", methods={"POST","GET"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->get('email')&& $request->get('password')&&$request->get('id')){
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$request->get('email')]);
            if($user->getId() == $request->get('id')){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $request->get('password')
                    )
                );
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->json(['user'=> $user->getPseudo(),'changement'=>'ok']);
            }else{
                dd('id error');
            }

        }else{
            $token = $_GET['token'];
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['token'=>$token]);
            if($user){
                return $this->render('security/newPassword.html.twig',[
                    'user' => $user
                ]);
            }else{
                throw $this->createAccessDeniedException();
            }
        }
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
            $post = new Post();
            $comment = new Comment();

            $form = $this->createForm(PostType::class,$post);
            $formComment = $this->createForm(CommentType::class,$comment);

            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                "user" => $this->getUser(),
                "formPost" => $form->createView(),
                "friends" => $this->findFriendsUser($this->getUser()),
                "formComment"=> $formComment->createView(),
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
                "yourFriend"=>$yourFriend

            ]);
        }else{
            return $this->redirectToRoute('user.profil');
        }
    }
    /**
     * @Route("/profil/edit/{id}",name="user.edit")
     * @return Response
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function editProfil(Request $request,User $user):Response
    {
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

    /**
     * @Route("/add/Comment/{id}",name="post.addComment", methods={"POST","GET"})
     * @param Request $request
     * @param Post $post
     * @return Response
     * @throws Exception
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function addComment(Request $request,Post $post)
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
            }

            return $this->render('users/profile.html.twig',[
                "current_menu" => "profil",
                "user" =>$post->getProfile(),
                "formComment" => $form->createView(),
                "post" => $post,
                "editComment" =>true,
                "yourFriend"=>"ok",
                "friends" => $this->findFriendsUser($post->getProfile()),
            ]);

        }
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