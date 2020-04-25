<?php


namespace App\Controller\Editeur;


use App\Entity\Etablissement;
use App\Form\EtablissementType;
use App\Repository\EtablissementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class editEtablissementController extends AbstractController
{
    /**
     * @var EtablissementRepository
     */
    private $repository;

    private $em;

    /**
     * editEtablissementController constructor.
     * @param EtablissementRepository $repository
     * @param ManagerRegistry $em
     */
    public function __construct(EtablissementRepository $repository, ManagerRegistry $em)
    {
        $this->repository = $repository;


        $this->em = $em;
    }

    /**
     * @Route("etablissement/edit/{id}", name="etablissement.edit")
     * @param Etablissement $etablissement
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function editEtablissement(Etablissement $etablissement, Request $request):Response
    {
        $form = $this->createForm(EtablissementType::class,$etablissement);

        $form->handleRequest($request);
        if($form->isSubmitted()and $form->isValid()){
            $etablissement->setUpdateat(new \DateTime('now',new \DateTimeZone('Europe/Paris')));
            $this->em->getManager()->flush();
        }
        return $this->render('etablissements/editEtablissement.html.twig',[
            'etablissement' => $etablissement,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("etablissement/new", name="etablissement.new")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function newEtablissement(Request $request):Response
    {
        $etablissement = new Etablissement();
        $form = $this->createForm(EtablissementType::class,$etablissement);

        $form->handleRequest($request);
        if($form->isSubmitted()and $form->isValid()){
            $etablissement->setUpdateat(new \DateTime('now',new \DateTimeZone('Europe/Paris')));
            $this->em->getManager()->persist($etablissement);
            $this->em->getManager()->flush();
            return $this->redirectToRoute('etablissements.getAllEtablissement');
        }
        return $this->render('etablissements/newEtablissement.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}