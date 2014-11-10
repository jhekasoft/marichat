<?php

namespace Marichat\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Marichat\ChatBundle\Form\ChatType;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ChatController extends Controller
{
    /**
     * @Route("/", name="_chat")
     * @Template()
     */
    public function indexAction()
    {
        $messages = $this->getDoctrine()
            ->getRepository('MarichatChatBundle:Message')->findAll();

        $form = $this->createForm(new ChatType());

        return array(
            'messages' => $messages,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/add", name="_chat_add")
     * @Template()
     */
    public function addAction()
    {
        $data = array('test' => 'test_1');
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $data = array('test' => 'test_noajax1');
        }

        return new JsonResponse($data);
    }
}
