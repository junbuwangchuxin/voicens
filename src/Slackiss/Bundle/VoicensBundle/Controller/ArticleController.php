<?php

namespace Slackiss\Bundle\VoicensBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Slackiss\Bundle\VoicensBundle\Entity\Article;
use Slackiss\Bundle\VoicensBundle\Form\ArticleType;

/**
 * Article controller.
 *
 * @Route("/admin/article")
 */
class ArticleController extends Controller
{

    /**
     * Lists all Article entities.
     *
     * @Route("/", name="admin_article")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $repo = $em->getRepository('SlackissVoicensBundle:Article');
        $query = $repo->createQueryBuilder('a')
                      ->orderBy('a.modified','desc')
                      ->where('a.status = true' )
                      ->getQuery();
        $entities = $this->get('knp_paginator')->paginate($query,$page,50);
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Article entities.
     *
     * @Route("/publish/{id}", name="admin_article_publish")
     * @Method("GET")
     */
    public function publishArticleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这篇文章.');
        }
        if ($entity->getState()==Article::STATE_DISABLED)
            {
                throw $this->createNotFoundException('禁用的文章不能发表');
            }
        $entity->setModified( new \DateTime());
        $entity->setState(Article::STATE_PUBLISHED);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_article'));

    }
    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="admin_article_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这篇文章.');
        }

        if ($entity->getStatus())
        {

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
        }
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="admin_article_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这篇文章.');
        }
        if ($entity->getStatus())
        {

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
        }
    }

    /**
    * Creates a form to edit a Article entity.
    *
    * @param Article $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(true), $entity, array(
            'action' => $this->generateUrl('admin_article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => '保存'));

        return $form;
    }
    /**
     * Edits an existing Article entity.
     *
     * @Route("/{id}", name="admin_article_update")
     * @Method("PUT")
     * @Template("SlackissVoicensBundle:Article:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这篇文章.');
        }
        if ($entity->getStatus())
        {

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setModified( new \DateTime());
            $em->flush();

            return $this->redirect($this->generateUrl('admin_article_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
        }
    }
    /**
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="admin_article_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

            if (!$entity||!$entity->getStatus()) {
                throw $this->createNotFoundException('没找到这篇文章.');
            }
            $entity->setModified( new \DateTime());
            $entity->setState(Article::STATE_DISABLED);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_article'));
    }

    /**
     * Creates a form to delete a Article entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => '禁用'))
            ->getForm()
        ;
    }
}
