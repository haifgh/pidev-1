<?php

namespace EventBundle\Controller;

use AppBundle\Entity\Evenement;
use AppBundle\Entity\liste_participants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Liste_participant controller.
 *
 * @Route("liste_participants")
 */
class liste_participantsController extends Controller
{
    /**
     * Lists all liste_participant entities.
     *
     * @Route("/", name="liste_participants_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $liste_participants = $em->getRepository('AppBundle:liste_participants')->findAll();

        return $this->render('@Event/liste_participants/index.html.twig', array(
            'liste_participants' => $liste_participants,
        ));
    }

    /**
     * Creates a new liste_participant entity.
     *
     * @Route("/new", name="liste_participants_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $liste_participant = new liste_participants();
        $form = $this->createForm('EventBundle\Form\liste_participantsType', $liste_participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($liste_participant);
            $em->flush();

            return $this->redirectToRoute('liste_participants_show', array('id' => $liste_participant->getId()));
        }

        return $this->render('@Event/liste_participants/new.html.twig', array(
            'liste_participant' => $liste_participant,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a liste_participant entity.
     *
     * @Route("/{id}", name="liste_participants_show")
     * @Method("GET")
     */
    public function showAction(liste_participants $liste_participant)
    {
        $deleteForm = $this->createDeleteForm($liste_participant);

        return $this->render('@Event/liste_participants/show.html.twig', array(
            'liste_participant' => $liste_participant,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing liste_participant entity.
     *
     * @Route("/{id}/edit", name="liste_participants_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, liste_participants $liste_participant)
    {
        $deleteForm = $this->createDeleteForm($liste_participant);
        $editForm = $this->createForm('EventBundle\Form\liste_participantsType', $liste_participant);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('liste_participants_edit', array('id' => $liste_participant->getId()));
        }

        return $this->render('@Event/liste_participants/edit.html.twig', array(
            'liste_participant' => $liste_participant,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a liste_participant entity.
     *
     * @Route("/delete/{id}", name="liste_participants_delete")
     * @Method("DELETE")
     */

      public function deleteAction($id)
      {

          $em = $this->getDoctrine()->getManager();
          $listP= $em->getRepository(liste_participants::class)->find($id);

          $em->remove($listP);

          $em->flush();
          return $this->redirectToRoute("liste_participants_index");
      }

    /**
     * Creates a form to delete a liste_participant entity.
     *
     * @param liste_participants $liste_participant The liste_participant entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(liste_participants $liste_participant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('liste_participants_delete', array('id' => $liste_participant->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
