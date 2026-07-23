<?php

namespace Thecommerceshop\Map\Controller\Index;

/**
 * Class Updatecart
 *
 * @package Thecommerceshop\Map\Controller\Index
 */
class Updatecart extends \Magento\Framework\App\Action\Action
{
    /**
     * Update cart
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_redirect('/');
            return;
        }

        $jsonData = json_encode(['result' => true]);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
