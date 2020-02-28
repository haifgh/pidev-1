<?php

namespace GuidesBundle\Controller;

use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Guide;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commentaire controller.
 *
 */
class CommentaireController extends Controller
{
    /**
     * Lists all commentaire entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $commentaires = $em->getRepository('AppBundle:Commentaire')->findAll();

        return $this->render('@Guides/commentaire/index.html.twig', array(
            'commentaires' => $commentaires,
        ));
    }

    /**
     * Creates a new commentaire entity.
     *
     */
    public function newAction(Request $request)
    {

        $commentaire = new Commentaire();
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($request->get('id'));
        $commentaire->setDate(new \DateTime('now'));
        $commentaire->setGuide($guide);
        $commentaire->setUser($this->getUser());
        $commentaire->setContenu($request->get('comment'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($commentaire);
        $em->flush();

        return $this->redirectToRoute('guide_details', array('id' => $request->get('id')));


    }

    /**
     * Finds and displays a commentaire entity.
     *
     */
    public function showAction(Commentaire $commentaire)
    {
        $deleteForm = $this->createDeleteForm($commentaire);

        return $this->render('@Guides/commentaire/show.html.twig', array(
            'commentaire' => $commentaire,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing commentaire entity.
     *
     */
    public function editAction(Request $request, Commentaire $commentaire)
    {
        $deleteForm = $this->createDeleteForm($commentaire);
        $editForm = $this->createForm('GuidesBundle\Form\CommentaireType', $commentaire);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('guide_details', array('id' => $commentaire->getGuide()->getId()));
        }

        return $this->render('@Guides/commentaire/edit.html.twig', array(
            'commentaire' => $commentaire,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commentaire entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaire::class)->find($id);

        $em->remove($commentaire);
        $em->flush();


        return $this->redirectToRoute('commentaire_index');
    }

    public function delete2Action($id, $idGuide)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaire::class)->find($id);

        $em->remove($commentaire);
        $em->flush();


        return $this->redirectToRoute('guide_details', array('id'=>$idGuide));
    }

    /**
     * Creates a form to delete a commentaire entity.
     *
     * @param Commentaire $commentaire The commentaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commentaire $commentaire)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commentaire_delete', array('id' => $commentaire->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
