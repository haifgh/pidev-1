<?php

namespace PromoBundle\Controller;

use AppBundle\Entity\ligne_promotion;
use AppBundle\Entity\Produit;
use AppBundle\Entity\Promotion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ligne_promotion controller.
 *
 * @Route("ligne_promotion")
 */
class ligne_promotionController extends Controller
{
    /**
     * Lists all ligne_promotion entities.
     *
     * @Route("/", name="ligne_promotion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligne_promotions = $em->getRepository('AppBundle:ligne_promotion')->findAll();

            return $this->render('@Promo/ligne_promotion/index.html.twig',
                array( 'ligne_promotions' => $ligne_promotions));
    }

    /**
     * Creates a new ligne_promotion entity.
     *
     * @Route("/new/{produit}/{promotion}", name="ligne_promotion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $produit,$promotion)
    {

        $qte=$request->query->get('qte');//qte eli da5alha ladmin


            $em = $this->getDoctrine()->getManager();
        $produit= $em->getRepository(Produit::class)->find($produit);
        if($qte<=$produit->getQte()){//kan qte eli da5alha ladmin qte mta3 produit fi base
        $lp=$em->getRepository(ligne_promotion::class)->findByProduit($produit);
          if($lp==null){//w ba3d nchouf kanou mouch mawjoud fi promo o5ra 9bal
              $ligne_promotion = new Ligne_promotion();//ajout ligne promotion
           $promotion= $em->getRepository(Promotion::class)->find($promotion);
           $ligne_promotion->setPromotion($promotion);
           $ligne_promotion->setProduit($produit);
           $ligne_promotion->setQuantite($qte);
           $em->persist($ligne_promotion);
           $em->flush();
              $prix= ($produit->getPrix()*$promotion->getTauxReduction())/100;
              $produit->setPrixPromo($prix);
              $em->flush();
          }
           else{

               $quantite=$lp[0]->getQuantite();//[] find by traja3li array
               if($quantite+$qte<=$produit->getQte()){//tester
               $lp[0]->setQuantite($quantite+$qte);
               $em->flush();}
               $ligne_promotion=$lp[0];

           }
            return $this->redirectToRoute('ligne_promotion_index', array('id' => $ligne_promotion->getId()));

        }
        else{
            return $this->redirectToRoute('ligne_promotion_index');

        }




    }

    /**
     * Finds and displays a ligne_promotion entity.
     *
     * @Route("/{id}", name="ligne_promotion_show")
     * @Method("GET")
     */
    public function showAction(ligne_promotion $ligne_promotion)
    {

        return $this->render('@Promo/ligne_promotion/show.html.twig', array(
            'ligne_promotion' => $ligne_promotion,
        ));

    }

    /**
     * Displays a form to edit an existing ligne_promotion entity.
     *
     * @Route("/{id}/edit", name="ligne_promotion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ligne_promotion $ligne_promotion)
    {

        $editForm = $this->createForm('PromoBundle\Form\ligne_promotionType', $ligne_promotion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ligne_promotion_edit', array('id' => $ligne_promotion->getId()));
        }

        return $this->render('@Promo/ligne_promotion/edit.html.twig', array(
            'ligne_promotion' => $ligne_promotion,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a ligne_promotion entity.
     *
     * @Route("/delete/{id}", name="ligne_promotion_delete")
     * @Method("DELETE")
     */
    public function deleteAction ($id)
    {
        $em = $this ->getDoctrine()->getManager();
        $promo = $em -> getRepository(ligne_promotion::class)->find($id);
        $em -> remove($promo);
        $em -> flush();
        return $this ->redirectToRoute("ligne_promotion_index");
    }




}
