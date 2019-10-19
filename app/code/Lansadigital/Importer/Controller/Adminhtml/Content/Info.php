<?php
namespace Stack\Example\Controller\Adminhtml\Content;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Info extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lansadigital_Importer::content';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Update the breadcrumb and extra information
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Lansadigital Importer'), __('Content'));
        $resultPage->getConfig()->getTitle()->prepend(__('Content'));

        return $resultPage;
    }
}