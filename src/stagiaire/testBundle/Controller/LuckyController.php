<?php  
namespace stagiaire\testBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use stagiaire\testBundle\Entity\TestEntite;
use stagiaire\testBundle\Entity\Image;
use stagiaire\testBundle\Entity\Application;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LuckyController extends Controller{
    
    public function numberAction($max){
        if (!is_numeric($max)){
            throw $this->createNotFoundException('il faut un nombre!!!');
        }
        else{
            $number = mt_rand(0,$max);
            $advNumber = mt_rand(0,$max);
            if ($number < $advNumber){
                $result = "Vous gagnez le combat!";
            }
            else{
                $result = "Il arrive à se défendre!";
            }
            return $this->render('@stagiairetest/lucky/number.html.twig', array('max' => $max, 'number' => $number, 'advNumber' => $advNumber, 'result' => $result));
        }
    }


    public function indexAction()
    {
        $test = "Ceci est un test de routage, si tu es ici c'est que ça marche bien ^^'";
        return $this->render('@stagiairetest/lucky/base.html.twig', array('test' => $test,));   
    }

    public function serviceAction(){
        $logger = $this->container->get('logger');
        $logger->info('Look! I just used a service');
        return new Response ("Merci d'utiliser le service *Mettre un nom random*");
    }

    public function addAction(Request $request){
        // Création de l'entité
        $testEntite = new TestEntite();
        $testEntite ->setTitle('Recherche de joueur JDR.');
        $testEntite ->setAuthor('MJ');
        $testEntite ->setContent("Je recherche des joueurs pour mour... faire une partie de JDR!");
        // On peut ne pas définir ni la date ni la publication,
        // car ces attributs sont définis automatiquement dans le constructeur

        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('http://mediathequeludotheque.bonneuil94.fr/pbonneuil/images/stories/coups-coeur/ados/donjon-900.jpg');
        $image->setAlt('Donjon de Naheulbeuk');

        // On lie l'image à l'annonce
        $testEntite->setImage($image);

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");

        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");

        // On lie les candidatures à l'annonce
        $application1->setAdvert($testEntite);
        $application2->setAdvert($testEntite);
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité
        $em->persist($testEntite);

        // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
        // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
        $em->persist($application1);
        $em->persist($application2);

        // Étape 2 : On « flush » tout ce qui a été persisté avant
        $em->flush();

        // Reste de la méthode qu'on avait déjà écrit
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('/', array('test' => 'Erreur' ));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('@stagiairetest/lucky/annonce.html.twig', array('testEntite' => $testEntite));
    }

    public function viewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
         // On récupère le repository
          $repository = $em->getRepository('stagiairetestBundle:TestEntite');
  
         // On récupère l'entité correspondante à l'id $id
         $testEntite = $repository->find($id);

         $a = new TestEntite();
         $form = $this->createFormBuilder($a) 
         ->add('date', DateType::class)
         ->add('title', TextType::class)
         ->add('author', TextType::class)
         ->add('save', SubmitType::class, array('label' => 'Save'))
         ->getForm();

         $form->handleRequest($request);
         
             if ($form->isSubmitted() && $form->isValid()) {
                 // $form->getData() holds the submitted values
                 // but, the original `$task` variable has also been updated
                 $task = $form->getData();
         
               $em->persist($task);
               $em->flush();
         
                 //return $this->redirectToRoute('task_success');
             }

  
         // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
         // ou null si l'id $id  n'existe pas, d'où ce if :
         if (null === $testEntite) {
        throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
         }
  
         // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository('stagiairetestBundle:Application')
            ->findBy(array('advert' => $testEntite))
        ;

         // Le render ne change pas, on passait avant un tableau, maintenant un objet
         return $this->render('@stagiairetest/lucky/annonce.html.twig', array(
            'testEntite' => $testEntite,
            'listApplications' => $listApplications,
            'form' => $form->createView()
        ));
    }
}