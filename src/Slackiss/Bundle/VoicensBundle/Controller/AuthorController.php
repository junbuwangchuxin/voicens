<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-7-19
 * Time: 下午4:38
 */

namespace Slackiss\Bundle\VoicensBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Slackiss\Bundle\VoicensBundle\Entity\Article;
use Slackiss\Bundle\VoicensBundle\Form\ArticleType;


/**
 * Article controller.
 *
 * @Route("/author/article")
 */
class AuthorController  extends Controller{

    /**
     * Lists all Article entities.
     *
     * @Route("/", name="author_article_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $current = $this->get('security.context')->getToken()->getUser();
        $repo = $em->getRepository('SlackissVoicensBundle:Article');
        $query = $repo->createQueryBuilder('a')
            ->orderBy('a.id','desc')
            ->where('a.status=true')
                 ->andwhere('a.member = :member')
            //   ->andWhere('a.state <> :state')
                ->setParameters(array('member'=>$current->getId()))
            ->getQuery();

        $entities = $this->get('knp_paginator')->paginate($query,$page,50);
        return array(
            'entities' => $entities,
        );
    }


    /**
     * Lists all Article entities.
     *
     * @Route("/check/{id}", name="author_article_check")
     * @Method("GET")
     */
    public function checkArticleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);
        $current = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if($current->getId()!=$entity->getMember()->getId()){
            return $this->redirect($this->generateUrl('author_article_list'));
        }
        if($entity->getState()==Article::STATE_DISABLED)
        { return $this->redirect($this->generateUrl('author_article_list'));}

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这个文章.');
        }
        if ($entity->getState()==Article::STATE_DRAFT)
            {
        $entity->setModified( new \DateTime);
        $entity->setState(Article::STATE_CHECKED);
        $em->flush();
        return $this->redirect($this->generateUrl('author_article_list'));
            }

    }



    /**
     * Displays a form to create a new Article entity.
     *
     * @Route("/new", name="author_article_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Article();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Creates a new Article entity.
     *
     * @Route("/", name="author_article_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Article();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $current = $this->get('security.context')->getToken()->getUser();
            $entity->setMember($current);

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','创建成功');
            return $this->redirect($this->generateUrl('author_article_list'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    private function createCreateForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('author_article_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => '保存'));

        return $form;
    }
    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="author_article_show")
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
        $current = $this->get('security.context')->getToken()->getUser();


        if($current->getId()!==$entity->getMember()->getId()){
          return  $this->redirect($this->generateUrl('author_article_list'));
        }
        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="author_article_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $current = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if($current->getId()!=$entity->getMember()->getId()){
            return $this->redirect($this->generateUrl('author_article_list'));
        }
           if($entity->getState()==Article::STATE_DISABLED)
               { return $this->redirect($this->generateUrl('author_article_list'));}

           if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这个文章.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Edits an existing Article entity.
     *
     * @Route("/{id}", name="author_article_update")
     * @Method("PUT")
     * @Template("SlackissVoicensBundle:Author:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity||!$entity->getStatus()) {
            throw $this->createNotFoundException('没找到这个文章.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $current = $this->get('security.context')->getToken()->getUser();

            if($current->getId()===$entity->getMember()->getId()){
                $entity->setModified( new \DateTime());
                $entity->setState(Article::STATE_DRAFT);
                $em->flush();
                return $this->redirect($this->generateUrl('author_article_show', array('id' => $id)));

        }
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
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
            'action' => $this->generateUrl('author_article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => '保存'));

        return $form;
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="author_article_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $current = $this->get('security.context')->getToken()->getUser();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

            if (!$entity||!$entity->getStatus()) {
                throw $this->createNotFoundException('没找到这个文章.');
            }

         //   $em->remove($entity);
            if($current->getId()===$entity->getMember()->getId()){
                $entity->setState(Article::STATE_DISABLED);
                $em->flush();
            }

        }

        return $this->redirect($this->generateUrl('author_article_list'));
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
            ->setAction($this->generateUrl('author_article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => '禁用'))
            ->getForm()
            ;
    }
}