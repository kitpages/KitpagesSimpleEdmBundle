<?php

namespace Kitpages\SimpleEdmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentController extends Controller
{

    public function documentAddAction()
    {
        return $this->render('KitpagesSimpleEdmBundle:Document:documentAdd.html.twig', array(
        ));
    }

    public function documentListAction()
    {
        return $this->render('KitpagesSimpleEdmBundle:Document:documentList.html.twig', array(
        ));
    }

}
