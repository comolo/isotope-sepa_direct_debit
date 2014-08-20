<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @copyright  Hendrik Obermayer <mail@comolo.de>
 *
 */

namespace ComoloIsotope\Model\Payment;

use Isotope\Interfaces\IsotopePayment;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Payment\Postsale;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\Payment\Cash;

class SepaDirectDeposit extends Cash implements IsotopePayment
{

    /**
     * SEPA only supports these currencies
     * @return  true
     */
    public function isAvailable()
    {
        if (!in_array(Isotope::getConfig()->currency, array('EUR'))) {
            return false;
        }
        
        if(!FE_USER_LOGGED_IN) {
            return false;
        }
        else {
            $user = \FrontendUser::getInstance();
            if(!isset($user->iso_sepa_active) || $user->iso_sepa_active != "1") {
                return false;
            }
        }

        return parent::isAvailable();
    }

    /**
    * Return information or advanced features in the backend.
    *
    * Use this function to present advanced features or basic payment information for an order in the backend.
    * @param integer Order ID
    * @return string
    */
    
    /*
    public function backendInterface($orderId)
    {
        //
    }
    */
}