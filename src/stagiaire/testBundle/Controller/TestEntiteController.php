<?php

namespace stagiaire\testBundle\Controller;

use stagiaire\testBundle\Entity\TestEntite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Testentite controller.
 *
 * @Route("testentite")
 */
class TestEntiteController extends Controller
{
    /**
     * Lists all testEntite entities.
     *
     * @Route("/", name="testentite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $testEntites = $em->getRepository('stagiairetestBundle:TestEntite')->findAll();

        return $this->render('testentite/index.html.twig', array(
            'testEntites' => $testEntites,
        ));
    }

    /**
     * Creates a new testEntite entity.
     *
     * @Route("/new", name="testentite_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $testEntite = new Testentite();
        $form = $this->createForm('stagiaire\testBundle\Form\TestEntiteType', $testEntite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($testEntite);
            $em->flush();

            return $this->redirectToRoute('testentite_show', array('id' => $testEntite->getId()));
        }

        return $this->render('testentite/new.html.twig', array(
            'testEntite' => $testEntite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a testEntite entity.
     *
     * @Route("/{id}", name="testentite_show")
     * @Method("GET")
     */
    public function showAction(TestEntite $testEntite)
    {
        $deleteForm = $this->createDeleteForm($testEntite);

        return $this->render('testentite/show.html.twig', array(
            'testEntite' => $testEntite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing testEntite entity.
     *
     * @Route("/{id}/edit", name="testentite_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TestEntite $testEntite)
    {
        $deleteForm = $this->createDeleteForm($testEntite);
        $editForm = $this->createForm('stagiaire\testBundle\Form\TestEntiteType', $testEntite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('testentite_edit', array('id' => $testEntite->getId()));
        }

        return $this->render('testentite/edit.html.twig', array(
            'testEntite' => $testEntite,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a testEntite entity.
     *
     * @Route("/{id}", name="testentite_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TestEntite $testEntite)
    {
        $form = $this->createDeleteForm($testEntite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($testEntite);
            $em->flush();
        }

        return $this->redirectToRoute('testentite_index');
    }

    /**
     * Creates a form to delete a testEntite entity.
     *
     * @param TestEntite $testEntite The testEntite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TestEntite $testEntite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('testentite_delete', array('id' => $testEntite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
