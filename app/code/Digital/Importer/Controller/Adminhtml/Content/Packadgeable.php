<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Digital\Importer\Controller\Adminhtml;

abstract class Packadgeable extends \Magento\Backend\App\Action
{
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->_request->getActionName()) {
            case 'regenerate':
                return $this->_authorization->isAllowed('Digital_Importer::save');
        }
        return false;
    }
}
