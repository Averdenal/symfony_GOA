<?php


namespace App\Controller;


use App\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}