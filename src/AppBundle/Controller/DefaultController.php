<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Follow;
use AppBundle\Entity\Jaime;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
    public function adminAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('adminBase.html.twig');
    }
    /**
     * @Route("/rechercheUser/{param}", name="recherche_utilisteur")
     */
    public function rechercheUserAction(Request $request, $param){


            $query=$this->getDoctrine()->getManager()->createQuery("select u.username,u.id from AppBundle:User u where u.username like :key")->setParameter('key','%'.$param.'%');
            $serializer= new Serializer(array(new ObjectNormalizer()));
            $data=$serializer->normalize($query->getResult());

            return new JsonResponse($data);

        }



    /**
     * @Route("/follow/{id}", name="follow")
     */
    public function followAction($id){
        $followed = $this->getDoctrine()->getRepository(User::class)->find($id);
        $follower=$this->getDoctrine()->getRepository(User::class)->find($id);
        $follow = $this->getDoctrine()->getRepository(Follow::class)->findOneBy(array("follower"=>$this->getUser(),"followed"=>$followed));
        if(!$follow){
            $suivre = new Follow();
            $followed->setFollower($followed->getFollower()+1);
            $suivre->setFollowed($followed);
            $suivre->setFollower($this->getUser());
            $em=$this->getDoctrine()->getManager();
            $em->persist($suivre);
            $em->persist($followed);
            $em->flush();
            return new Response("Done");
        }else{
            $em=$this->getDoctrine()->getManager();
            $followed->setFollower($followed->getFollower()-1);
            $em->persist($followed);
            $em->remove($follow);
            $em->flush();
            return new Response("error");
        }
    }
    /**
     * @Route("/detailUser/{id}", name="detail_user")
     */
    public function DetailsUserAction($id){

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if($user == $this->getUser())
            return $this->redirectToRoute('post_index');
        $follow = $this->getDoctrine()->getRepository(Follow::class)->findOneBy(array("follower"=>$this->getUser(),"followed"=>$user));
        $post = $this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$user));
        return $this->render('@Publication/Default/detailsUser.html.twig', array(
           'user'=>$user,
            'posts'=>$post,
            'follow'=>$follow
        ));
    }
    /**
     * @Route("/jaime/{id}", name="jaime")
     */
    public function jaimeAction($id){

        $em=$this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        $jaime=$em->getRepository(Jaime::class)->findOneBy(['user'=>$this->getUser(),'post'=>$post]);

            if($jaime!=null){
                $em ->remove($jaime);
                $em->flush();
                return new Response('removed');
            }
        else
        {
            $like=new Jaime();
            $like->setUser($this->getUser());
            $like->setPost($post);
            $em=$this->getDoctrine()->getManager();
            $em ->persist($like);
            $em->flush();
            return new Response('added');

        }

    }

}
