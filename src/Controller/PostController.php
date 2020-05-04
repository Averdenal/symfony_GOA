<?php


namespace App\Controller;


use App\Entity\Friends;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
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
        if($this->getDoctrine()->getRepository(Friends::class)->isFriend($this->getUser(),$user) == 'ok'){
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
        return $this->redirectToRoute('home',['message'=>'non autorisé'],401);
    }
}