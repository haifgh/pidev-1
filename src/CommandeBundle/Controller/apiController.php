<?php

namespace CommandeBundle\Controller;


use AppBundle\Entity\commande;
use AppBundle\Entity\ligne_commande;
use AppBundle\Entity\ligne_promotion;
use AppBundle\Entity\Produit;
use AppBundle\Entity\User;
use CommandeBundle\Entity\orders;
use Doctrine\Common\Annotations\AnnotationReader;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class apiController extends Controller
{



    /**
     * @Route("/api/{id}/orders/", name="get_orders")
     */
    public function getAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null and u.user=:x order by u.date desc ');
        $user=$em->getRepository(User::class)->find($id);
        $qb->setParameter('x',$user);
        $res= $qb->getResult();
       //$res=$em->getRepository('AppBundle:commande')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['commande','Produit','commande_lc']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/{id}/order/", name="create_order")
     */
    public function commandeAction(Request $request,$id)
    {





        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $var=$serializer->deserialize($request->getContent() ,orders::class,"json");

        dump($var->getAddress());
        dump($var->getTel());
          $panier=[];
            foreach ($var->getData() as $i){
                $panier[]=[
                    'product'=>$i['produit'],
                    'quantity'=>$i['quantite']
                ];
            }

      $c=$this->createOrder($panier,$id);
            $c->setTel($var->getTel());
            $c->setAdresse($var->getAddress());
        $em = $this->getDoctrine()->getManager();
        $em->persist($c);
        $em->flush();
           return new JsonResponse();
    }
    private function createOrder($panier,$id){
        $em = $this->getDoctrine()->getManager();
        $commande = new commande();
        $date= new \DateTime();
        $commande->setAdresse("mobile");
        $commande->setTel("mobile");
        $commande->setChargeId("mobile");
        $commande->setDate($date);
        $commande->setStatus('Pending');
        $commande->setUser($em->getRepository(User::class)->find($id));
        $em->persist($commande);
        $em->flush();

        foreach ($panier as $entry){
            $p=$entry['product'];
            $qte=$entry['quantity'];
            $lq= new ligne_commande();
            $produit=$em->getRepository(Produit::class)->find($p);
            //$lp=$this->getDoctrine()->getRepository(ligne_promotion::class)->findByProduit($p);
            $lq->setProduit($produit);
            $lq->setQuantite($qte);
            $test=false;
//            foreach ($lp as $test){
//                if($test->getPromotion()->getValid()==true)
//                    $test=true;
//            }
//            if($test==true){
//                $lq->setPrix($produit->getPrixPromo()*$qte);
//            }else{
                $lq->setPrix($produit->getPrix()*$qte);
//            }

            $commande->setPrixTotal($commande->getPrixTotal()+$lq->getPrix());
            $lq->setCommande($commande);
            $em->persist($lq);
            $em->flush();
        }
        $em->persist($commande);
        $em->flush();

        return $commande;
    }



}
