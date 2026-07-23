<?php
namespace Thecommerceshop\Map\Controller\Adminhtml\Discountbuild;

use Thecommerceshop\Map\Cron\UpdateRule;

class Index extends \Magento\Backend\App\Action
{
    protected $_logger;
    protected $_DiscountCron;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        UpdateRule $DiscountCron,
    ) {
        $this->_logger = $logger;
        parent::__construct($context);
        $this->_DiscountCron = $DiscountCron;
    }
    public function execute()
    {
        $this->_DiscountCron->execute();
        //$this->_logger->debug('Sync Starts!!'); 
    }
}
