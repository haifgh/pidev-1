<?php

namespace PublicationBundle\Controller;

use AppBundle\Entity\Follow;
use AppBundle\Entity\Jaime;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post controller.
 *
 * @Route("post")
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('AppBundle:Post')->findByUser($this->getUser());

        return $this->render('@Publication/post/prof.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setDateCreation(new \DateTime('now'));
        $post->setUser($this->getUser());
        $form = $this->createForm('PublicationBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index', array('id' => $post->getId()));
        }

        return $this->render('@Publication/post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
    /**
     * Creates a new post entity.
     *
     * @Route("/search", name="search_user")
     * @Method({"GET", "POST"})
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->request->get('search');
        $users =  $em->getRepository('AppBundle:User')->findByNom($requestString);

        return $this->render('search.html.twig');
    }


    /**
     * Finds and displays a post entity.
     *
     * @Route("/home", name="home")
     * @Method("GET")
     */
    public function homeAction()
    {
        $followed=$this->getDoctrine()->getRepository(Follow::class)->findBy(['follower'=>$this->getUser()]);
        $q=$this->getDoctrine()->getManager()->createQuery("select u from AppBundle:Follow u where u.follower=:id")->setParameter('id',$this->getUser()->getId());
        return $this->render('home.html.twig',['followed'=>$followed]);
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('PublicationBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index', array('id' => $post->getId()));
        }

        return $this->render('@Publication/post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/delete/{id}", name="post_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this ->getDoctrine() ->getManager();
        $post = $em -> getRepository(Post::class)->find($id);
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
