<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $deleteForm = $this->createDeleteForm($commande);

        return $this->render('@Commande/commande/show.html.twig', array(
            'commande' => $commande,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction(Request $request, commande $commande)
    {
        $deleteForm = $this->createDeleteForm($commande);
        $editForm = $this->createForm('CommandeBundle\Form\commandeType', $commande);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_edit', array('id' => $commande->getId()));
        }

        return $this->render('@Commande/commande/edit.html.twig', array(
            'commande' => $commande,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commande entity.
     *
     */
    public function deleteAction(Request $request, commande $commande)
    {
        $form = $this->createDeleteForm($commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commande);
            $em->flush();
        }

        return $this->redirectToRoute('commande_index');
    }

    /**
     * Creates a form to delete a commande entity.
     *
     * @param commande $commande The commande entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(commande $commande)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commande_delete', array('id' => $commande->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
