<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('home.html.twig');
    }
    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findByQte(0);
        $categories = $this->getDoctrine()->getRepository(categorie::class)->findAll();
        foreach ($categories as $c){
            $produit = $this->getDoctrine()->getRepository(Produit::class)->findBy(array('categorie'=>$c));
            $c->setProduit(count($produit));
            $em = $this->getDoctrine()->getManager();
            $em->persist($c);
            $em->flush();

        }
        $hunting = $this->getDoctrine()->getRepository(categorie::class)->findOneBy(array("nom"=>"Hunting"));
        $camping = $this->getDoctrine()->getRepository(categorie::class)->findOneBy(array("nom"=>"Camping"));
        $fishing = $this->getDoctrine()->getRepository(categorie::class)->findOneBy(array("nom"=>"Fishing"));

        $pieChart = new PieChart();

        $pieChart->getData()->setArrayToDataTable(
            [['Catéggories', 'Produits'],

                ["Camping",$camping->getProduit()],
                ["Fishing",$fishing->getProduit()],
                ["Hunting",$hunting->getProduit()]

            ]
        );
        $pieChart->getOptions()->setTitle('Mes Produits');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(1376);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        // replace this example code with whatever you need
        return $this->render('adminBase.html.twig',array('produit'=>$produits,'piechart' => $pieChart));
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




    /**
     * @Route("/search", name="searchProduct")
     */
    public function searchByNameAction(Request $request){
        $c = $this->getDoctrine()->getRepository(categorie::class)->findAll();


        $produits = $this->getDoctrine()->getRepository(Produit::class)->findByNom($request->get('nom'));

        return $this->render('@Produit/Default/searchByName.html.twig', array(
            'produits' => $produits,'c'=>$c
        ));
    }
    /**
     * Lists all produit entities.
     *
     * @Route("/charts", name="charts")
     */
    public function ChartAction(){

        return $this->render('@Produit/Default/index.html.twig', array());
        //return new Response($array[0]);
    }

}
