<?php

namespace CommandeBundle\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @Route("/api/orders", name="get_orders")
     */
    public function getAction()
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
      // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:commande')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['commande','user']]);
        return new Response($jsonContent);
    }

}
