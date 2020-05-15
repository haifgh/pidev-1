<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Follow;
use AppBundle\Entity\Jaime;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
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
        return $this->render('mainhome.html.twig');
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
        $pieChart->getOptions()->setTitle('Products');
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

        }}
    /**
     * @Route("/admin/users", name="users")
     */
    public function showusersAction(Request $request)
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('users.html.twig',['users'=>$users]);
    }
    /**
     * @Route("/user/edit", name="user_edit")
     */
    public function editusersAction(Request $request)
    {   $user=$this->getUser();
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $user->UploaderProfilePicture();
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('post_index');
        }

        return $this->render('edituser.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),

        ));
    }

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

        $nom=$request->get('nom');
        $produits = $this->getDoctrine()->getManager()
            ->createQuery('select p from AppBundle:Produit p where p.nom like :par and p.qte>0')->setParameter('par','%'.$nom.'%');

        return $this->render('@Produit/Default/searchByName.html.twig', array(
            'produits' => $produits->getResult(),'c'=>$c
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
    /**
     * @Route("/api/login/{uname}/{pass}", name="api_login")
     */
    public function loginapiAction($uname,$pass)
    {

        $_username = $uname;
        $_password = $pass;
        $factory = $this->get('security.encoder_factory');
        $user_manager = $this->get('fos_user.user_manager');
        $user = $user_manager->findUserByUsername($_username);


        if(!$user){
            return new Response(
                'Username doesnt exists',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }

        /// Start verification
        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        if(!$encoder->isPasswordValid($user->getPassword(), $_password, $salt)) {
            return new Response(
                'Username or Password not valid.',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(['commandes','evenements','reclamations','posts','participations']);
        $serializer = new Serializer([$normalizer], $encoders);
        $jsonContent = $serializer->serialize($user, 'json');
        return new Response($jsonContent);
    }

}
