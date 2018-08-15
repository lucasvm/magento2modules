<?php

namespace Digital\Importer\Controller\Adminhtml\Packadgeable;


use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;
use Magento\Framework\App\State;

 
class Index extends \Magento\Backend\App\Action
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
	$url = "http://www.wagelaar-enschede.eu/artikels/";
        $fileRemoteName = "artikels.csv";	
        $username = "Wagelaar";
        $password = "Wage4CS";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
        $mediaPath=$fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

        $ch = curl_init();
    	$source = $url.$fileRemoteName;
    	curl_setopt($ch, CURLOPT_URL, $source);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    	$data = curl_exec ($ch);
    	curl_close ($ch);

    	$destination = $mediaPath."/".$fileRemoteName;
    	$file = fopen($destination, "w+");
    	fputs($file, $data);
    	fclose($file);

        
        $file = $mediaPath."/artikels.csv";
        $fh = fopen($file, 'r');


        $flag = true;
        while (($line = fgetcsv($fh, 1000, "\t")) !== FALSE) {
        if($flag) { $flag = false; continue; }
   


          $sku = $line[0];
          $name = $line[1];
          $price = $line[6];
          $eancode = $line[8];
          $inStock = $line[14];
          //Category ID for Fietzen 
          // Root category ID 70
          // Csv category id will be loaded automatically if exists on magento
          $defaultCategory = 70;
          $categoryName = "Tassen & Manden";

         /* $categoryNameFromCsv = $line[3];

          $collection = $objectManager->get('Magento\Catalog\Model\CategoryFactory')->create()->getCollection()
		->addFieldToSelect('name')
		->addFieldToFilter('name', ['in' => $categoryNameFromCsv]);*/
 

         /* $getCategoryId = $collection->getFirstItem()->getId();

          if ($getCategoryId) {
            $categoryId = $getCategoryId;
          } else {
            $categoryId = $defaultCategory;
          } */

          //Diverse category id = 75
          //Default category id = 70 

          $categoryChildId2 = '';
          $categoryChildId3= '';

          if ($categoryName == $line[3]) {
            $categoryId = 9427;
          } else {
            $categoryId = 70;
          }

  
          

          $productRepo = $objectManager->create('Magento\Catalog\Model\ProductRepository');
          /*if(!$setup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'ean')) {
                //Create the attribute
          }*/
          try {
                $_product = $productRepo->get($sku);
                $getSku = $_product->getSku();
                $actualPrice = $_product->getPrice();

                if ($getSku) {
                        $intValActual = intval($actualPrice);
                        $intVal = intval($price);
                        if ($intVal < $intValActual) {
                          $price = $intValActual;
                        } else {
                          $price = $intVal;
                        }
                         $_product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                            $_product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                            $_product->setIsMassupdate(true);
                            $_product->setExcludeUrlRewrite(true);
                            $_product->setEan($eancode);
                            //If you want to add a new subcategory add id below next to comma
                            $_product->setCategoryIds($categoryId);
                            $_product->setPrice($price);
                            $_product->setStockData(
                                        array(
                                            'use_config_manage_stock' => 0,
                                            'manage_stock' => 1,
                                            'is_in_stock' => $inStock,
                                            'qty' => 999999999
                                        )
                                    );
                         $_product->save();
  
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                $_product = $objectManager->create('\Magento\Catalog\Model\Product');
                $_product->setSku($sku); // Set your sku here
                $_product->setName($name); // Name of Product
                $_product->setAttributeSetId(4); // Attribute set id
                $_product->setStatus(1); // Status on product enabled/ disabled 1/0
                $_product->setWeight(10); // weight of product
                $_product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $_product->setTaxClassId(0); // Tax class id
                $_product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                $_product->setPrice($price); // price of product
  
                $_product->setCategoryIds($categoryId);
                $_product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                            $_product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                            $_product->setIsMassupdate(true);
                            $_product->setExcludeUrlRewrite(true);
                $_product->setStockData(
                                        array(
                                            'use_config_manage_stock' => 0,
                                            'manage_stock' => 1,
                                            'is_in_stock' => $inStock,
                                            'qty' => 999999999
                                        )
                                    );
                $_product->setEan($eancode);
                $_product->save();
            }
            
        }
	// Display Script End time
	$time_end = microtime(true);

	//dividing with 60 will give the execution time in minutes other wise seconds
	$execution_time = ($time_end - $time_start)/60;

	//execution time of the script
	echo 'Script Finished <b>Total Execution Time:</b> '.$execution_time.' Mins';
        if (file_exists($file)) {
            //If file exists remove...	
            unlink($file);
        }
            
           
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
