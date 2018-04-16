<?php

namespace stagiaire\testBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@stagiairetest/lucky/base.html.twig', 'test');
    }
}
