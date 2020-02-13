<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('adminBase.html.twig');
    }
    /**
     * @Route("/admin/users", name="users")
     */
    public function showusersAction(Request $request)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('users.html.twig',['users'=>$users]);
    }
    /**
     * @Route("/admin/user/{id}/edit", name="user_edit")
     */
    public function editusersAction(Request $request,User $user)
    {
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('edituser.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),

        ));}
        /**
         * @Route("/admin/user/{id}/disable", name="user_disable")
         */
        public function disableAction($id){
            $user=$this->getDoctrine()->getRepository(User::class)->find($id);
            $user->setEnabled(false);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('users');
        }
    /**
     * @Route("/admin/user/{id}/enable", name="user_enable")
     */
    public function enableAction($id){
        $user=$this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setEnabled(true);

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('users');
    }
    /**
     * @Route("/admin/user/{id}/notadmin", name="user_not_set_admin")
     */
    public function notadminAction($id){
        $user=$this->getDoctrine()->getRepository(User::class)->find($id);
        $userManager = $this->get('fos_user.user_manager');

        $user->removeRole('ROLE_ADMIN');
        $userManager->updateUser($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('users');
    }
    /**
     * @Route("/admin/user/{id}/setadmin", name="user_set_admin")
     */
    public function setadminAction($id){
        $user=$this->getDoctrine()->getRepository(User::class)->find($id);
        $userManager = $this->get('fos_user.user_manager');
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('users');
    }





}
