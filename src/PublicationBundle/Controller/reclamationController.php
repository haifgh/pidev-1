<?php

namespace PublicationBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\reclamation;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Reclamation controller.
 *
 * @Route("reclamation")
 */
class reclamationController extends Controller
{
    /**
     * Lists all reclamation entities.
     *
     * @Route("/", name="reclamation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reclamations = $em->getRepository('AppBundle:reclamation')->findByUser($this->getUser());

        return $this->render('@Publication/reclamation/index.html.twig', array(
            'reclamations' => $reclamations,
        ));
    }

    /**
     * Lists all reclamation entities.
     *
     * @Route("/reclamationShow", name="reclamationa_index")
     * @Method("GET")
     */
    public function indexaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $reclamations = $em->getRepository(reclamation::class)->findAll();
        $paginator = $this->get('knp_paginator');
        $paginator = $paginator->paginate(
            $reclamations,
            $request->query->getInt('page',  1),2

        );
        return $this->render('@Publication/reclamation/reclamationa.html.twig', array(
            'reclamations' => $paginator,
        ));
    }


    /**
     * Creates a new reclamation entity.
     *
     * @Route("/new/{id}", name="reclamation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request,$id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $reclamation = new Reclamation();
        $reclamation->setReclamer($user);
        $reclamation->setDate(new \DateTime('now'));
        $reclamation->setUser($this->getUser());
        $form = $this->createForm('PublicationBundle\Form\reclamationType', $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();

            return $this->redirectToRoute('reclamation_show', array('id' => $reclamation->getId()));
        }

        return $this->render('@Publication/reclamation/new.html.twig', array(
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a reclamation entity.
     *
     * @Route("/{id}", name="reclamation_show")
     * @Method("GET")
     */
    public function showAction(reclamation $reclamation)
    {
        $deleteForm = $this->createDeleteForm($reclamation);

        return $this->render('@Publication/reclamation/show.html.twig', array(
            'reclamation' => $reclamation,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Finds and displays a reclamation entity.
     *
     * @Route("/show/{id}", name="reclamationa_show")
     * @Method("GET")
     */
    public function showaAction(reclamation $reclamation)
    {
        $deleteForm = $this->createDeleteForm($reclamation);

        return $this->render('@Publication/reclamation/show_admin.html.twig', array(
            'reclamation' => $reclamation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing reclamation entity.
     *
     * @Route("/{id}/edit", name="reclamation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, reclamation $reclamation)
    {
        $deleteForm = $this->createDeleteForm($reclamation);
        $editForm = $this->createForm('PublicationBundle\Form\reclamationType', $reclamation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reclamation_index', array('id' => $reclamation->getId()));
        }

        return $this->render('@Publication/reclamation/edit.html.twig', array(
            'reclamation' => $reclamation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a reclamation entity.
     *
     * @Route("/delete/{id}", name="reclamation_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this ->getDoctrine() ->getManager();
        $recl = $em -> getRepository(reclamation::class)->find($id);
        $em->remove($recl);
        $em->flush();
        return $this->redirectToRoute('reclamation_index');
    }

    /**
     * Creates a form to delete a reclamation entity.
     *
     * @param reclamation $reclamation The reclamation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(reclamation $reclamation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reclamation_delete', array('id' => $reclamation->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
    /**
     * Lists all reclamation entities.
     *
     * @Route("/block/{id}", name="block_user")
     * @Method("GET")
     */
    public function BlockUserAction($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setEnabled(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('reclamationa_index');
    }
    /**
     * Lists all reclamation entities.
     *
     * @Route("/unblock/{id}", name="unblock_user")
     * @Method("GET")
     */
    public function UnBlockUserAction($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setEnabled(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('reclamationa_index');
    }
}
