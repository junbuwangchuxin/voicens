<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-7-20
 * Time: 下午4:32
 */

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
 * @Route("/user/article")
 */
class UserController extends Controller{

    /**
     * Lists all Article entities.
     *
     * @Route("/", name="user_article_list")
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
            ->orderBy('a.modified','desc')
            ->where('a.state = :state')
            ->setParameters(array('state'=>Article::STATE_PUBLISHED))
            ->getQuery();

        $entities = $this->get('knp_paginator')->paginate($query,$page,50);
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="user_article_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SlackissVoicensBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('没找到这个文章');
        }


        if($entity->getState()!== Article::STATE_PUBLISHED){
            return  $this->redirect($this->generateUrl('user_article_list'));
        }
        return array(
            'entity'      => $entity
        );
    }


}