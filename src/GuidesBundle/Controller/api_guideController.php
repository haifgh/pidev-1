<?php


namespace GuidesBundle\Controller;
use AppBundle\Entity\Commentaire;
use AppBundle\Entity\Guide;
use AppBundle\Entity\Likes;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class api_guideController extends Controller
{

    /**
     * @Route("/api/guides", name="get_guides")
     */
    public function allAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $guides = $em->getRepository('AppBundle:Guide')
            ->findAll();
        //  $guide = $em->getRepository('AppBundle:Guide')->findAllGuide();
        // $serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted= $serializer->normalize($guides);
        // return new JsonResponse($formatted);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($guides, 'json', ['groups' => ['guide', 'user']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/details/{id}", name="detail_guide")
     */
    public function detailsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guide = $em->getRepository('AppBundle:Guide')->find($request->get('id'));
        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(array('guide' => $guide));
        $isGuideLiked = $em->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $this->getUser()));
        $nbLikes = $em->getRepository(Likes::class)->countByGuide($guide);

        /*normalizer = new ObjectNormalizer();
         $normalizer->setCircularReferenceLimit(0);
         $normalizer->setCircularReferenceHandler(function ($object) {
              $object->get('id');
         });
         $normalizers = array($normalizer);
         $serializer = new Serializer($normalizers);
         $formatted = $serializer->normalize(array('titre' => $guide->getTitre(),
             'datecreation' => $guide->getDateCreation(),
             'description' => $guide->getDescription(),
             'commentaire' => $commentaire,
             'id' => $guide->getId(),
             'lien' => $guide->getLien(),
             'photos' => $guide->getPhoto(),
            'isGuideLiked' => $isGuideLiked,
             'nbLikes'=> $nbLikes
         ));
         return new JsonResponse($formatted);*/
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        //$jsonContent = $serializer->serialize([$guide,$commentaire,$nbLikes,$isGuideLiked], 'json',['groups' =>['guide','user','commentaire','likes']]);
        $jsonContent = $serializer->serialize(array('titre' => $guide->getTitre(),
            'datecreation' => $guide->getDateCreation(),
            'description' => $guide->getDescription(),
            'commentaire' => $commentaire,
            'id' => $guide->getId(),
            'lien' => $guide->getLien(),
            'photos' => $guide->getPhoto(),
            'isGuideLiked' => $isGuideLiked,
            'nbLikes' => $nbLikes), 'json', ['groups' => ['guide', 'user', 'commentaire', 'likes']]);
        return new Response($jsonContent);

    }

    /**
     * @Route("/api/rechercher", name="recher_guide")
     */

    public function rechercherAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
      //  $guide = $em->getRepository('AppBundle:Guide')->findAllGuide();

        $guides = $em->getRepository('AppBundle:Guide')->findBy(array('titre' => $request->get('titre')));
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize( $guides, 'json', ['groups' => ['guide', 'user']]);
        return new Response($jsonContent);
        //return $this->render('@Guides/guide/index2.html.twig', array(
        //  'guides'=>$guides ,'guide'=>$guide
        //));
    }

    /**
     * @Route("/api/PDF/{id}", name="gener_pdf")
     */

    public function returnPDFAction($id)
    {
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($id);

        $name = md5(uniqid());

        $html=$this->renderView(
            '@Guides/guide/PDF.html.twig', array('guide' => $guide));

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html)
                 .$name.'.pdf');


       }
       /* dump($this);
        return new Response(
            $this->getOutputFromHtml($this),200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$name.'.pdf"'
            )
        );*/

    /*  $serializer = new Serializer([new ObjectNormalizer()]),
      "C:/Users/haifa/Desktop".$name . ".pdf");

  $formatted= $serializer->normalize($name);

   return new JsonResponse($formatted);*/

    /**
     * @Route("/api/guide/rate/{id}/{g}/{note}", name="add_rate")
     */
    public function addRateAction($id, $g, $note)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($g);
        $rate = $this->getDoctrine()->getRepository(Rate::class)->findOneBy(array('user' => $user, 'guide' => $guide));
        $rates = $this->getDoctrine()->getRepository(Rate::class)->findBy(array('guide' => $guide));
        if (!$rate) {
            $avis = 0;
            $moyenne = 0;
            if ($rates) {
                foreach ($rates->getNote() as $n) {
                    $avis = $avis + $n;

                }
                $moyenne = (($avis + $note) / (count($rates) + 1));
            } else {

                $moyenne = (($note) / (count($rates) + 1));
            }


            $rate = new Rate();
            $rate->setGuide($guide);
            $rate->setUser($user);
            $rate->setNote($note);
            $guide->setNote($moyenne);
            $em = $this->getDoctrine()->getManager();
            $em->persist($guide);
            $em->persist($rate);
            $em->flush();
            /*$serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($rate);
            return new JsonResponse($formatted);*/
            $encoders = [new JsonEncoder()];
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($rate, 'json', ['groups' => ['guide', 'user', 'rate',]]);
            // return new Response($jsonContent);
            return new Response("done");
            // return new Response("Done");
        } else {
            return new Response("error");


        }
    }

    /**
     * @Route("/api/Aimer/{id}/{u}", name="add_like")
     */
    public function LikeAction($id,$u)
    {

            //$user = $this->getUser();
        $user = $this->getDoctrine()->getRepository(User::class)->find($u);
            $em = $this->getDoctrine()->getManager();
             $guide = $em->getRepository("AppBundle:Guide")->find($id);
            $like = $this->getDoctrine()->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $user));
            if (!$like) {
                $like = new Likes();
                $like->setUser($user);
                $like->setGuide($guide);
                $em->persist($like);
                $em->flush();
                return new Response('addedd');

    }}

   /* public function LikeAction($id){

        $em=$this->getDoctrine()->getManager();
        $guide = $em->getRepository("AppBundle:Guide")->find($id);
        $jaime=$em->getRepository(Likes::class)->findOneBy(['user'=>$this->getUser(),'guide'=>$guide]);

        if($jaime!=null){
            $em ->remove($jaime);
            $em->flush();
            return new Response('removed');
        }
        else
        {
            $like = new Likes();
            $like->setUser($this->getUser());
            $like->setGuide($guide);
            $em=$this->getDoctrine()->getManager();
            $em ->persist($like);
            $em->flush();
            return new Response('added');

        }}*/

         /**
         * @Route("/api/PasAimer/{id}/{u}", name="del_like")
        */
    public function DislikeAction($id,$u)
    {

            //$user = $this->getUser();
        $user = $this->getDoctrine()->getRepository(User::class)->find($u);
            $em = $this->getDoctrine()->getManager();
        $guide = $em->getRepository("AppBundle:Guide")->find($id);
            $like = $em->getRepository(Likes::class)->findOneBy(array('guide' => $guide, 'user' => $user));
            //$em->persist($guide);
            if($like)
                $em->remove($like);
            $em->flush();
        return new Response('removed');
        }
    /**
     * @Route("/api/AfficherComm/{g}", name="AfficherComm")
     */
   /* public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
      //  $guide = $em->getRepository('AppBundle:Guide')->find($id);
        $commentaires = $em->getRepository('AppBundle:Commentaire')->findBy(array('guide' => $request->get('id')));
       // $commentaires = $em->getRepository('AppBundle:Commentaire')->findBy(array('guide'=>$g));
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($commentaires, 'json', ['groups' => ['commentaire', 'user','guide']]);
        return new Response($jsonContent);
    }*/
    public function indexAction($g)
    {
        $em = $this->getDoctrine()->getManager();
        //  $guide = $em->getRepository('AppBundle:Guide')->find($id);
      //$commentaires = $em->getRepository('AppBundle:Commentaire')->getPostComments($g);
      $commentaires = $em->getRepository('AppBundle:Commentaire')->findBy(array('guide'=>$g));
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
       $formatted=$serializer->serialize( $commentaires,'json' ,['groups' => ['commentaire', 'user','guide']]);
        return new Response($formatted);
    }
//$data = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['familyName', 'company' => ['name']]]);
   /* public function afficherCommentaireAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQuery('select c, u.id
       FROM AppBundle:Commentaire c 
       JOIN c.user u 
       WHERE c.guide = :id   ');
        $guide=$em->getRepository(User::class)->find($id);
        $user=$em->getRepository(Guide::class)->find($id);
        $qb->setParameter('id',$guide);
        $res= $qb->getResult();
        //$res=$em->getRepository('AppBundle:commande')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['commentaire','guide','user']]);
        return new Response($jsonContent);


    }*/
    /**
     * @Route("/api/AddComm/{id}/{g}/{c}", name="AddComm")
     */
    public function newComment($id,$g,$c)
    {

        $commentaire = new Commentaire();
        //$guide = $this->getDoctrine()->getRepository(Guide::class)->find($request->get('id'));
       // $user=$this->getDoctrine()->getRepository(User::class)->find($request->get('idu'));
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($g);
        $commentaire->setDate(new \DateTime('now'));
        $commentaire->setGuide($guide);
        $commentaire->setUser($user);
        //$commentaire->setUser($this->getUser());
        //$commentaire->setContenu($request->get('comment'));
        $commentaire->setContenu($c);
        $em = $this->getDoctrine()->getManager();
        $em->persist($commentaire);
        $em->flush();
        return new Response("success");
    }
    /**
     * @Route("/api/DellCom/{id}/{idGuide}/{u}", name="DellCom")
     */
    public function deleteComm($id, $idGuide,$u)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaire::class)->find($id);

        $em->remove($commentaire);
        $em->flush();

        return new Response("success");
        /*$encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize([$commentaire, $idGuide], 'json', ['groups' => ['guide', 'user','commentaire']]);
        return new Response($jsonContent);*/

    }

    /**
     * @Route("/api/EditCom/{id}", name="EditCom")
     */
    public function updateComm(Request $request ,$id)
    {
       // $deleteForm = $this->createDeleteForm($commentaire);
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaire::class)->find($id);
        $guide = $this->getDoctrine()->getRepository(Guide::class)->find($request->get('id'));
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            $object->get('id');
        });
        $commentaire->setContenu($request->get('comment'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($commentaire);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);


             $formatted= $serializer->normalize($commentaire,$guide);

            return new JsonResponse($formatted);


        }
    /**
     * @Route("/api/nbrelike/{id}", name="nbrelike")
     */
    public function nbrelikeAction(Request $request ,$id)
    {

        $em = $this->getDoctrine()->getManager();
        $guide = $em->getRepository('AppBundle:Guide')->find($id);
        $nbLikes = $em->getRepository(Likes::class)->countByGuide($guide);

        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize([$guide,$nbLikes], 'json', ['groups' => ['guide', 'user']]);
        return new Response($jsonContent);
    }

}