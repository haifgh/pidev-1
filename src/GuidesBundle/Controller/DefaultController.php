<?php

namespace GuidesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GuidesBundle:Default:index.html.twig');
    }
}
