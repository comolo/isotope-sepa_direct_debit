<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Isotope_sepa
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'ComoloIsotope',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Library
	'ComoloIsotope\Model\Payment\SepaDirectDeposit' => 'system/modules/isotope_sepa/library/Isotope/Model/Payment/SepaDirectDeposit.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'iso_be_payment_sepa' => 'system/modules/isotope_sepa/templates',
));