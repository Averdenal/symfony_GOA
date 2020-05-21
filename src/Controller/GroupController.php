<?php


namespace App\Controller;


use App\Entity\AffGroupe;
use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\GroupType;
use App\Form\PostGroupType;
use App\Repository\GroupRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @var GroupRepository
     */
    private $repository;

    /**
     * GroupController constructor.
     * @param GroupRepository $repository
     */
    public function __construct(GroupRepository $repository)
    {

        $this->repository = $repository;
    }
    /**
     * @Route("/Groupes", name="groups.allGroupePublic")
     * @return Response
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index():Response
    {
        return $this->render('groups/allGroups.html.twig',[
            "groups" => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/Groupes/create", name="groups.create")
     * @param Request $request
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function createGroup(Request $request):Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class,$group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $group->setCreatedBy($this->getUser())
                ->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();
        }
        return $this->render('groups/createGroup.html.twig',[
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/AffGroupe/{id}", name="groupe.aff")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function affGroupe(Group $group):Response
    {
        if($group->getStatus() == Group::statusinfo['Privé']){
            $status = 0;
        }else{
            $status = 1;
        }
        $aff = new AffGroupe();
        $aff->setUser($this->getUser())
            ->setGroupe($group)
            ->setStatus($status);
        $group->addAffGroupe($aff);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();
        if($status==1){
            return $this->redirectToRoute('groupe.show',[
                'id'=>$group->getId()
            ]);
        }else{
            return $this->redirectToRoute('groups.allGroupePublic');
        }
    }

    /**
     * @Route("/Groupe/{id}", name="groupe.show", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function showGroupe(Group $group)
    {
        $membre = $this->getDoctrine()->getRepository(AffGroupe::class)
            ->findOneBy(['user'=>$this->getUser(),'groupe'=>$group]);
        $admin = $this->getDoctrine()->getRepository(Group::class)
            ->findOneBy(['createdBy'=>$this->getUser(),'id'=>$group->getId()]);
        /**
         * si membre du group
         * si admin du group
         * si group public
         */
        if($membre || $admin || $group->getStatus() == 0){
            if($membre || $admin){
                $post= new Post();
                $form = $this->createForm(PostGroupType::class,$post)->createView();
            }else{
                $form = null;
            }

            return $this->render('groups/show.html.twig',[
                'group'=>$group,
                'formPost'=>$form,
                'posts' => $group->getPosts(),
                'membre' => $membre,
                'admin' => $admin
            ]);
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Route("/Group/addPost/{id}",name="groupe.addPostUser", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param Group $group
     */
    public  function addPost(Request $request,Group $group){
        $user = $this->getUser();
        $groupeMember = $this->getDoctrine()->getRepository(AffGroupe::class)
            ->findOneBy(['groupe'=>$group,'user'=>$user]);
        if($groupeMember || $group->getCreatedBy()== $user){
            $post = new Post();
            $form = $this->createForm(PostGroupType::class,$post);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $post->setGroupe($group)
                    ->setCreatedBy($user)
                    ->setCreatedAt(new DateTime('now'))
                    ->setPrivat(false);
                $group->addPost($post);
                $em = $this->getDoctrine()->getManagerForClass(Group::class);
                $em->persist($group);
                $em->flush();
            }
        }
        return $this->redirectToRoute('groupe.show',[
            'id'=> $group->getId()
        ]);
    }

    /**
     * @Route("/Groupe/exit/{id}", name="groupe.exit")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function exitGroup(Group $group)
    {
        $aff = $this->getDoctrine()->getRepository(AffGroupe::class)->findOneBy([
            'user'=>$this->getUser(),
            'groupe'=>$group
        ]);
        if($aff){
            $em = $this->getDoctrine()->getManagerForClass(AffGroupe::class);
            $em->remove($aff);
            $em->flush();
        }
         return $this->redirectToRoute('groups.allGroupePublic');
    }

    /**
     * @Route("/group/post/{id}",name="groupe.post")
     * @param Post $post
     * @return Response
     */
    public function showCommentPostGroup(Post $post)
    {
        $member = false;
        foreach ($this->getUser()->getAffGroupes() as $affGroupe){
            if($affGroupe->getGroupe() == $post->getGroupe()){
                $member = true;
            }
        }
        if($post->getCreatedBy() == $this->getUser() || $member ) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);

            return $this->render('groups/show.html.twig',[
                'group'=>$post->getGroupe(),
                'formPost'=>$form->createView(),
                'post' => $post,
                "formComment" => $form->createView(),
                "editComment" =>true

            ]);


        }
        throw $this->createAccessDeniedException();
    }
}