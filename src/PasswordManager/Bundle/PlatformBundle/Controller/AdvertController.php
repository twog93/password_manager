<?php

namespace PasswordManager\Bundle\PlatformBundle\Controller;


use PasswordManager\Bundle\PlatformBundle\Entity\ApplicationRepository;
use PasswordManager\Bundle\PlatformBundle\Entity\AdvertRepository;
use PasswordManager\Bundle\PlatformBundle\Entity\AdvertSkill;
use PasswordManager\Bundle\PlatformBundle\Entity\Skill;
use PasswordManager\Bundle\PlatformBundle\Entity\Application;
use PasswordManager\Bundle\PlatformBundle\Entity\Image;
use PasswordManager\Bundle\PlatformBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PasswordManager\Bundle\PlatformBundle\Form\AdvertEditType;
use PasswordManager\Bundle\PlatformBundle\Form\AdvertType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;




class AdvertController extends Controller

{

  public function listApplicationAction(){

     $em = $this->getDoctrine()->getManager();
     $listAdverts = $em->getRepository('PasswordManagerPlatformBundle:Advert')->getAdvertWithCategories(array('Développement web', 'Intégration'));

     return $this->render('PasswordManagerPlatformBundle:Advert:index.html.twig', array('listAdverts' => $listAdverts));
  }

  public function listAnnonceAction(){

     $em = $this->getDoctrine()->getManager();
     $listApplications = $em->getRepository('PasswordManagerPlatformBundle:Application')->getApplicationsWithAdvert(2);

     return $this->render('PasswordManagerPlatformBundle:Advert:application.html.twig', array('listApplications' => $listApplications ));
  }

  public function viewAction($id){

    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('PasswordManagerPlatformBundle:Advert')->find($id);
    //$categories = $em->getRepository('PasswordManagerPlatformBundle:AdvertSkill')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $listApplication = $em->getRepository('PasswordManagerPlatformBundle:Application')->findBy(array('advert' => $advert));
    $listAdvertSkills = $em->getRepository('PasswordManagerPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
    $listCategories = $em->getRepository('PasswordManagerPlatformBundle:Advert')->find($id);
	
    return $this->render('PasswordManagerPlatformBundle:Advert:view.html.twig', array(
		'advert' => $advert,
        'listApplication' => $listApplication,
        'listAdvertSkills' => $listAdvertSkills,
        'listCategories' => $listCategories,
		));
  }

  public function viewSlugAction($year, $slug, $format){

    return new Response("Affichage de l'annonce d'id : ".$year . $slug . $format);
  }
  
  public function indexAction($page){

    if ($page < 1) {
      throw new NotFoundHttpException("La page ".$page." n'existe pas.");
    }
   $nbPerPage = 3;
      $userId = $this->getUser()->getId();
   $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);
   //$listAdverts->getAdverts($page, $nbPerPage);

   $nbPages = ceil(count($listAdverts) / $nbPerPage);
   if ($page > $nbPages) {

      throw $this->createNotFoundException("La page ".$page." n'existe pas.");

    }
	 //$em = $this->getDoctrine()->getManager();  
	 //$listAdverts = $em->getRepository('PasswordManagerPlatformBundle:Advert')->findAll();

	
  
    return $this->render('PasswordManagerPlatformBundle:Advert:index.html.twig', array(

      'listAdverts' => $listAdverts,

      'nbPages'     => $nbPages,

      'page'        => $page,

    ));
  }
  
  public function addAction(Request $request){
      $userId = $this->getUser()->getId();
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);

      if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
          // Sinon redirection login page
          return $this->redirectToRoute('fos_user_security_login');
      }

      $user = $this->getUser();
      //$username=$this->getUser()->getUsername();
        $advert = new Advert();

    // On ajoute le formulaire créer avec doctrine et la class AdvertType
      $form = $this->get('form.factory')->create(AdvertType::class, $advert);

     if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {


              // On enregistre notre objet $advert dans la base de données, par exemple
              // c'est elle qui déplace l'image là où on veut les stocker
            //  $advert->getImage()->upload();

              $em = $this->getDoctrine()->getManager();
              $advert->setUser($user);
              $em->persist($advert);
              $em->flush();

              $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
              return $this->redirectToRoute('password_manager_platform_view', array('id' => $advert->getId()));

    }


    // On passe la méthode createView() du formulaire à la vue
   
    return $this->render('PasswordManagerPlatformBundle:Advert:add.html.twig', array(

      'form' => $form->createView(),));
     
	}

  public function editAction($id, Request $request){



     $em = $this->getDoctrine()->getManager();
     $advert = $em->getRepository('PasswordManagerPlatformBundle:Advert')->find($id);
    
      if(null === $advert){

        throw new NotFoundHttpException("Le mot de passe d'id ".$id." n'existe pas.");
      }
      $userId = $this->getUser()->getId();
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);
      $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);


      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

          $em->persist($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Mot de passe bien modifié.');


          return  $this->redirectToRoute('password_manager_platform_view', array('id' => $advert->getId()));

      }

      return $this->render('PasswordManagerPlatformBundle:Advert:edit.html.twig', array(
          'form' => $form->createView(),
          'listAdverts' => $listAdverts
      ));

  }

  public function deleteAction($id, Request $request){

      $em = $this->getDoctrine()->getManager();
      $advert = $em->getRepository('PasswordManagerPlatformBundle:Advert')->find($id);
      $userId = $this->getUser()->getId();
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);

      if (null === $advert) {

        throw new NotFoundHttpException("Le mot de passe d'id ".$id." n'existe pas.");
      }

      $form = $this->get('form.factory')->create();


      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->remove($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', "Le mot de passe à été supprimé.");

          return $this->redirectToRoute('password_manager_core_home');

      }
      return $this->render('PasswordManagerPlatformBundle:Advert:delete.html.twig',
          array(
              'advert' => $advert,
              'form'   => $form->createView(),
              'listAdverts' => $listAdverts));
  } 
  
  public function listAction(){

     $em = $this->getDoctrine()->getManager();
     $listAdverts =  $em->getRepository('PasswordManagerPlatformBundle:Advert')->getAdvertWithCategories(array('Développeur', 'Intégrateur'));
  
     return $this->render('PasswordManagerPlatformBundle:Advert:list.html.twig', array(
     'listAdverts' => $listAdverts
     ));
  }

  public function menuAction($page){
	  
	    $em = $this->getDoctrine()->getManager();
      $listAdverts = $em->getRepository('PasswordManagerPlatformBundle:Advert')->getAdverts();
	   
      return $this->render('PasswordManagerPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts ));
  }
  

}