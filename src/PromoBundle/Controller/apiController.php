<?php


namespace PromoBundle\Controller;
use AppBundle\AppBundle;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\Produit;
use AppBundle\Entity\ligne_promotion;
use AppBundle\Entity\User;
use AppBundle\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;






/**
 * @Route("/api", name="api_")
 */
class apiController extends Controller
{
    /**
     * @Route("/all/promo/products", name="ge")
     */
    public function getProductsAction()

    {
        $em=$this->getDoctrine()->getManager();
        $q=$em->createQuery('SELECT p from AppBundle:Produit p where exists (select l from AppBundle:ligne_promotion l where p.id=l.produit)');
        $res=$q->getResult();
        $encoder=[new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoder);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['Produit','user']]);
        return new Response($jsonContent);

    }

    /**
     * @Route("/promotion/all", name="get_promos")
     */
    public Function allOffersAction(){

        $em=$this->getDoctrine()->getManager();
        $res=$em->getRepository('AppBundle:Promotion')->findAll();
        $encoder=[new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoder);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['Promotion','user']]);
        return new Response($jsonContent);

    }
    /**
     * @Route("/newuser", name="add_user")
     * @param Request $request
     * @return JsonResponse
     */
    public function newAction(Request $request){

        $em=$this->getDoctrine()->getManager();
        $user= new User();
        $user->setUsername($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('pass'));
        $user->setLastLogin($request->get('date'));
        $em->persist($user);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($user);
        return new Response('ok');


    }
    /**
     * @Route("/newpromo", name="add")
     * @param Request $request
     * @return JsonResponse
     */
    public function newPromoAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $promo= new Promotion();
        $promo->setNom($request->get('nom'));
        $promo->setTauxReduction($request->get('rate'));
        $em->persist($promo);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($promo);
        return new Response('ok');


    }
    /**
     * @Route("/newligne/{idpdt}/{idpromo}", name="add_ligne")
     * @param Request $request
     * @return JsonResponse
     */
    public function newLigneAction(Request $request,$idpdt,$idpromo){

        $em=$this->getDoctrine()->getManager();

        $promo = $em->getRepository('AppBundle:Promotion')->find($idpromo);
        $produit = $em->getRepository('AppBundle:Produit')->find($idpdt);

        $query=$em->createQuery('SELECT l from AppBundle:ligne_promotion l where  l.promotion=?1 and l.produit=?2 ');
        $query->setParameter(1, $idpromo);
        $query->setParameter(2, $idpdt);
        $res=$query->getResult();
        if($res==null){
            $lg= new ligne_promotion();
            $lg->setProduit($produit);
            $lg->setPromotion($promo);
            $lg->setQuantite(0);
            $em->persist($lg);
            $em->flush();
            $produit->setPrixPromo($produit->getPrix()-$produit->getPrix()*$promo->getTauxReduction()/100);
            $produit->setQte($produit->getQte()-1);
            $em->persist($produit);
            $em->flush();

        }else{
            $produit->setPrixPromo($produit->getPrix()-$produit->getPrix()*$promo->getTauxReduction()/100);
            $produit->setQte($produit->getQte()-1);
            $em->persist($produit);
            $em->flush();

        }

        $query=$em->createQuery('SELECT l from AppBundle:ligne_promotion l where  l.promotion=?1 and l.produit=?2 ');
        $query->setParameter(1, $idpromo);
        $query->setParameter(2, $idpdt);
        $res=$query->getResult();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $encoders=[new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);


        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['ligne_promotion','user']]);//
        return new Response($jsonContent);//

       // return new Response('ok');


    }

    /**
     * @Route("/promo/products/{id}", name="a")
     */
    public function findAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $query=$em->createQuery('SELECT l from AppBundle:ligne_promotion l where  l.promotion=?1 ');
        $query->setParameter(1, $id);
        $res=$query->getResult();
        $encoder=[new JsonEncoder()];
       $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
       $serializer = new Serializer($normalizers, $encoder);
       $jsonContent = $serializer->serialize($res, 'json',['groups' =>['Produit','ligne_promotion','user']]);
       return new Response($jsonContent);
    }
    /**
     * @Route("/promo/supprimer/{id}", name="supprime")
     */
    public function removeAction(Promotion $article,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Promotion')->find($id);

        if (!$entity) {
           // throw $this->createNotFoundException('Unable to find Promotion entity.');
            return new Response('Unable to find entity', 404);
        }

        $em->remove($entity);
        $em->flush();


        return new Response('ok');
    }

    /**
     * @Route("/promo/update/{id}", name="u")
     *
     */
        public function updatePromoAction(Request $request, $id)
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Promotion')->find($id);
    if($request->isMethod('GET'))
    {
    $entity->setNom($request->get('nom'));
    $entity->setTauxReduction($request->get('rate'));
      //  $date= new \DateTime();
      //  $date=$request->get('date');
        //$entity->setDateDebut($date);

    $em->flush();
    $normalizer = new ObjectNormalizer();
    $normalizer->setCircularReferenceLimit(2);
    // Add Circular reference handler
    $normalizer->setCircularReferenceHandler(function ($object) {
        return $object->getId();
    });
    $normalizers = array($normalizer);
    $encoders=[new JsonEncoder()];
    $serializer = new Serializer($normalizers, $encoders);
    $formate=$serializer->normalize($entity);
    return new Response('ok');


}
            return new Response('Failed', 404);
        }
    /**
     * @Route("/quantitysupdate/{id}", name="update_promo")
     *
     */
    public function updateQuantityAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:ligne_promotion')->find($id);
        $entity2 = $em->getRepository('AppBundle:Produit')->find($entity->getProduit());



        if($request->isMethod('GET'))
        {
            $entity->setQuantite($request->get('qtepromo'));
            $entity2->setQte($request->get('qtepdt'));
            $em->flush();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(2);
            // Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $normalizers = array($normalizer);
            $encoders=[new JsonEncoder()];
            $serializer = new Serializer($normalizers, $encoders);
            $formate=$serializer->normalize($entity);
            return new Response('ok');


        }
        return new Response('Failed', 404);
    }
    /**
     * @Route("/all", name="get_promos-products")
     */
        public function getAll(){
            $em=$this->getDoctrine()->getManager();
           $q=$em->createQuery('SELECT l from AppBundle:ligne_promotion l ');
           // $q=$em->getConnection()->query('select * from produit,ligne_promotion');
            $res=$q->getResult();
            $encoder=[new JsonEncoder()];
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
            $serializer = new Serializer($normalizers, $encoder);
            $jsonContent = $serializer->serialize($res, 'json',['groups' =>['Produit','Promotion','ligne_promotion','user']]);
            return new Response($jsonContent);
        }
    /**
     * @Route("/!promopdt", name="get_p")
     */
    public function getAllPdt(){
        $em=$this->getDoctrine()->getManager();
        $q=$em->createQuery('SELECT p from AppBundle:Produit p  WHERE NOT EXISTS (select l from AppBundle:ligne_promotion l where p.id=l.produit)');
        // $q=$em->getConnection()->query('select * from produit,ligne_promotion');
        $res=$q->getResult();
        $encoder=[new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoder);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['Produit','Promotion','ligne_promotion','user']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/getphonenumber", name="get_products")
     */
    public function  getphoneAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $q=$em->createQuery('SELECT u from AppBundle:User u  ');
        $res=$q->getResult();
        $encoder=[new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoder);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['User','user']]);
        return new Response($jsonContent);

    }

    }