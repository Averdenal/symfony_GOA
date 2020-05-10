<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     */
    public function show(Request $request)
    {
        $param = $request->attributes->get('exception');
        $code = $param->getStatusCode();
        $message = $param->getMessage();
        return $this->render('error/index.html.twig', [
            'code' => $code,
            'message' => $message
        ]);
    }
}
