<?php

namespace AppBundle\Controller;

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
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/admin/", name="admin")
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
        return $this->render('adminBase.html.twig',array('produits'=>$produits,'piechart' => $pieChart));
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
