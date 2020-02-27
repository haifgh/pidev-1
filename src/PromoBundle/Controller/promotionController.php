<?php

namespace PromoBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\ligne_promotion;
use AppBundle\Entity\Produit;
use AppBundle\Entity\Promotion;
use http\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;


/**
 * Promotion controller.
 *
 * @Route("")
 */
class promotionController extends Controller
{
    /**
     * Lists all promotion entities.
     *
     * @Route("/admin/promotion", name="promotion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $promotions = $em->getRepository('AppBundle:Promotion')->findAll();

        return $this->render('@Promo/promotion/index.html.twig', array(
            'promotions' => $promotions,
        ));
    }

    /**
     * Creates a new promotion entity.
     *
     * @Route("/admin/promotion/new", name="promotion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm('PromoBundle\Form\promotionType', $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();
            $twilio=$this->get('twilio.api');
            $mm= $twilio->account->messages->sendMessage(
                '+17608197805',
                '+21629628289', 'New promotion at Huntkingdom.tn Visit us !'
            );
            dump($mm);
            //return $this->redirectToRoute('promotion_show', array('id' => $promotion->getId()));
            return $this ->redirectToRoute("promotion_index");
        }

        return $this->render('@Promo/promotion/new.html.twig', array(
            'promotion' => $promotion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a promotion entity.
     *
     * @Route("/admin/promotion/{id}", name="promotion_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $q=$em->getRepository('AppBundle:Promotion')->find($id);//18/02
        return $this->render('@Promo/promotion/show.html.twig', array(
            'promotion' => $q,));
    }
    /**
     * Finds and displays a promotion entity.
     *
     * @Route("/admin/{id}/{message}", name="promotion_AjoutProduct")
     * @Method("GET")
     */
    public function showAllProductAction(Request $request,promotion $promotion,$message=null)//call onclick Add
    {   $p=new Produit();
        $form=$this->createForm('PromoBundle\Form\produitType',$p);
        $form->handleRequest($request);
        $reader=$this->getDoctrine()->getRepository(Produit :: class)->findAll();
        if($form->isSubmitted()&&$form->isValid()){
            $q=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.nom like :pr')->setParameter('pr','%'.$p->getNom().'%');
            $reader=$q->getResult();
            $message=null;
        }
        return $this->render('@Promo/promotion/showProduct.html.twig', array(
            'reader'=>$reader,
            'promo'=>$promotion,
            'message'=>$message,
            'form'=>$form->createView()
        ));

    }



    /**
     * Displays a form to edit an existing promotion entity.
     *
     * @Route("/admin/promotion/{id}/edit", name="promotion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, promotion $promotion)
    {
        $editForm = $this->createForm('PromoBundle\Form\promotionType', $promotion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('promotion_edit', array('id' => $promotion->getId()));
            return $this ->redirectToRoute("promotion_index");
        }

        return $this->render('@Promo/promotion/edit.html.twig', array(
            'promotion' => $promotion,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a promotion entity.
     *
     * @Route("/admin/promotion/delete/{id}", name="promotion_delete")dB
     * @Method("DELETE")
     */
    public function deleteAction ($id)
    {
        $em = $this ->getDoctrine()->getManager();
        $promo = $em -> getRepository(Promotion::class)->find($id);
        $em -> remove($promo);
        $em -> flush();
        return $this ->redirectToRoute("promotion_index");
    }
    /**
     * Deletes a promotion entity.
     *
     * @Route("/admin/promotion/prod_del/{id}", name="prod_delete")dB
     * @Method("DELETE")
     */
    public function deleteProduct($id){
        $em = $this ->getDoctrine()->getManager();
        $product = $em -> getRepository(ligne_promotion::class)->find($id);
        $em -> remove($product);
        $em -> flush();
        return $this ->redirectToRoute("promotion_index");

    }




}
