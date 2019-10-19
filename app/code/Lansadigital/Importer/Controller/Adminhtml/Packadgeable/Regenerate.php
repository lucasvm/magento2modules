<?php
 
namespace Lansadigital\Importer\Controller\Adminhtml\Packadgeable;

use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\State;
 
class Regenerate extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        State $appState,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_appState = $appState;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
    
   /**
     * @return void
     */
   public function execute()
   { 
        $resultPage = "Hola";
        
        return $resultPage;
   }

   /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lansadigital_Importer::packadgeable');
    }
}