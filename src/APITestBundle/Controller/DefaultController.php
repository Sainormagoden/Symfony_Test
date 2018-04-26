<?php

namespace APITestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use APITestBundle\Service\Meteo;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@APITest/Default/index.html.twig');
    }

    /**
     * @Rest\View(StatusCode = 201)
     */
    public function APITestAction(string $lieu, string $_locale, Request $request, Meteo $meteo){
        $response=$meteo->traitementRender($lieu, $_locale);
        if($request->request->get('meteo_actuel')){
            $response=$meteo->traitementRender($lieu, $_locale);
            return new JsonResponse($response);
        }
        return $this->render('@APITest/Default/meteo.html.twig', array(
            'response'=>$response,
            'lieu'=>$lieu
        ));
    }
}
