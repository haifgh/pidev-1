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
    /**
     * @Route("/panier", name="panier")
     */
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
        if(!empty($panier[$id])){
            $panier[$id]+=1;
        }else{
            $panier[$id]=1;
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
        return $this->render('@Commande/panier/checkout.html.twig',['panier'=>$panierWithData]);
    }




    /**
     * @Route("/user/payer", name="payer")
     */
    public function payerAction(Request $request)
    {   $em = $this->getDoctrine()->getManager();
        $add = $request->query->get('adresse');
        if ($request->getSession()->get('panier',[])==[]){
            return $this->redirectToRoute('checkout');
        }
        $commande=$this->createOrder($request);
        $commande->setAdresse($add);
        $em->persist($commande);
        $em->flush();
        return $this->render('@Commande/panier/pay.html.twig',['commande'=>$commande]);
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
            $commande->setChargeId($charge->id);

            $em->persist($commande);
            $em->flush();
        }
        catch (Base $e){

            return $this->render('@Commande/panier/charge.html.twig',['charge'=>[],'error'=>$e]);
        }


        $session = $request->getSession();
        $session->set('panier',[]);
        return $this->render('@Commande/panier/charge.html.twig',['charge'=>$charge,'error'=>[]]);
    }

    private function createOrder(Request $request){
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            $panier = $session->get('panier',[]);
            $commande = new commande();
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
