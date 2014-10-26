<?php

namespace Chat\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Chat\Model\Chat;
use Chat\Form\ChatForm;

class ChatController extends AbstractActionController
{
    protected $chatTable;

    protected function renderMessages()
    {
        $viewModel = new ViewModel(array(
            'messages' => $this->getChatTable()->fetchAll(),
        ));
        $viewModel->setTemplate('chat/chat/messages');
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        return $viewRender->render($viewModel);
    }

    protected function renderEditForm($form)
    {
        $viewModel = new ViewModel(array(
            'editForm' => $form,
        ));
        $viewModel->setTemplate('chat/chat/edit-form');
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        return $viewRender->render($viewModel);
    }

    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $model = new Chat();
        $addForm = new ChatForm();
        $addForm->setInputFilter($model->getInputFilter());

        $uri = $this->getRequest()->getUri();

        return new ViewModel(array(
            'messages' => $this->getChatTable()->fetchAll(),
            'addForm' => $addForm,
            'webSocketsUlr' => 'ws://' . $uri->getHost() . ':' . $config['web_sockets']['port'],
        ));
    }

    public function addAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        $form = new ChatForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = new Chat();
            $form->setInputFilter($model->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $model->exchangeArray($form->getData());
                $nowDateTime = new \DateTime();
                $model->time = $nowDateTime->format(\DateTime::ISO8601);
                $this->getChatTable()->saveItem($model);

                $form->reset();
                return new JsonModel(array(
                    'result' => 'ok',
                    'listHtml' => $this->renderMessages(),
                    'addFormHtml' => $this->renderEditForm($form),
                ));
            }

            return new JsonModel(array(
                'result' => 'fail',
                'addFormHtml' => $this->renderEditForm($form),
            ));
        }

        return new JsonModel(array(
            'result' => 'fail'
        ));
    }

    public function getEditFormAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = (int) $request->getPost('id', 0);

            if ($id) {
                try {
                    $item = $this->getChatTable()->getItem($id);
                }
                catch (\Exception $ex) {
                    return new JsonModel(array(
                        'result' => 'fail',
                        'errorDetails' => $ex->getMessage(),
                    ));
                }

                $form = new ChatForm();
                $form->bind($item);

                return new JsonModel(array(
                    'result' => 'ok',
                    'editFormHtml' => $this->renderEditForm($form),
                ));
            }
        }

        return new JsonModel(array(
            'result' => 'fail'
        ));
    }

    public function editAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        $form = new ChatForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = (int) $request->getPost('id', 0);
            try {
                $item = $this->getChatTable()->getItem($id);
            }
            catch (\Exception $ex) {
                return new JsonModel(array(
                    'result' => 'fail',
                    'errorDetails' => $ex->getMessage(),
                ));
            }

            $form->setInputFilter($item->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $item->exchangeArray($form->getData());
                $nowDateTime = new \DateTime();
                $item->time = $nowDateTime->format(\DateTime::ISO8601);
                $this->getChatTable()->saveItem($item);

                $form->reset();
                return new JsonModel(array(
                    'result' => 'ok',
                    'listHtml' => $this->renderMessages(),
                    'addFormHtml' => $this->renderEditForm($form),
                ));
            }

            return new JsonModel(array(
                'result' => 'fail',
                'addFormHtml' => $this->renderEditForm($form),
            ));
        }

        return new JsonModel(array(
            'result' => 'fail'
        ));
    }

    public function deleteAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = (int) $request->getPost('id', 0);

            if ($id) {
                $this->getChatTable()->deleteItem($id);

                return new JsonModel(array(
                    'result' => 'ok',
                    'html' => $this->renderMessages(),
                ));
            }
        }

        return new JsonModel(array(
            'result' => 'fail'
        ));
    }

    public function changeStatusAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = (int) $request->getPost('id', 0);
            try {
                $item = $this->getChatTable()->getItem($id);
            }
            catch (\Exception $ex) {
                return new JsonModel(array(
                    'result' => 'fail',
                    'errorDetails' => $ex->getMessage(),
                ));
            }

            $item->status = 'done';

            if ($request->getPost('status') == 'undone') {
                $item->status = 'undone';
            }
            $this->getChatTable()->saveItem($item);

            return new JsonModel(array(
                'result' => 'ok',
                'listHtml' => $this->renderMessages(),
            ));
        }

        return new JsonModel(array(
            'result' => 'fail'
        ));
    }

    public function getListAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \Exception("Not ajax request");
        }

        return new JsonModel(array(
            'result' => 'ok',
            'listHtml' => $this->renderMessages(),
        ));
    }

    public function getChatTable()
    {
        if (!$this->chatTable) {
            $sm = $this->getServiceLocator();
            $this->chatTable = $sm->get('Chat\Model\ChatTable');
        }
        return $this->chatTable;
    }

}
