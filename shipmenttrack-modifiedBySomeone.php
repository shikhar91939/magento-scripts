<?php 
error_reporting(E_ALL);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';

umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$resource = Mage::getSingleton('core/resource');
$writeC = $resource->getConnection('core_write');
$readC = $resource->getConnection('core_read');
$file = fopen('./var/shipmenttrack.csv', 'r');

$row = 1;
$hrow=1;
ini_set('display_errors', 1);
error_reporting(E_ALL);
while (($data = fgetcsv($file)) !== FALSE)
{
    if($row != 1)
    {
        $customerEmailComments = $data[4];
        echo 'data[0]= '.$data[0].': ';
        $order = Mage::getModel('sales/order')
                 ->loadByIncrementId($data[0]);
 
        if (!$order->getId()) {
            Mage::throwException("Order does not exist, for the Shipment process to complete");
        }
        // if($order->getStatus()!='pick_pack' && $order->getStatus() != 'readytoship' && $order->getStatus() == 'canceled')
        // {
        //     continue;
        // }
        if ($order->canShip()) 
        {
            try 
            {
                $shipment = Mage::getModel('sales/service_order', $order)
                                ->prepareShipment(getItemQtys($order));
                $arrTracking = array(
                    'carrier_code' => $data[1],
                    'title' => $data[2],
                    'number' => $data[3],
                );
     
                $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                $shipment->addTrack($track);
                $shipment->register();
                $order->addStatusHistoryComment($customerEmailComments, true);
                saveShipment($shipment, $order, $customerEmailComments);
                if($shipment){
                        if(!$shipment->getEmailSent()){
                            $shipment->save();                          
                        }
                    }
                     
                saveOrder($order);
				foreach($order->getAllItems() as $item)
				{
					UpdateItemStatus($item);
				}
            } 
            catch (Exception $e) 
            {
                throw $e;
            }
            echo $data[0].' is updated.<br>';
        }
        else
        {
            echo $data[0].' is not updated.<br>';
        }
    }
    $row++;
}
fclose($file);
echo "$row Updated. Import finished.";
function getItemQtys(Mage_Sales_Model_Order $order)
{
    $qty = array();
 
    foreach ($order->getAllItems() as $_eachItem) {
        if ($_eachItem->getParentItemId()) {
            $qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
        } else {
            $qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
        }
    }
 
    return $qty;
}
function saveShipment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order, $customerEmailComments = '')
{
    $shipment->getOrder()->setIsInProcess(true);
    $transactionSave = Mage::getModel('core/resource_transaction')
                           ->addObject($shipment)
                           ->addObject($order)
                           ->save();
 
    $emailSentStatus = $shipment->getData('email_sent');
    if (!is_null($customerEmail) && !$emailSentStatus) {
        $shipment->sendEmail(true, $customerEmailComments);
        $shipment->setEmailSent(true);
    }
 
    return $this;
}
function saveOrder(Mage_Sales_Model_Order $order)
{
    $order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
    $order->addStatusToHistory('readytoship', 'Order marked Ready to ship through Script.', false);
 
    $order->save();
 
    return $this;
}
function UpdateItemStatus($item)
{
	$myitems=Mage::getModel('awa_serialcode/mysavedorder')->getCollection()
					->addFieldToFilter('item_id',$item->getItemId());
	if(count($myitems)>0)
	{
		foreach($myitems as $myitem)
		{
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');	
			$query = "UPDATE awa_serialcode_mysavedorder SET item_status ='Ready To Ship'
							 WHERE entity_id = "
						  . (int)$myitem->getEntityId();
			$write->query($query);
		}
	}
}