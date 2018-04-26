<?php  
namespace stagiaire\testBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use stagiaire\testBundle\Entity\TestEntite;
use stagiaire\testBundle\Entity\Image;
use stagiaire\testBundle\Entity\Application;
use stagiaire\testBundle\Form\TestEntiteType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
		$form = $this->createForm(TestEntiteType::class, $testEntite);
		if ($request->isMethod('POST')) {
      		// On fait le lien Requête <-> Formulaire
      		// À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
      		$form->handleRequest($request);
			
			// On vérifie que les valeurs entrées sont correctes
			// (Nous verrons la validation des objets en détail dans le prochain chapitre)
			  if ($form->isValid()) {
				// On enregistre notre objet $advert dans la base de données, par exemple
				$em = $this->getDoctrine()->getManager();
				$em->persist($testEntite);
				$em->flush();
				  
				$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
				// On redirige vers la page de visualisation de l'annonce nouvellement créée
				return $this->redirectToRoute('viewTest', array('id' => $testEntite->getId()));
			}
		}

        // Si on n'est pas en POST, alors on affiche le formulaire
    	return $this->render('@stagiairetest/lucky/form.html.twig', array('form' => $form->createView()));
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
	
	public function testUserAction(Request $request){
		return $this->render('@stagiairetest/lucky/testUser.html.twig');
	}
	public function testAdminAction(Request $request){
		return $this->render('@stagiairetest/lucky/testAdmin.html.twig');
	}
}