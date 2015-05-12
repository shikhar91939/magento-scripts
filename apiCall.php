<?php
// ________________________________get Order info (to check if shipped same day)___________________________________

try{
$client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
$session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');


$params = array('complex_filter'=>
    array(
        array('key'=>'created_at','value'=>array('key' =>'from','value' => '2015-05-01 00:00:00')),
        array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => '2015-05-07 00:00:00'))
    )
);
echo "<pre>";
$result = $client->salesOrderInfo	($session,'BST1100012893');
var_dump($result);die;
$result = $client->salesOrderList($session,$params);
// echo "<pre>";
// var_dump($result);
// echo "</pre>";
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

echo count($result);die;
// ----------------------------------------------------------------------------------------------------------------
//--------------------------------------------get all orders between 2 dates------------------------------
try {
$client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
$session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');


$params = array('complex_filter'=>
    array(
        array('key'=>'created_at','value'=>array('key' =>'from','value' => '2015-05-01 00:00:00')),
        array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => '2015-05-07 00:00:00'))
    )
);
$result = $client->salesOrderList($session,$params);
// echo "<pre>";
// var_dump($result);
// echo "</pre>";
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

echo count($result);

foreach ($result as $i) {
echo "<hr><pre>";
  // var_dump($i);die;
  var_dump($i->increment_id);
  var_dump($i->created_at);
echo "</pre>";
}