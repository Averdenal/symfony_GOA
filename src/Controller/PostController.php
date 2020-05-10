<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Friends;
use App\Entity\Post;
use App\Entity\Reaction;
use App\Entity\User;
use App\Form\PostType;
use DateTime;
use Doctrine\ORM\EntityManager;
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
     */
    public function deletePost(Post $post)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($post->getCreatedBy() == $this->getUser() || $post->getProfile() == $this->getUser()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }
        return $this->json($post->getProfile()->getId());
    }
    /**
     * @Route("/add/Reaction",name="post.addReaction",methods={"POST"})
     */
    public function addReaction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = $this->getDoctrine()->getRepository(Post::class)->find($request->request->get('post'));

        $friend = $this->getDoctrine()->getRepository(Friends::class)->isFriend($post->getCreatedBy(),$this->getUser());
        if( $friend == 'ok'){
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

        return $this->json('nok',403);

    }


}