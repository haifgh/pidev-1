<?php


namespace ProduitBundle\Controller;
use AppBundle\AppBundle;
use AppBundle\Entity\categorie;
use AppBundle\Entity\Produit;
use AppBundle\Entity\Jaime;
use AppBundle\Entity\Post;
use AppBundle\Entity\reclamation;
use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use PromoBundle\Form\produitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class ApiProduitController extends Controller
{
    /**
     * @Route("/api/produit", name="getProd")
     */
    public function getProdAction() {
        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQuery('select p from AppBundle:Produit p where p.qte > 0 ');
        $res= $qb->getResult();
        //dump($res);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/cat", name="getCat")
     */
    public function getCatAction(){
        $em=$this->getDoctrine()->getManager();
        $res=$em->getRepository('AppBundle:categorie')->findAll();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/newprod", name="add_prod")
     *@param Request $request
     * @return JsonResponse
     *@throws \Exception
     */
    public function addProduitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = new produit();
        $id=$request->get('categorie');
        $nom=$request->get('nom');
        $qte=$request->get('qte');
        $prix=$request->get('prix');
        $prix_promo=$request->get('prix_promo');
        $description=$request->get('description');
        $photo=$request->get('photo');
        $categorie_id = $this->getDoctrine()->getManager()->getRepository('AppBundle:categorie')->find($id);
        $produit ->setNom($nom);
        $produit ->setQte($qte);
        $produit ->setPrix($prix);
        $produit ->setPrixPromo($prix_promo);
        $produit ->setDescription($description);
        $produit ->setPhoto($photo);
        $produit ->setCategorie($categorie_id);
        $em->persist($produit);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($produit);
        return new Response("product added successfully");
    }
    /**
     * @Route("/api/newcat/{nom}/{produit}", name="add_cat")
     *
     * @return JsonResponse
     *
     */
    public function addCategorieAction($nom , $produit)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie = new categorie();
        $categorie->setNom($nom);
        $categorie->setProduit($produit);
        $em->persist($categorie);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted= $serializer->normalize($categorie);
        return new Response("category added successfully");
    }

    /**
     * @Route("/api/editprod/{id}", name="edit_prod")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     *
     */
    public function editProdAction(Request $request, Produit $Produit)
    {

        $em = $this->getDoctrine()->getManager();
        $id=$request->get('categorie');
        $categorie_id = $this->getDoctrine()->getManager()->getRepository('AppBundle:categorie')->find($id);
        $Produit->setNom($request->get('nom'));
        $Produit->setQte($request->get('qte'));
        $Produit->setPrix($request->get('prix'));
        $Produit->setPrixPromo($request->get('prix_promo'));
        $Produit->setDescription($request->get('description'));
        $Produit->setPhoto($request->get('photo'));
        $Produit ->setCategorie($categorie_id);
        $em->persist($Produit);
        $em->flush();
        return new Response("Product edited successfully");
    }
    /**
     * @Route("/api/editcat/{id}", name="edit_cat")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     *
     */
    public function editCatAction(Request $request, categorie $categorie)
    {

        $em = $this->getDoctrine()->getManager();
        $categorie->setNom($request->get('nom'));
        $categorie->setProduit($request->get('produit'));
        $em->persist($categorie);
        $em->flush();
        return new Response("category edited successfully");
    }

    /**
     * @Route("/api/deletecat/{id}", name="delete_cat")
     *
     * @return JsonResponse
     */
    public function deletecatAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository(categorie::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        return new Response("deleted category");
    }
    /**
     * @Route("/api/deletep/{id}", name="delete_prod")
     *
     * @return JsonResponse
     */
    public function deletepAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $Produit = $em->getRepository(Produit::class)->find($id);
        $em->remove($Produit);
        $em->flush();
        return new Response("deleted product");
    }
    /**
     * @Route("/api/find/{nom}", name="find_prod")
     * @param $nom
     * @param Request $request
     * @return Response
     * @throws AnnotationException
     */
    public function findProdAction($nom,Request $Request)
    {
        //$em=$this->getDoctrine()->getManager();
        //$res=$em->getRepository('AppBundle:Produit')->findByNom($produit->getNom());
        $catp=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.qte>0 AND p.nom=:p')->setParameter('p',$nom);
        $res=$catp ->getResult();
      //  dump($res);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/findcat/{nom}", name="getProdCat")
     * @param $nom
     * @param Request $request
     * @return Response
     * @throws AnnotationException
     */
    public function getCatProd($nom,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $res=$em->getRepository('AppBundle:categorie')->findByNom($nom);
        $catp=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.qte>0 AND p.categorie=:p')->setParameter('p', $res);
        $rest=$catp ->getResult();
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($rest, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/findc/{nom}", name="getProdCat")
     * @param $nom
     * @param Request $request
     * @return Response
     * @throws AnnotationException
     */
    public function getCat($nom,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $res=$em->getRepository('AppBundle:categorie')->findByNom($nom);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }

    /**
     * @Route("/api/findPOut", name="getPOut")
     * @return Response
     * @throws AnnotationException
     */
    public function getPOut()
    {

        $catp=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.qte=0');
        $res= $catp -> getResult();
      //  dump($res);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }
    /**
     * @Route("/api/details/{id}", name="prod_det")
     * @param $id
     * @param Request $request
     * @return Response
     * @throws AnnotationException
     */
    public function getProdDetails($id,Request $Request)
    {
        $catp=$this->getDoctrine()->getManager()->createQuery('select p from AppBundle:Produit p where p.qte>0 AND p.id=:p')->setParameter('p',$id);
        $res=$catp ->getResult();
        //dump($res);
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new DateTimeNormalizer(),new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($res, 'json',['groups' =>['produit','produitc']]);
        return new Response($jsonContent);
    }









}