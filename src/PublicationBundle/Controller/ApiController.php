<?php


namespace PublicationBundle\Controller;
use AppBundle\AppBundle;
use AppBundle\Entity\Follow;
use AppBundle\Entity\Jaime;
use AppBundle\Entity\Post;
use AppBundle\Entity\reclamation;
use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @Route("/api/postall", name="get")
     */
    public function getAction()
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:Post')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['post','user','jaimes']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/claims/{id}", name="get_claims")
     */
    public function getReclamationAction(User $user)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:reclamation')->findByUser($user->getId());
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['reclamation','user']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/jaime", name="get_jaime")
     */
    public function getLikesnAction()
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:Jaime')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['jaime','user']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/follow", name="get_reclamation")
     */
    public function getFollowAction()
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:Follow')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['follow','user']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/newpost", name="get_post")
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function addPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $id=$request->get('aut');
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
        $post->setContenu($request->get('Contenu'));
        $post->setDateCreation(new \DateTime());
        $post->setUser($user);
        $em->persist($post);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($post);
        return new Response("ok");
    }
    /**
     * @Route("/api/edit/{id}", name="edit_post")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     *
     */
    public function editAction(Request $request, Post $post)
    {

        $em = $this->getDoctrine()->getManager();
        $post->setContenu($request->get('Contenu'));
        $em->persist($post);
        $em->flush();
        return new Response("edit");
    }

    /**
     * @Route("/api/delete/{id}", name="delete_post")
     * @param Post $post
     * @return JsonResponse
     */
    public function deleteAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return new Response("delete");
    }
    /**
     * @Route("/api/newclaim", name="get_claim")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function addClaimsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $claim = new reclamation();
        $id=$request->get('us');
        $tar=$request->get('target');
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
        $target = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($tar);
        $claim ->setContenu($request->get('cont'));
        $claim ->setObjet($request->get('obj'));
        $claim ->setDate(new \DateTime('now'));
        $claim ->setUser($user);
        $claim->setReclamer($target);
        $em->persist($claim);
        $em->flush();
        return new Response("ok c");
    }

    /**
     * @Route("/api/editclaim/{id}", name="edit_claim")
     * @param Request $request
     * @param reclamation $claim
     * @return JsonResponse
     */
    public function editClaimAction(Request $request, Reclamation $claim)
    {
        $em = $this->getDoctrine()->getManager();
        $claim ->setContenu($request->get('cont'));
        $claim ->setObjet($request->get('obj'));
        $em->persist($claim);
        $em->flush();
        return new Response("edit c");
    }

    /**
     * @Route("/api/deleteclaim/{id}", name="delete_claim")
     * @param reclamation $claim
     * @return JsonResponse
     */
    public function deleteclaimAction(Reclamation $claim)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($claim);
        $em->flush();
        return new Response("delete c");
    }

    /**
     * @Route("/api/newjaime", name="new_like")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function addLikeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $like = new Jaime();
        $id=$request->get('us');
        $post=$request->get('post');
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find($id);
        $pub = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->find($post);
        $like ->setUser($user);
        $like ->setPost($pub);
        $em->persist($like);
        $em->flush();
        return new Response("ok like");
    }

    /**
     * @Route("/api/deletejaime/", name="delete_like")
     * @param Jaime $like
     * @return JsonResponse
     */
    public function deleteLikeAction(Jaime $like)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($like);
        $em->flush();
        return new Response("delete like");
    }

    /**
     * @Route("/api/newfollow", name="new_follow")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function addFollowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $follow = new Follow();
        $flr=$request->get('flw');
        $fld=$request->get('fld');
        $follower = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find( $flr);
        $followed = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find( $fld);

        $follow ->setFollower($follower);
        $follow->setFollowed($followed);
        $em->persist($follow);
        $em->flush();
        return new Response("ok FOLLOW");
    }

    /**
     * @Route("/api/deletefollow/{id}", name="delete_follow")
     * @param Follow $follow
     * @return JsonResponse
     */
    public function deleteFollowAction(Follow $follow)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($follow);
        $em->flush();
        return new Response("delete follow");
    }

    /**
     * @Route("/api/find/{id}", name="getUser")
     */
    public function getUserAction(User $user)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:User')->findById($user->getId());
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['user']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/jaimeu/{user}/{post}", name="get_nbjaime")
     * @param User $user
     * @param Post $post
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getnbLikesnAction(User $user,Post $post)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:Jaime')->findBy(['user'=>$user->getId(),'post'=>$post->getId()]);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['jaime','user']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/deletejaime/{id}", name="delete_j")
     *
     * @return JsonResponse
     */
    public function deletejaimeAction(Jaime $jaime)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($jaime);
        $em->flush();
        return new Response("delete jaime");
    }
    /**
     * @Route("/api/finduser/{nom}", name="getUsern")
     */
    public function getUserAllAction($nom)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:User')->findBy(['nom'=>$nom]);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['user']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/post/{id}", name="getpost")
     */
    public function getpostAction(User $user)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $res=$em->getRepository('AppBundle:Post')->findByUser($user->getId());
        //  $post=$em->getRepository('AppBundle:Post')->findBy(['user'=>$res->getId()]);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['post','user','jaimes']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/followu/{id}/{idd}", name="get_nbfolows")
     *
     * @param Follow $follow
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getnbfoolownAction($id,$idd)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $follower=$this->getDoctrine()->getRepository(User::class)->find($id);
        $followed=$this->getDoctrine()->getRepository(User::class)->find($idd);
        $res=$em->getRepository('AppBundle:Follow')->findBy(['follower'=>$follower,'followed'=>$followed]);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['user','follow']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/followl/{id}", name="get_nbfolo")
     *i
     * @param Follow $follow
     * @return Response
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function getnbfollownAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        //$qb=$em->createQuery('select u from AppBundle:commande u where u.chargeId is not null order by u.date desc ');
        // $res= $qb->getResult();
        $follower=$this->getDoctrine()->getRepository(User::class)->find($id);
        $res = $this->getDoctrine()->getRepository(Follow::class)->findBy(['follower'=>$follower]);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['user','follow']]);
        return new Response($jsonContent);

    }
    /**
     * @Route("/api/editUser/{id}", name="edit_user")
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function editUserAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user ->setPhoto($request->get('photo'));
        $user ->setNom($request->get('username'));
        $user ->setUsername($request->get('username'));
        $user ->setEmail($request->get('mail'));
        $user ->setTel($request->get('tlf'));
        $user ->setAdresse($request->get('add'));
        $em->persist($user);
        $em->flush();
        return new Response("edit c");
    }



}