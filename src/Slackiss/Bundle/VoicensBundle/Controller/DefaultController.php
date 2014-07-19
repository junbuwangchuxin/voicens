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
        $param = [];
        return $param;
    }
}
