<?php

namespace Digital\Importer\Controller\Adminhtml\Packadgeable;


use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;
use Magento\Framework\App\State;

 
class Categories extends \Magento\Backend\App\Action
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
     * @return void
     */
   public function execute()
   { 
  	      $time_start = microtime(true);

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
          $mediaPath=$fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
          
          $file = $mediaPath."/artikels.csv";
          $fh = fopen($file, 'r');


          $flag = true;
          while (($line = fgetcsv($fh, 1000, "\t")) !== FALSE) {
          if($flag) { $flag = false; continue; }
          //The root category in Magento, the one you get by default install, usually has the id 2
            $_rootParentCat = 70; 
           
            /* START Create first level categories */
            $_rootCats = $this->_db->fetchCol("SELECT DISTINCT ProductCategory1 FROM SomeProductCategoryTable");
           
           
            foreach($_rootCats as $_rootCat)
            {
              $_c = $this->_db->fetchOne("SELECT ProductCategory1URL FROM SomeProductCategoryTable WHERE ProductCategory1 = ?", array($_rootCat));
           
           
              if($_c != '' && $_c != 'na')
              {
                $_cat = new Mage_Catalog_Model_Category();
                $_cat->setName($_rootCat);
                $_cat->setUrlKey($_c);
                $_cat->setIsActive(1);
           
                $parentCategory = Mage::getModel('catalog/category')->load($_rootParentCat);
                $_cat->setPath($parentCategory->getPath());       
           
                $_cat->save();
                Zend_Debug::dump($_c);
           
                /* START Create second level categories */  
                $_childCats = $this->_db->fetchCol("SELECT DISTINCT ProductCategory2 FROM SomeProductCategoryTable WHERE ProductCategory1 = ?", array($_rootCat));
           
                foreach($_childCats as $_childCat) 
                {
                  $_cc = $this->_db->fetchOne("SELECT ProductCategory2URL FROM SomeProductCategoryTable WHERE ProductCategory2 = ?", array($_childCat));
           
                  if($_cc != '' && $_cc != 'na')
                  {
                    $_catChild = new Mage_Catalog_Model_Category();
                    $_catChild->setName($_childCat);
                    $_catChild->setUrlKey($_cc);
                    $_catChild->setIsActive(1); 
           
                    $parentCategory = Mage::getModel('catalog/category')->load($_cat->getId());
                    $_catChild->setPath($parentCategory->getPath());              
           
                    $_catChild->save(); 
                    Zend_Debug::dump($_cc); 
                    unset($_catChild);            
                  }
                }
                /* END Create second level categories */
                unset($_cat);   
              }
           
            }
            /* END Create first level categories */
     }       
  	// Display Script End time
  	$time_end = microtime(true);

  	//dividing with 60 will give the execution time in minutes other wise seconds
  	$execution_time = ($time_end - $time_start)/60;

  	//execution time of the script
  	echo 'Script Finished <b>Total Execution Time:</b> '.$execution_time.' Mins';
              
             
     }
    
    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Importer::packadgeable');
    }
}
