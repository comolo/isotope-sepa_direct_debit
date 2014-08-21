<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 *
 * @package    Isotope
 * @license    proprietary
 * @copyright  Hendrik Obermayer, Comolo GmbH <mail@comolo.de>
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
     * @param IsotopeProductCollection $objOrder
     * @param \Module $objModule
     * @return mixed
     */
    public function processPayment(IsotopeProductCollection $objOrder, \Module $objModule)
    {
        // Get user's SEPA account
        $user = \FrontendUser::getInstance();

        // Save to order
        $objOrder->payment_data = array(
            'iso_sepa_iban'             => $user->iso_sepa_iban,
            'iso_sepa_bic'              => $user->iso_sepa_bic,
            'iso_sepa_accountholder'    => $user->iso_sepa_accountholder,
            'iso_sepa_mandate'          => $user->iso_sepa_mandate,
            'iso_sepa_date_of_issue'    => $user->iso_sepa_date_of_issue,
        );

        return parent::processPayment($objOrder, $objModule);
    }

    /**
    * Return information or advanced features in the backend.
    *
    * Use this function to present advanced features or basic payment information for an order in the backend.
    * @param integer Order ID
    * @return string
    */
    public function backendInterface($orderId)
    {
        $database = \Database::getInstance();
        $order = $database
                    ->prepare("SELECT * FROM tl_iso_product_collection WHERE id LIKE ?")
                    ->limit(1)
                    ->execute($orderId);


        $template = new \BackendTemplate('iso_be_payment_sepa');
        $template->order = $order;
        $template->payment_data = unserialize($order->payment_data);

        return $template->parse();
    }
}