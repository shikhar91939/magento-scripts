<?php

require_once './app/Mage.php';
Mage::app();
ini_set('display_errors', 1);

// umask(0);
// Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$file = fopen('./var/newAttrValues.csv', 'r'); //Change the file name here


$row = 1;
$hrow=1;
while (($data = fgetcsv($file)) !== FALSE) 
{ 
    if($row != 1)
    {
    	try
        {

        	$arg_attribute = "author";
			$arg_value = $data[0];
			
            addLabel($arg_attribute, $arg_value);
        }
        catch (Exception $e) 
        {
            throw $e;
        }
    }
    $row++;
}

    function addLabel($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                echo "$arg_value already exists<br/>";
                return;
            }
        }
        $attribute->setData('option', array('value' => array('option' => array($arg_value,$arg_value) ) ));
        $attribute->save();
        echo "saved $arg_value<br/>";
    }