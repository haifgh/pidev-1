<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class shopController extends Controller
{
    /**
     * @Route("/shop", name="shop")
     */
    public function shopAction(Request $request)
    {
       $prdts= $this->getDoctrine()->getRepository(Produit::class)->findAll();
       return $this->render('shop.html.twig',['produits'=>$prdts]);
    }
}