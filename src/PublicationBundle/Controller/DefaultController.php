<?php

namespace PublicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/publication")
     */
    public function indexAction()
    {
        return $this->render('PublicationBundle:Default:index.html.twig');
    }
}
