<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Friends;
use App\Entity\Post;
use App\Entity\Reaction;
use App\Entity\User;
use App\Form\PostType;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

}