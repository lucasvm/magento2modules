<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Digital\Importer\Model\Rate;
/**
 * Tax Rate CSV Import Handler
 */
class CsvImportHandler
{
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;
    /**
     * Region collection prototype
     *
     * The instance is used to retrieve regions based on country code
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected $_regionCollection;
    /**
     * Country factory
     *
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;
    /**
     * Tax rate factory
     *
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $_taxRateFactory;
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
   private $messageManager;

       /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /** @var  \Magento\Catalog\Model\ResourceModel\Product */
    protected $productResourceModel;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
    ) {
        // prevent admin store from loading
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_regionCollection = $regionCollection;
        $this->_countryFactory = $countryFactory;
        $this->_taxRateFactory = $taxRateFactory;
        $this->csvProcessor = $csvProcessor;
        $this->messageManager = $messageManager;
        $this->productFactory = $productFactory;
        $this->productResourceModel = $productResourceModel;
    }
    /**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Code'),
            1 => __('Country'),
            2 => __('State'),
            3 => __('Zip/Post Code'),
            4 => __('Rate'),
            5 => __('Zip/Post is Range'),
            6 => __('Range From'),
            7 => __('Range To')
        ];
    }
    /**
     * Import Tax Rates from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        $filename = "artikels.csv";

        $file = fopen(__DIR__ .'/dev12.fietsenmagazijn.nl/'.$filename, 'r+');
        //Loop through the CSV rows.

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
        $product = $objectManager->create('\Magento\Catalog\Model\Product');



        while (($row = fgetcsv($file, 1000, "\t")) !== FALSE) {
            //Print out my column data.
            $sku = $row[0];
            $name = $row[1];
            $getSku = $product->getIdBySku($sku);

            if($getSku) {
                $actualPrice = $product->getFinalPrice();

            } else {

                    $product->setSku($sku); // Set your sku here
                    $product->setName($name . " - " . $sku);
                    $product->setAttributeSetId(4); // Attribute set id
                    $product->setStatus(1); // Status on product enabled/ disabled 1/0
                    $product->setWeight(10); // weight of product
                    $product->setStoreId(1);
                    $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                    $product->setTaxClassId(0); // Tax class id
                    $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
                    $product->setPrice(100); // price of product
                    $product->setStockData(
                                            array(
                                                'use_config_manage_stock' => 0,
                                                'manage_stock' => 1,
                                                'is_in_stock' => 1,
                                                'qty' => 999999999
                                            )
                                        );
                    $product->save();



            $this->messageManager->addSuccess('Product with SKU '.$sku.' Added');
            }
               
        }  
                
    }
   
}