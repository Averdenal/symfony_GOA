<?php


namespace App\Controller;


use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
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
     */
    public function index():Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('groups/allGroups.html.twig',[
            "groups" => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/Groupes/create", name="groups.create")
     * @param Request $request
     */
    public function createGroup(Request $request):Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
}