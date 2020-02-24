<?php

namespace ProduitBundle\Controller;

use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use AppBundle\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Produit controller.
 *
 * @Route("produit")
 */
class ProduitController extends Controller
{
    /**
     * Lists all produit entities.
     *
     * @Route("/", name="produit_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AppBundle:Produit')->findAll();
		$paginator=$this->get('knp_paginator');
		$pagination=$paginator->paginate(
		$produits,
		$request->query->getInt('page',1),
		$request->query->getInt('limit',1)
		);
        return $this->render('@Produit/produit/index.html.twig', array(
            'produits' => $pagination,
            'produit'=>$produits
        ));
    }




    /**
     * Creates a new produit entity.
     *
     * @Route("/new", name="produit_new")
     */
    public function newAction(Request $request)
    {
        $produit = new Produit();
        $form = $this->createForm('ProduitBundle\Form\ProduitType', $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $produit->UploadProfilePicture();
            $produit->setPhoto("images/".$produit->getPhoto());
            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
        }

        return $this->render('@Produit/produit/new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produit entity.
     *
     * @Route("/{id}", name="produit_show")
     */
    public function showAction(Produit $produit)
    {

        return $this->render('@Produit/produit/show.html.twig', array(
            'produit' => $produit,
        ));
    }

    /**
     * Displays a form to edit an existing produit entity.
     *
     * @Route("/{id}/edit", name="produit_edit")
     */
    public function editAction(Request $request, Produit $produit)
    {
        $editForm = $this->createForm('ProduitBundle\Form\ProduitType', $produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_edit', array('id' => $produit->getId()));
        }

        return $this->render('@Produit/produit/edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a produit entity.
     *
     * @Route("/delete/{id}", name="produit_delete")
     */
    public function deleteAction($id)
    {
        $em = $this ->getDoctrine() ->getManager();
        $produit = $em -> getRepository(Produit::class)->find($id);
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('produit_index');
    }


}
