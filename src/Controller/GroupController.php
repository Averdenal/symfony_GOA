<?php


namespace App\Controller;


use App\Entity\AffGroupe;
use App\Entity\Group;
use App\Entity\Post;
use App\Form\GroupType;
use App\Form\PostType;
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
     * @Route("/Groupe/show/{id}", name="groupe.show", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function showGroupe(Group $group)
    {
        $membre = $this->getDoctrine()->getRepository(AffGroupe::class)
            ->findOneBy(['user'=>$this->getUser(),'groupe'=>$group]);
        $admin = $this->getDoctrine()->getRepository(Group::class)
            ->findOneBy(['createdBy'=>$this->getUser(),'id'=>$group->getId()]);
        $post= new Post();
        $form = $this->createForm(PostType::class,$post);
        if($membre || $admin){

            return $this->render('groups/show.html.twig',[
                'group'=>$group,
                'formPost'=>$form->createView(),
                'posts' => $group->getPosts()
            ]);
        }
    }

    /**
     * @Route("/Group/addPost/{id}",name="groupe.addPostUser")
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
            $form = $this->createForm(PostType::class,$post);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $post->setGroupe($group)
                    ->setCreatedBy($user)
                    ->setCreatedAt(new DateTime('now'));
                $group->addPost($post);
                $this->getDoctrine()->getManager()->flush();
            }
        }
    }
}