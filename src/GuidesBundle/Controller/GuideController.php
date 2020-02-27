<?php

namespace GuidesBundle\Controller;

use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Guide;
use AppBundle\Entity\Likes;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Guide controller.
 *
 */
class GuideController extends Controller
{
    /**
     * Lists all guide entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $guides = $em->getRepository('AppBundle:Guide')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $guides,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );
        return $this->render('@Guides/guide/index.html.twig', array(
            'guides' => $pagination
        ));
    }

    public function index2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

       $guides = $em->getRepository('AppBundle:Guide')->findAll();
        $guide = $em->getRepository('AppBundle:Guide')->findAllGuide();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $guides,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 2)
        );
        return $this->render('@Guides/guide/index2.html.twig', array(
            'guides' => $pagination,'guide'=>$guide
        ));
    }

    /**
     * Creates a new guide entity.
     *
     */
    public function newAction(Request $request)
    {
        $guide = new Guide();
        $guide->setDateCreation(new \DateTime('now'));
        $form = $this->createForm('GuidesBundle\Form\GuideType', $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $guide->UploadProfilePicture();
            $guide->setNote(0);
            $em->persist($guide);
            $em->flush();

            return $this->redirectToRoute('guide_show', array('id' => $guide->getId()));
        }

        return $this->render('@Guides/guide/new.html.twig', array(
            'guide' => $guide,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a guide entity.
     *
     */
    public function showAction(Guide $guide)
    {
        $deleteForm = $this->createDeleteForm($guide);

        return $this->render('@Guides/guide/show.html.twig', array(
            'guide' => $guide,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing guide entity.
     *
     */
    public function editAction(Request $request, Guide $guide)
    {
        $deleteForm = $this->createDeleteForm($guide);
        $editForm = $this->createForm('GuidesBundle\Form\GuideType', $guide);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('guide_show', array('id' => $guide->getId()));
        }

        return $this->render('@Guides/guide/edit.html.twig', array(
            'guide' => $guide,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a guide entity.
     *
     */
    public function deleteAction(Request $request, Guide $guide)
    {
        $form = $this->createDeleteForm($guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($guide);
            $em->flush();
        }

        return $this->redirectToRoute('guide_index');
    }

    public function detailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $guide = $em->getRepository('AppBundle:Guide')->find($id);
        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(array('guide' => $guide));
        $isGuideLiked = $em->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $this->getUser()));
        $nbLikes = $em->getRepository(Likes::class)->countByGuide($guide);

        return $this->render('@Guides/guide/detailsguide.html.twig', array('titre' => $guide->getTitre(),
            'datecreation' => $guide->getDateCreation(),
            'description' => $guide->getDescription(),
            'commentaire' => $commentaire,
            'id' => $guide->getId(),
            'lien' => $guide->getLien(),
            'photos' => $guide->getPhoto(),
            'isGuideLiked' => $isGuideLiked,
            'nbLikes'=> $nbLikes
        ));

    }

    /**
     * Creates a form to delete a guide entity.
     *
     * @param Guide $guide The guide entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Guide $guide)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('guide_delete', array('id' => $guide->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $guides = $em->getRepository('AppBundle:Guide')->findEntitiesByString($requestString);
        if (!$guides) {
            $result['guides']['error'] = "Post Not found :( ";
        } else {
            $result['guides'] = $this->getRealEntities($guides);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($guides)
    {
        foreach ($guides as $guides) {
            $realEntities[$guides->getId()] = [$guides->getPhoto(), $guides->getTitre()];

        }
        return $realEntities;
    }

    /**public function addCommentAction(Request $request, UserInterface $user)
     * {
     *
     *
     * $ref = $request->headers->get('referer');
     *
     * $guide = $this->getDoctrine()
     * ->getRepository(Guide::class)
     * ->findGuideByid($request->request->get('guide_id'));
     *
     * $comment = new commentaire();
     *
     * $comment->setUser($user);
     * $comment->setGuide($guide);
     * $comment->setDate(new \DateTime('now'));
     * $comment->setContenu($request->request->get('comment'));
     * $em = $this->getDoctrine()->getManager();
     * $em->persist($comment);
     * $em->flush();
     *
     * $this->addFlash(
     * 'info', 'Comment published !.'
     * );
     *
     * return $this->redirect($ref);
     *
     * }
     **/
    public function LikeAction(Request $request, Guide $guide)
    {
        if ($request->isXmlHttpRequest()) {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            //$guide = $em->getRepository("AppBundle:Guide")->find($id);
            $isGuideLiked = $this->getDoctrine()->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $user));
            if (!$isGuideLiked) {
                $like = new Likes();
                $like->setUser($user);
                $like->setGuide($guide);
                $em->persist($like);
                //$em->persist($guide);
                $em->flush();
                return new JsonResponse();
            }
        } else
            return new Response('Error!', 400);
    }

    public function DislikeAction(Request $request, Guide $guide)
    {
        if ($request->isXmlHttpRequest()) {
            //$guide->setLike($guide->getLike()-1);
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $like = $em->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $user));
            //$em->persist($guide);
            if($like)
            $em->remove($like);
            $em->flush();
            return new JsonResponse($like);
        } else
            return new Response('Error!', 400);
    }

    public function addRateAction($id, $g, $note)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($g);
        $rate = $this->getDoctrine()->getRepository(Rate::class)->findOneBy(array('user' => $user, 'guide' => $guide));
        $rates = $this->getDoctrine()->getRepository(Rate::class)->findBy(array('guide' => $guide));
        if (!$rate) {
            $avis = 0;
            $moyenne = 0;
            if ($rates) {
                foreach ($rates->getNote() as $n) {
                    $avis = $avis + $n;

                }
                $moyenne = (($avis + $note) / (count($rates) + 1));
            } else {

                $moyenne = (($note) / (count($rates) + 1));
            }


            $rate = new Rate();
            $rate->setGuide($guide);
            $rate->setUser($user);
            $rate->setNote($note);
            $guide->setNote($moyenne);
            $em = $this->getDoctrine()->getManager();
            $em->persist($guide);
            $em->persist($rate);
            $em->flush();
            return new Response("Done");
        } else {
            return new Response("error");
        }

    }

    public function returnPDFAction($id)
    {
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($id);
        $name = md5(uniqid());

        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                '@Guides/guide/PDF.html.twig', array('guide' => $guide)

            ),
            "C:/Users/rejeb/". $name . ".pdf"
        );
        return $this->redirectToRoute('guide_details', array('id' => $id));
    }

    public function rechercherAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guide = $em->getRepository('AppBundle:Guide')->findAllGuide();

        $guides = $em->getRepository('AppBundle:Guide')->findBy(array('titre' => $request->get('titre')));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $guides,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );
        return $this->render('@Guides/guide/index2.html.twig', array(
            'guides' => $pagination,'guide'=>$guide
        ));
    }
}