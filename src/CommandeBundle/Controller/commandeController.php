<?php

namespace CommandeBundle\Controller;

use AppBundle\Entity\commande;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Commande controller.
 *
 */
class commandeController extends Controller
{
    /**
     * Lists all commande entities.
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginator=$this->get(PaginatorInterface::class);
        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        $pagination = $paginator->paginate($qb
            , /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );


        return $this->render('@Commande/commande/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }



    /**
     * Finds and displays a commande entity.
     *
     */
    public function showAction(commande $commande)
    {


        return $this->render('@Commande/commande/show.html.twig', array(
            'commande' => $commande

        ));
    }
    public function ordersAction(Request $request)
    {

        $user=$this->getUser();
        $commandes=$user->getCommandes();

        $paginator=$this->get(PaginatorInterface::class);
        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQuery('select u from AppBundle:commande u where u.user=:x and u.chargeId is not null ');
            $qb->setParameter('x',$user);
        $pagination = $paginator->paginate($qb
            , /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );

        return $this->render('@Commande/commande/orders.html.twig', array(
            'pagination' => $pagination

        ));
    }

    /**
     * Displays a form to edit an existing commande entity.
     *
     */
    public function editAction($id,$type)
    {
        $commande=$this->getDoctrine()->getRepository(commande::class)->find($id);
        if(in_array($type,['Pending','Cancelled','Delivered']))
            $commande->setStatus($type);
        if($type=='Delivered')
        {
            $date=new \DateTime();
            $commande->setDateLivraison($date);
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('commande_index');

    }



}
