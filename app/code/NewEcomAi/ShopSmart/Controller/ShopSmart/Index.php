<?php

namespace NewEcomAi\ShopSmart\Controller\ShopSmart;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{


    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    private ForwardFactory $resultForwardFactory;

    /**
     * Index constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory    $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('NewEcomAi_ShopSmart::listing');
        $resultPage->getConfig()->getTitle()->prepend(__('Shop Smart'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('NewEcomAi'), __('NewEcomAi'));
        $resultPage->addBreadcrumb(__('Shop Smart'), __('Shop Smart'));

        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('NewEcomAi_ShopSmart::masscancel');
    }
}

