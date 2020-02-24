<?php

namespace ProduitBundle\Controller;

use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProduitBundle:Default:index.html.twig');
    }
    /**
     * Lists all categorie entities.
     *
     * @Route("/shop/", name="shop")
     * @Method("GET")
     */
    public function shopAction(Request $request)
    {


        //$p = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        $c = $this->getDoctrine()->getRepository(categorie::class)->findAll();
        $em = $this->getDoctrine()->getManager();
        $p= $em->createQuery('select p from AppBundle:Produit p where p.qte>0');

        $paginator=$this->get('knp_paginator');
        $pagination=$paginator->paginate(
            $p,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',1)
        );

        return $this->render('@Produit/Default/shop.html.twig', array(
            'p' => $pagination,
            'c'=> $c
        ));
    }
    /**
     * Lists all categorie entities.
     *
     * @Route("/shop/{id}", name="shop_cat")
     * @Method("GET")
     */
    public function shopcatAction($id,Request $request)
    {
        $c = $this->getDoctrine()->getRepository(categorie::class)->findAll();
        //$cp = $this->getDoctrine()->getRepository(categorie::class)->find($id);
        $catp=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.categorie=:p and p.qte>0')->setParameter('p',$id);
        $paginator=$this->get('knp_paginator');
        $pagination=$paginator->paginate(
            $catp,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',1)
        );
        return $this->render('@Produit/Default/shopcat.html.twig', array(
            'c'=>$c,
            'cp'=> $pagination,

        ));
    }




}
