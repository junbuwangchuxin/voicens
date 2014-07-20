<?php

namespace Slackiss\Bundle\VoicensBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    /**
     * @Route("/",name="welcome")
     * @Template()
     */
    public function indexAction()
    {
        $current = $this->get('security.context')->getToken()->getUser();
        if($current->hasRole('ROLE_ADMIN')){
            return $this->redirect($this->generateUrl('admin_article'));
        }
       else if($current->hasRole('ROLE_AUTHOR')){
           return $this->redirect($this->generateUrl('author_article_list'));
       } if($current->hasRole('ROLE_USER')){
        return $this->redirect($this->generateUrl('user_article_list'));
    }
        $param = [];
        return $param;
    }
}
