<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Commande controller.
 *
 */
class commandeController extends Controller
{
    /**
     * Lists all commande entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $commandes = $em->getRepository('AppBundle:commande')->findAll();

        return $this->render('@Commande/commande/index.html.twig', array(
            'commandes' => $commandes,
        ));
    }

    /**
     * Creates a new commande entity.
     *
     */
    public function newAction(Request $request)
    {
        $commande = new Commande();
        $form = $this->createForm('CommandeBundle\Form\commandeType', $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date= new \DateTime();
            $commande->setDate($date);
            $commande->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            $em->flush();

            return $this->redirectToRoute('commande_show', array('id' => $commande->getId()));
        }

        return $this->render('@Commande/commande/new.html.twig', array(
            'commande' => $commande,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a commande entity.
     *
     */
    public function showAction(commande $commande)
    {


        return $this->render('@Commande/commande/show.html.twig', array(
            'commande' => $commande

        ));
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction(Request $request, commande $commande)
    {

        $editForm = $this->createForm('CommandeBundle\Form\commandeType', $commande);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('@Commande/commande/edit.html.twig', array(
            'commande' => $commande,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a commande entity.
     *
     */
    public function deleteAction($id)
    {
        $cmd=$this->getDoctrine()->getRepository(commande::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($cmd);
        $em->flush();
        return $this->redirectToRoute('commande_index');
    }


}
