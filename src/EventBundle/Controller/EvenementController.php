<?php

namespace EventBundle\Controller;

use AppBundle\Entity\Evenement;
use AppBundle\Entity\liste_participants;
use AppBundle\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Evenement controller.
 *
 * @Route("")
 */
class EvenementController extends Controller
{
    /**
     * Lists all evenement entities.
     *
     * @Route("/admin/evenement", name="evenement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $evenements = $em->getRepository('AppBundle:Evenement')->findAll();

        return $this->render('@Event/evenement/index.html.twig', array(
            'evenements' => $evenements,
        ));
    }

    /**
     * Creates a new evenement entity by admin
     *
     * @Route("/admin/evenement/new", name="evenement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $evenement = new Evenement();
        $evenement->setDateDebut(new DateTime('now '));
        $evenement->setDateFin(new DateTime('now '));
        $user = $this->getUser();
        $evenement ->setUser($user);
        $form = $this->createForm('EventBundle\Form\EvenementType', $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $evenement->UploaderProfilePicture();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('evenement_index', array('id' => $evenement->getId()));
        }

        return $this->render('@Event/evenement/new.html.twig', array(
            'evenement' => $evenement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new evenement entity by user
     *
     * @Route("/user/evenement/new", name="evenement_newUser")
     * @Method({"GET", "POST"})
     */
    public function newUserEventAction(Request $request)
    {
        $evenement = new Evenement();
        $evenement->setDateDebut(new DateTime('now '));
        $evenement->setDateFin(new DateTime('now '));
        $user = $this->getUser();
        $evenement ->setUser($user);
        $form = $this->createForm('EventBundle\Form\EvenementType', $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $evenement->UploaderProfilePicture();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('evenement_view', array('id' => $evenement->getId()));
        }

        return $this->render('@Event/evenement/newUserEvent.html.twig', array(
            'evenement' => $evenement,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a evenement entity.
     *
     * @Route("/admin/evenement/{id}", name="evenement_show")
     * @Method("GET")
     */
    public function showAction(Evenement $evenement)
    {

        return $this->render('@Event/evenement/show.html.twig', array(
            'evenement' => $evenement,
        ));
    }

    /**
     * Displays a form to edit an existing evenement entity.
     *
     * @Route("/admin/evenemnt/{id}/edit", name="evenement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Evenement $evenement)
    {
        $deleteForm = $this->createDeleteForm($evenement);
        $editForm = $this->createForm('EventBundle\Form\EvenementType', $evenement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evenement_edit', array('id' => $evenement->getId()));
        }

        return $this->render('@Event/evenement/edit.html.twig', array(
            'evenement' => $evenement,
            'edit_form' => $editForm->createView(),
        ));
    }
    /**
     * .
     *
     * @Route("/user/participer/{id}", name="participer")
     *
     */
     public function participerAction($id)
     {
      $event = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
      $lp = $this->getDoctrine()->getRepository(liste_participants::class)->findBy(['user'=>$this->getUser(),'evenement'=>$event]);
      if($lp == null) {
          $listP = new liste_participants();
          $listP->setUser($this->getUser());
          $listP->setEvenement($event);
          $listP->setDateParticipation(new DateTime('now'));

          $event->setNbrePlaces($event->getNbrePlaces()-1);
      }
            $em=$this->getDoctrine()->getManager();
      $em->persist($listP);
      $em->flush();


      return $this->redirectToRoute('evenement_view');
     }


    /**
     * .
     *
     * @Route("/user/quitter/{id}", name="quitter")
     *
     */
    public function quitterAction($id)
    { $em=$this->getDoctrine()->getManager();
        $event = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $lp = $this->getDoctrine()->getRepository(liste_participants::class)->findBy(['user'=>$this->getUser(),'evenement'=>$event]);
        if($lp != null) {
           $em->remove($lp[0]);
            $event->setNbrePlaces($event->getNbrePlaces()+1);
        }


        $em->flush();
        return $this->redirectToRoute('evenement_view');
    }


    /**
     * Deletes a evenement entity.
     *
     * @Route("/admin/delete/{id}", name="evenement_delete")
     *
     */
    public function deleteAction($id)
    {


        $em = $this->getDoctrine()->getManager();
        $event= $em->getRepository(Evenement::class)->find($id);

        $em->remove($event);

        $em->flush();
        return $this->redirectToRoute("evenement_index");
    }
    /**
     *  a evenement entity.
     *
     * @Route("/user/evenement", name="evenement_view")
     *
     */
    public function afficheAction()
    {
        $q=$this->getDoctrine()->getManager()->createQuery('select n from AppBundle:Evenement n
        where n.dateFin > CURRENT_DATE() order by n.nbrePlaces desc ') ;
        $events=$q->getResult();
        //$events=$this->getDoctrine()->getRepository(Evenement::class)->findAll();
        return $this->render('@Event/evenement/viewEvents.html.twig', array(
            'events'=>$events
        ));

    }


    /**
     * Creates a form to delete a evenement entity.
     *
     * @param Evenement $evenement The evenement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Evenement $evenement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evenement_delete', array('id' => $evenement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
