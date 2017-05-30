<?php
/**
 * Created by PhpStorm.
 * User: PerigeeSoftouaire
 * Date: 22/05/2017
 * Time: 11:55
 */

namespace Fot\Bundle\ElvisConnectorBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ActionsController extends  Controller
{
    public function indexAction()
    {
        return new Response("hello");
    }


}