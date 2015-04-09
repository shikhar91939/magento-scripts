<?php 
error_reporting(E_ALL);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';

umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$resource = Mage::getSingleton('core/resource');
$writeC = $resource->getConnection('core_write');
$readC = $resource->getConnection('core_read');
$file = fopen('./var/toBeMarkedDelivered.csv', 'r');

$row = 1;
$hrow=1;
ini_set('display_errors', 1);
error_reporting(E_ALL);
while (($data = fgetcsv($file)) !== FALSE) { 
//print_r($data);
    if($row != 1)
    {
        try{
        //echo $data[0];die;
        $customerEmailComments = $data[1];
         $order = Mage::getModel('sales/order')
                         ->loadByIncrementId($data[0]);
         
            if (!$order->getId())
                Mage::throwException("Order does not exist");
            // echo $data[0];
            // echo $order->getStatus();die;
            if($order->getStatus() =='complete'){// now marking as delivered...
                // echo 'entered ';echo $order->getStatus();die;


                // $order->setStatus('Delivered');
                // $order->setData('status', 'Delivered');
                $order->addStatusToHistory('delivered', $customerEmailComments, false);
                $order->save();
            }
        echo $data[0].' is updated.<br>';
        }catch (Exception $e) {
            throw $e;
        }
    }
    $row++;
}
    fclose($file);
//echo $row;
echo "$row Updated. Import finished.";
    