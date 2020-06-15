<?php

namespace EventBundle\Controller;

use AppBundle\Entity\Evenement;
use AppBundle\Entity\liste_participants;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
/**
 * apiEvent controller.
 *
 * @Route("")
 */
class apiEventController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
    /**
     * Lists api Event.
     *
     * @Route("/api/evenement", name="evenement_api")
     * @Method("GET")
     */

    public function allAction()
    {

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('transitions'));
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoder = new JsonEncoder();
        //$serializer = $this->get('serializer');
        $serializer = new Serializer(array($normalizer), array($encoder));
        $events = $this->getDoctrine()->getManager()
            ->getRepository(Evenement::class)
            ->findAll();
        $jsonflowsites =  $serializer->serialize( $events, 'json');
        return new JsonResponse(json_decode($jsonflowsites));

    }
    /**
     * Lists api Event.
     *
     * @Route("/api/user", name="user_api")
     * @Method("GET")
     */

    public function allUserAction()
    {

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('transitions'));
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoder = new JsonEncoder();
        //$serializer = $this->get('serializer');
        $serializer = new Serializer(array($normalizer), array($encoder));
        $events = $this->getDoctrine()->getManager()
            ->getRepository(User::class)
            ->findAll();
        $jsonflowsites =  $serializer->serialize( $events, 'json');
        return new JsonResponse(json_decode($jsonflowsites));

    }

    /**
     * Lists api Event.
     *
     * @Route("/api/evenement/{id}/new", name="evenement_new")
     * @Method("GET")
     * @throws \Exception
     */
    public function newEvent(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = new Evenement();
        $event->setNom($request->get('nom'));
        $event->setDescription($request->get('description'));

        $event->setDateDebut(new \DateTime('now'));
        $event->setDateFin(new \DateTime('now'));

        $binary = $request->files->get("fileUpload");
        if($binary != null){
            $file = $binary;
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('images_directory'), $fileName);
            $event->setPhoto($fileName);
        }

        $event->setUser($em->getRepository(User::class)->find($id));

        $event->setNbrePlaces($request->get('nbr_places'));

        $em->persist($event);
        $em->flush();
        //$serializer = new Serializer([new ObjectNormalizer()]);
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoder = new JsonEncoder();
        //$serializer = $this->get('serializer');
        $serializer = new Serializer(array($normalizer), array($encoder));
        $jsonflowsites =  $serializer->serialize( $event, 'json');
        return new JsonResponse(json_decode($jsonflowsites));
    }
    /**
     * Lists api Event.
     *
     * @Route("/api/evenement/inscrire/{id}/{user_id}", name="evenement_participer")
     * @Method("GET")
     * @throws \Exception
     */
    public function inscrireAction($id,$user_id){
        $em=$this->getDoctrine()->getManager();
        $event = $em->getRepository(Evenement::class)->find($id);
        $user = $em->getRepository(User::class)->find($user_id);

        $lp =new  liste_participants();

        $lp->setUser($user);
        $lp->setEvenement($event);
        $lp->setDateParticipation(new \DateTime());
        $event->setNbrePlaces($event->getNbrePlaces()-1);
        $em->persist($lp);
        $em->flush();
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('password','passwordRequestedAt','usernameCanonical','username','confirmationToken','emailCanonicals'));
        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setCircularReferenceHandler( function ($object) {
            return $object->getId();
        });
        $normalizers = [$normalizer];
        $serializer = new Serializer($normalizers, $encoders);

        // Serialize your object in Json
        $jsonObject = $serializer->serialize($event, 'json');

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }




    /**
     * Lists api Event.
     *
     * @Route("/api/evenement/show/{id}", name="evenement_request")
     * @Method("GET")
     * @throws \Exception
     */

    public function ShowMobileSingleAction($id,Request $request)
    {

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('transitions'));
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoder = new JsonEncoder();
        //$serializer = $this->get('serializer');
        $serializer = new Serializer(array($normalizer), array($encoder));
        $event = $this->getDoctrine()->getManager()
            ->getRepository(Evenement::class)
            ->find($id);
        $jsonflowsites =  $serializer->serialize( $event, 'json');
        return new JsonResponse(json_decode($jsonflowsites));


    }

    /**
     * Lists api Event.
     *
     * @Route("/api/evenement/annuler/{id}/{user_id}", name="annuler_event")
     * @Method("GET")
     * @throws \Exception
     */


    public function annulerAction($id,$user_id) {
        $em = $this->getDoctrine()->getManager();
        $eve = $em->getRepository("AppBundle:Evenement")->find($id);
        $inscriptions = $eve->getListesParticipants();
        $initialCount = sizeof($inscriptions);
        forEach($inscriptions as $inscri){
            if($user_id==$inscri->getUser()->getId()){
                $em->remove($inscri);
                $em->flush();
            }
        };
        $eve->setNbrePlaces($eve->getNbrePlaces()+1);
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('passwordRequestedAt','dateNaissaince','usernameCanonical','username','confirmationToken','emailCanonicals'));
        $normalizer->setCircularReferenceHandler( function ($object) {
            return $object->getId();
        });
        $normalizers = [$normalizer];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonObject = $serializer->serialize($eve, 'json');

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
