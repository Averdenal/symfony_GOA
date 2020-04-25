<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @Route("/Groupes", name="groups.allGroupePublic")
     * @return Response
     */
    public function index():Response
    {
        return $this->render('groups/allGroups.html.twig');
    }
}