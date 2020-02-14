<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\ligne_commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ligne_commande controller.
 * @Route("lc")
 */
class ligne_commandeController extends Controller
{
    /**
     * Lists all ligne_commande entities.
     *
     * @Route("/", name="ligne_commande_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligne_commandes = $em->getRepository('AppBundle:ligne_commande')->findAll();

        return $this->render('@Commande/ligne_commande/index.html.twig', array(
            'ligne_commandes' => $ligne_commandes,
        ));
    }

    /**
     * Creates a new ligne_commande entity.
     *
     * @Route("/new", name="ligne_commande_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligne_commande = new Ligne_commande();
        $form = $this->createForm('CommandeBundle\Form\ligne_commandeType', $ligne_commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $prix=$ligne_commande->getProduit()->getPrix();
            $qte=$ligne_commande->getQuantite();
            $prixl=$prix*$qte;
            $ligne_commande->setPrix($prixl);
            $prixt=$ligne_commande->getCommande()->getPrixTotal();
            $ligne_commande->getCommande()->setPrixTotal($prixt+$prixl);
            $em->persist($ligne_commande);
            $em->flush();

            return $this->redirectToRoute('ligne_commande_show', array('id' => $ligne_commande->getId()));
        }

        return $this->render('@Commande/ligne_commande/new.html.twig', array(
            'ligne_commande' => $ligne_commande,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligne_commande entity.
     *
     * @Route("/{id}", name="ligne_commande_show")
     * @Method("GET")
     */
    public function showAction(ligne_commande $ligne_commande)
    {

        return $this->render('@Commande/ligne_commande/show.html.twig', array(
            'ligne_commande' => $ligne_commande,
        ));
    }

    /**
     * Displays a form to edit an existing ligne_commande entity.
     *
     * @Route("/{id}/edit", name="ligne_commande_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ligne_commande $ligne_commande)
    {
        $editForm = $this->createForm('CommandeBundle\Form\ligne_commandeType', $ligne_commande);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ligne_commande_edit', array('id' => $ligne_commande->getId()));
        }

        return $this->render('@Commande/ligne_commande/edit.html.twig', array(
            'ligne_commande' => $ligne_commande,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     *
     *
     * @Route("/delete/{id}", name="ligne_commande_delete")
     *
     */
    public function deleteAction($id)
    {
        $cmd=$this->getDoctrine()->getRepository(ligne_commande::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($cmd);
        $em->flush();
        return $this->redirectToRoute('ligne_commande_index');
    }



}
