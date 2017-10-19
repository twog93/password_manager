<?php

namespace PasswordManager\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PasswordManager\Bundle\CoreBundle\Entity\Contact;
use PasswordManager\Bundle\CoreBundle\Form\ContactType;

// Class HomeController


class HomeController extends Controller
{
    public function indexAction()
    {
        // Vérification si anonyme ou authentifier
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            //Display home Page
            $userId = $this->getUser()->getId();
            $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);

            return $this->render('PasswordManagerCoreBundle:Home:index.html.twig', array(

                'listAdverts' => $listAdverts,));

        }
		// renvois Login Page

        return $this->redirectToRoute('fos_user_security_login');

    }
	
	 public function contactAction(Request $request)
    {

        // Vérification si anonyme ou authentifier
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            // Sinon redirection login page
            return $this->redirectToRoute('fos_user_security_login');
        }

        $user = $this->getUser();
        $userId = $this->getUser()->getId();
        $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);

        $contact = new Contact();

        // Ajout du formulaire de contact
        $form = $this->get('form.factory')->create(ContactType::class, $contact);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $useremail = $this->getUser()->getEmail();
            $mailer = $this->container->get('mailer');
            $userId = $this->getUser()->getId();
            $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);


            //Email to user
            $message = (new \Swift_Message('Confirmation de réception de votre message'))
                ->setFrom('contact-gmp@afbiodiversite.fr')
                ->setTo($useremail)
                ->setBody(
                    $this->renderView(
                        'PasswordManagerCoreBundle:Home:mailing-contact.html.twig',
                        array('subject' => $contact->getSubject(),
                            'author' => $user->getUsername(),
                            'body' => $contact->getBody())
                    ),
                    'text/html'
                );

            $messageToAdmin = (new \Swift_Message("Demande d'information"))
                ->setFrom($useremail)
                ->setTo('contact-gmp@afbiodiversite.Fr')
                ->setBody(
                    $this->renderView(
                        'PasswordManagerCoreBundle:Home:mailing-contact-admin.html.twig',
                        array('subject' => $contact->getSubject(),
                            'author' => $user->getUsername(),
                            'body' => $contact->getBody(),
                            'email' => $user->getEmail())
                    ),
                    'text/html'
                );

            $mailer->send($message);
            $mailer->send($messageToAdmin);


            // create message in BDD
            $em = $this->getDoctrine()->getManager();
            $contact->setUser($user);
            $em->persist($contact);
            $em->flush();


            $request->getSession()->getFlashBag()->add('notice', "Votre message a été envoyé à l'équipe support");
            return $this->redirectToRoute('password_manager_core_home');


        }

            // On passe la méthode createView() du formulaire à la vue
            return $this->render('PasswordManagerCoreBundle:Home:contact.html.twig', array(
                'form' => $form->createView(),
                'listAdverts' => $listAdverts,));
    }

    protected function checkGetPass(){

        $userId = $this->getUser()->getId();
        $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);
        return $listAdverts;

    }

    public function generatorAction()
    {
        // Vérification si anonyme ou authentifier
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            // Sinon redirection login page
            return $this->redirectToRoute('fos_user_security_login');
        }
        $user = $this->getUser();
        $userId = $user->getId();
        $listAdverts = $this->getDoctrine()->getManager()->getRepository('PasswordManagerPlatformBundle:Advert')->myFindUserId($userId);


        return $this->render('PasswordManagerCoreBundle:Home:generate_password.html.twig', array(

            'listAdverts' => $listAdverts,)

        );



    }
}
