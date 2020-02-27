<?php

namespace ProduitBundle\Controller;

use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorie controller.
 *
 * @Route("categorie")
 */
class categorieController extends Controller
{
    /**
     * Lists all categorie entities.
     *
     * @Route("/admin", name="categorie_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:categorie')->findAll();
        $paginator=$this->get('knp_paginator');
        $pagination=$paginator->paginate(
            $categories,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',2)
        );
        return $this->render('@Produit/categorie/index.html.twig', array(
            'categories' => $pagination,
        ));
    }



    /**
     * Creates a new categorie entity.
     *
     * @Route("/admin/new", name="categorie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $categorie = new Categorie();
        $form = $this->createForm('ProduitBundle\Form\categorieType', $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('categorie_show', array('id' => $categorie->getId()));
        }

        return $this->render('@Produit/categorie/new.html.twig', array(
            'categorie' => $categorie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a categorie entity.
     *
     * @Route("/admin/show/{id}", name="categorie_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $categorie=$this->getDoctrine()->getRepository(categorie::class)->find($id);
        return $this->render('@Produit/categorie/show.html.twig', array(
            'categorie' => $categorie
        ));
    }

    /**
     * Displays a form to edit an existing categorie entity.
     *
     * @Route("/admin/{id}/edit", name="categorie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, categorie $categorie)
    {

        $editForm = $this->createForm('ProduitBundle\Form\categorieType', $categorie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_edit', array('id' => $categorie->getId()));
        }

        return $this->render('@Produit/categorie/edit.html.twig', array(
            'categorie' => $categorie,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a categorie entity.
     *
     * @Route("/admin/delete/{id}", name="categorie_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this ->getDoctrine() ->getManager();
        $categorie = $em -> getRepository(categorie::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('categorie_index');
    }







}
