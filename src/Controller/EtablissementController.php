<?php


namespace App\Controller;

use App\Entity\Etablissement;
use App\Repository\EtablissementRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablissementController extends AbstractController
{
    /**
     * @var EtablissementRepository
     */
    private $repository;


    /**
     * EtablissementController constructor.
     * @param EtablissementRepository $repository
     */
    public function __construct(EtablissementRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/etablissements",name="etablissements.getAllEtablissement")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function getAllEtablissement(PaginatorInterface $paginator, Request $request):Response
    {
        $etablissements = $this->repository->findAllEtablissementOpen();
        $etabs = $paginator->paginate(
            $etablissements,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('etablissements/allEtablissements.html.twig',[
            "etablissements"=> $etabs
        ]);
    }

    /**
     * @Route("/etablissements/show/{slug}_{id}",name="etablissements.getEtablissementById")
     * @param Etablissement $etablissement
     * @return Response
     */
    public function getEtablissement(Etablissement $etablissement):Response
    {
        return $this->render('etablissements/showEtablissements.html.twig',[
            'etablissement' => $etablissement
        ]);
    }


}