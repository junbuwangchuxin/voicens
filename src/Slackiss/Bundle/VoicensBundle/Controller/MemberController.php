<?php

namespace Slackiss\Bundle\VoicensBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Slackiss\Bundle\VoicensBundle\Entity\Member;
use Slackiss\Bundle\VoicensBundle\Form\MemberType;

/**
 * @Route("/admin/member")
 */
class MemberController extends Controller
{
    /**
     * @Route("/",name="admin_member_list")
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $param =  array('nav_active'=>'nav_setting');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SlackissVoicensBundle:Member');
        $query = $repo->createQueryBuilder('s')
                      ->orderBy('s.id','desc')
                      ->getQuery();
        $page = $request->query->get('page',1);
        $entities = $this->get('knp_paginator')->paginate($query,$page,50);
        $param['entities'] = $entities;
        return $param;
    }

    /**
     * @Route("/new",name="admin_member_new")
     * @Method({"GET"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $param =  array('nav_active'=>'nav_setting');

        $userManager = $this->get('fos_user.user_manager');
        $member = $userManager->createUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->getNewForm($member);
        $param['entity'] = $member;
        $param['form']   = $form->createView();
        return $param;
    }

    /**
     * @Route("/create",name="admin_member_create")
     * @Method({"POST"})
     * @Template("SlackissVoicensBundle:Member:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $param =  array('nav_active'=>'nav_setting');
        $userManager = $this->get('fos_user.user_manager');
        $member = $userManager->createUser();
        $form = $this->getNewForm($member);
        $form->handleRequest($request);
        if($form->isValid()){
            if(!$member->hasRole('ROLE_USER')){
                $member->addRole('ROLE_USER');
            }

            $userManager->updateUser($member);
            $this->get('session')->getFlashBag()->add('success','创建成功');
            return $this->redirect($this->generateUrl('admin_member_list'));
        }
        $param['entity'] = $member;
        $param['form']   = $form->createView();
        return $param;
    }

    protected function getNewForm($member)
    {
        $type = new MemberType();
        $form = $this->createForm($type,$member,[
            'method'=>'POST',
            'action'=>$this->generateUrl('admin_member_create')
        ]);
        return $form;
    }

    /**
     * @Route("/delete/{id}",name="admin_member_delete")
     * @Method({"GET"})
     * @Template()
     */
    public function deleteAction(Request $request,$id)
    {
        $param =  array('nav_active'=>'nav_setting');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SlackissVoicensBundle:Member');
        $member = $repo->find($id);
        if($member){
            $userManager = $this->get('fos_user.user_manager');
            $member = $userManager->findUserByEmail($member->getEmail());
            if($member){
                $member->setEnabled(false);
                $userManager->updateUser($member);
            }
        }
        $this->get('session')->getFlashBag()->add('success','禁用成功');
        return $this->redirect($this->generateUrl('admin_member_list'));
    }

    /**
     * @Route("/enable/{id}",name="admin_member_enable")
     * @Method({"GET"})
     * @Template()
     */
    public function enableAction(Request $request,$id)
    {
        $param =  array('nav_active'=>'nav_setting');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SlackissVoicensBundle:Member');
        $member = $repo->find($id);
        if($member){
            $userManager = $this->get('fos_user.user_manager');
            $member = $userManager->findUserByEmail($member->getEmail());
            if($member){
                $member->setEnabled(true);
                $userManager->updateUser($member);
            }
        }
        $this->get('session')->getFlashBag()->add('success','启用成功');
        return $this->redirect($this->generateUrl('admin_member_list'));
    }

    /**
     * @Route("/edit/{id}",name="admin_member_edit")
     * @Method({"GET"})
     * @Template()
     */
    public function editAction(Request $request,$id)
    {
        $param =  array('nav_active'=>'nav_setting');
        $em = $this->getDoctrine()->getManager();
        $current = $this->get('security.context')->getToken()->getUser();
        $repo = $em->getRepository('SlackissVoicensBundle:Member');
        $staff = $repo->find($id);
        if($staff){
            $userManager = $this->get('fos_user.user_manager');
            $staff=$userManager->findUserByEmail($staff->getEmail());
            if($staff){
                $param['form'] = $this->getEditForm($staff)->createView();
                $param['entity'] = $staff;
                return $param;
            }
        }
        $this->get('session')->getFlashBag()->add('warning','没有找到这个会员');
        return $this->redirect($this->generateUrl('admin_member_list'));
    }

    /**
     * @Route("/update/{id}",name="admin_member_update")
     * @Method({"PUT"})
     * @Template("SlackissVoicensBundle:Member:edit.html.twig")
     */
    public function updateAction(Request $request,$id)
    {
        $param =  array('nav_active'=>'nav_setting');
        $em = $this->getDoctrine()->getManager();
        $current = $this->get('security.context')->getToken()->getUser();
        $repo = $em->getRepository('SlackissVoicensBundle:Member');
        $staff = $repo->find($id);
        if($staff){
            $userManager = $this->get('fos_user.user_manager');
            $staff=$userManager->findUserByEmail($staff->getEmail());
            if($staff){
                $form = $this->getEditForm($staff);
                $form->handleRequest($request);
                if($form->isValid()){
                    $userManager->updateUser($staff);
                    $this->get('session')->getFlashBag()->add('success','保存成功');
                    return $this->redirect($this->generateUrl('admin_member_edit',['id'=>$staff->getId()]));
                }
                $param['form'] = $form->createView();
                $param['entity'] = $staff;
                return $param;
            }
        }
        $this->get('session')->getFlashBag()->add('warning','没有找到这个会员');
        return $this->redirect($this->generateUrl('admin_member_list'));
    }

    protected function getEditForm($staff)
    {
        $type = new MemberType(true);
        $form = $this->createForm($type,$staff,[
            'method'=>'PUT',
            'action'=>$this->generateUrl('admin_member_update',['id'=>$staff->getId()])
        ]);
        return $form;
    }
}
