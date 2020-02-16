<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\commande;
use AppBundle\Entity\ligne_commande;
use AppBundle\Entity\Produit;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * @Route("/payer", name="payer")
     */
    public function payerAction(Request $request)
    {
        return $this->render('@Commande/panier/pay.html.twig');
    }
    /**
     * @Route("/charge", name="charge")
     */
    public function chargeAction(Request $request)
    {
        $token=$request->request->get('stripeToken');
        $stripeClient = $this->get('flosch.stripe.client');
        $stripeClient->createCharge(100, 'usd', $token,'','','hello');

        return $this->render('@Commande/panier/charge.html.twig',['token'=>$token]);
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
                $lq->setCommande($commande);
                $em->persist($lq);
                $em->flush();
            }


            return $commande;
        }

}
