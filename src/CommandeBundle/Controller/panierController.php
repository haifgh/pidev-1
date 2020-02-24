<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\commande;
use AppBundle\Entity\ligne_commande;
use AppBundle\Entity\Produit;

use Stripe\Error\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class panierController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $panier = $session->get('panier',[]);
        $panierWithData=[];
        foreach ($panier as $id => $qte){
            $panierWithData[]=[
                'product'=>$this->getDoctrine()->getRepository(Produit::class)->find($id),
                'quantity'=>$qte
            ];
        }
        return $this->render('@Commande/panier/panier.html.twig',['items'=>$panierWithData]);
    }



    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function addAction($id, Request $request)
    {
        $session = $request->getSession();
        $panier= $session->get('panier',[]);
        $product=$this->getDoctrine()->getRepository(Produit::class)->find($id);
        if($product->getQte()>0){
        if(!empty($panier[$id])){
            if($product->getQte()>$panier[$id])
            $panier[$id]+=1;
        }else{
            $panier[$id]=1;
        }
        }
        $session->set('panier',$panier);
        return $this->redirectToRoute('shop');
    }



    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     */
    public function removeAction($id, Request $request)
    {
        $session = $request->getSession();
        $panier= $session->get('panier',[]);

            unset($panier[$id]);

        $session->set('panier',$panier);
        return $this->redirectToRoute('shop');
    }




    /**
     * @Route("/user/checkout", name="checkout")
     */
    public function checkoutAction(Request $request)
    {
        $session = $request->getSession();
        $panier = $session->get('panier',[]);
        $panierWithData=[];
        foreach ($panier as $id => $qte){
            $panierWithData[]=[
                'product'=>$this->getDoctrine()->getRepository(Produit::class)->find($id),
                'quantity'=>$qte
            ];
        }
        $em = $this->getDoctrine()->getManager();
        $commande = new commande();
        $form = $this->createForm('CommandeBundle\Form\commandeType', $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commande=$this->createOrder($request,$commande);

            return $this->render('@Commande/panier/pay.html.twig',['commande'=>$commande]);
        }




        return $this->render('@Commande/panier/checkout.html.twig',['panier'=>$panierWithData,'form'=>$form->createView()]);
    }








    /**
     * @Route("/user/charge", name="charge")
     */
    public function chargeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $token=$request->request->get('stripeToken');
        $commande_id=$request->request->get('commande');
        $commande=$this->getDoctrine()->getRepository(commande::class)->find($commande_id);
        $stripeClient = $this->get('flosch.stripe.client');
        $p=intval($commande->getPrixTotal());
        try{
            $charge=$stripeClient->createCharge( $p, 'usd', $token,'','','test');

        }
        catch (Base $e){

            return $this->render('@Commande/panier/charge.html.twig',['charge'=>[],'error'=>$e]);
        }
        $commande->setChargeId($charge->id);
        foreach ($commande->getLignesCommande() as $lc){
            $pd=$lc->getProduit();
            $pd->setQte($pd->getQte()-$lc->getQuantite());
        }
        $em->persist($commande);
        $em->flush();

        $session = $request->getSession();
        $session->set('panier',[]);
        return $this->render('@Commande/panier/charge.html.twig',['charge'=>$charge,'error'=>[]]);
    }

    private function createOrder(Request $request,commande $commande){
        $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            $panier = $session->get('panier',[]);
            $date= new \DateTime();
            $commande->setDate($date);
            $commande->setStatus('Pending');
            $commande->setUser($this->getUser());
        $em->persist($commande);
        $em->flush();

            foreach ($panier as $id => $qte){
                $lq= new ligne_commande();
                $produit=$this->getDoctrine()->getRepository(Produit::class)->find($id);
                $lq->setProduit($produit);
                $lq->setQuantite($qte);
                $lq->setPrix($produit->getPrix()*$qte);
                $commande->setPrixTotal($commande->getPrixTotal()+$lq->getPrix());
                $lq->setCommande($commande);
                $em->persist($lq);
                $em->flush();
            }
        $em->persist($commande);
        $em->flush();

            return $commande;
        }

}
