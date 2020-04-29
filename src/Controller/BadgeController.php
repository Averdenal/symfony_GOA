<?php


namespace App\Controller;


use App\Entity\Badge;
use App\Form\BadgeType;
use App\Repository\BadgeRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    /**
     * @var BadgeRepository
     */
    private $repository;

    /**
     * BadgeController constructor.
     * @param BadgeRepository $repository
     */
    public function __construct(BadgeRepository $repository)
    {
        $this->repository = $repository;

    }

    /**
     * @Route("/badges", name="badges.allBadges")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('badges/allBadges.html.twig',[
            'badges' => $this->repository->findAll(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/badges/create", name="badges.createBadges")
     * @return Response
     * @throws Exception
     */
    public function createBadge(Request $request):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $badge = new Badge();
        $form = $this->createForm(BadgeType::class,$badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($badge);
            $entityManager->flush();
        }
        return $this->render('badge/createBadge.html.twig',[
            'form'=> $form->createView()
        ]);
    }
}