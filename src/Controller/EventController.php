<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events.allEvents")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('events/allEvent.html.twig');
    }
}