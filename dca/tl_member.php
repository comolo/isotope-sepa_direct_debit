<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


    
/**
 * Table tl_member
 */

$GLOBALS['TL_DCA']['tl_member']['palettes']['__selector__'][] = 'iso_sepa_active';

$GLOBALS['TL_DCA']['tl_member']['subpalettes']['iso_sepa_active'] = 'iso_sepa_iban,iso_sepa_bic,iso_sepa_accountholder,iso_sepa_mandate,iso_sepa_date_of_issue';

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
    '{contact_legend}', 
    '{iso_sepa_legend},iso_sepa_active;{contact_legend}', 
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);
    
$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_active'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_active'],
	'search'                  => true,    
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_iban'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_iban'],
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>34, 'feViewable'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(34) NOT NULL default ''",
    'save_callback'           => array(
        array('tl_member_iso_sepa', 'checkIban')
    )
);

$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_bic'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_bic'],
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>11, 'feViewable'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(11) NOT NULL default ''",
    'save_callback'           => array(
        array('tl_member_iso_sepa', 'checkBic')
    )
);

$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_accountholder'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_accountholder'],
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'feViewable'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_mandate'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_mandate'],
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'unique'=>true, 'maxlength'=>50, 'feViewable'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(50) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['iso_sepa_date_of_issue'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['iso_sepa_date_of_issue'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => "varchar(10) NOT NULL default ''"
);


class tl_member_iso_sepa 
{
    protected $countries = array(
        'al'=>28, 'ad'=>24, 'at'=>20,'az'=>28,'bh'=>22,'be'=>16,
        'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,
        'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,
        'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,
        'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,
        'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,
        'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,
        'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,
        'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,
        'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,
        'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,
        'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,
        'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24
    );
    
    public function checkBic($bic)
    {   
        $bic = strtolower(str_replace(' ', '', $bic));
        
        // Check lenght
        if(strlen($bic) === 9 || strlen($bic) === 11) 
        {
            // Check country code
            $countryCode = substr($bic, 4, 2);
            if(isset($this->countries[$countryCode])) {
                
                // Check Bank Code (first 4 letters)
                $bankCode = substr($bic, 0, 4);
                if(ctype_alpha($bankCode)) {
                    
                    // check location code (2 letters)
                    $locationCode = substr($bic, 6, 2);
                    if($locationCode == str_replace('0', '', $locationCode)){
                        return strtoupper($bic);
                    }
                }
            }
        }
        
        
        throw new \Exception('BIC is not valid!');
    }
    
    public function checkIban($iban)
    {   
        $iban = strtolower(str_replace(' ', '', $iban));
            
        $chars = array(
            'a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,
            'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,
            'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,
            'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,
            'x'=>33,'y'=>34,'z'=>35
        );

        if(strlen($iban) == $this->countries[substr($iban,0,2)])
        {
            $movedChar = substr($iban, 4).substr($iban,0,4);
            $movedCharArray = str_split($movedChar);
            $newString = '';
            
            foreach($movedCharArray AS $key => $value)
            {
                if(!is_numeric($movedCharArray[$key]))
                {
                    $movedCharArray[$key] = $chars[$movedCharArray[$key]];
                }
                $newString .= $movedCharArray[$key];
            }

            if(bcmod($newString, '97') == 1)
            {
                return strtoupper($iban);
            }
        }
        
        throw new \Exception('IBAN is not valid!');
    }
}