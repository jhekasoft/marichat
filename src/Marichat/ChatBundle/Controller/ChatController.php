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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Marichat\ChatBundle\Entity\Message;

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
            'webSocketsUlr' => 'ws://' . $this->getRequest()->getHost() . ':' . $this->container->getParameter('marichat_websocket_port'),
        );
    }

    /**
     * @Route("/add", name="_chat_add")
     * @Template()
     */
    public function addAction(Request $request)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('Not ajax request');
        }

        $form = $this->createForm(new ChatType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = new Message();
            $message->setText($form->get('message')->getData());
            $message->setTime(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $messages = $this->getDoctrine()
                ->getRepository('MarichatChatBundle:Message')->findAll();

            $form = $this->createForm(new ChatType());
            $formHtml = $this->renderView('MarichatChatBundle::Chat/addform.html.twig', array('form' => $form->createView()));
            $messagesHtml = $this->renderView('MarichatChatBundle::Chat/messages.html.twig', array('messages' => $messages));

            return new JsonResponse(array(
                'result' => 'ok',
                'listHtml' => $messagesHtml,
                'addFormHtml' => $formHtml,
            ));
        }

        $formHtml = $this->renderView('MarichatChatBundle::Chat/addform.html.twig', array('form' => $form->createView()));
        return new JsonResponse(array(
            'result' => 'fail',
            'addFormHtml' => $formHtml,
        ));
    }

    /**
     * @Route("/messages", name="_chat_messages")
     * @Template()
     */
    public function messagesAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('Not ajax request');
        }

        $messages = $this->getDoctrine()
            ->getRepository('MarichatChatBundle:Message')->findAll();
        $messagesHtml = $this->renderView('MarichatChatBundle::Chat/messages.html.twig', array('messages' => $messages));

        return new JsonResponse(array(
            'result' => 'ok',
            'listHtml' => $messagesHtml,
        ));
    }
}
