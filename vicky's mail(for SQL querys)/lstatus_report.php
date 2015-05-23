<?php 
require_once APPPATH.'/libraries/REST_Controller.php';
class Lstatus_report extends REST_Controller
{
    private $sData;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();                                // Load the database library
        $this->sData = $this->session->userdata('user');        //Get the current User
	}
	
	public function index()
	{echo 'Dinesh';die;}
	
	
	
	public function index_get()
	{
		$this->load->model('Report_model');
		//$array['data'] = $this->Report_model->listedreport_mod();
		  $array = $this->Report_model->listedreport_mod();
		
		date_default_timezone_set('Asia/Kolkata');
		//echo date_default_timezone_get ();
		$i = 0;
         $mailBody = "<br>Open Attachments for the Detailed Report<br><br>
						Summary:<br>
						Number of SKU's Listed : ".$array[$i++]."<br>
						Number of Items Listed : ".$array[$i++]."<br>";
		//				Number of Items Listed In Last 24 Hours: ".$array[$i++]."<br>";
						
        			
													
							
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
                $this->email->from('vicky.singh@overcart.com');
                $this->email->to('product@overcart.com'); 
                $this->email->subject('Daily Listed Stock Report for : '.date("d-m-Y"));
                $this->email->message($mailBody);

				if(file_exists("./uploads/Listedstock-".date("d-m-Y").".csv"))
				{
					$this->email->attach("./uploads/Listedstock-".date("d-m-Y").".csv");
				}
				if(file_exists("./uploads/Last24hourslisting-".date("d-m-Y").".csv"))
				{
					$this->email->attach("./uploads/Last24hourslisting-".date("d-m-Y").".csv");
				}
				
                $this->email->send();
				//echo $this->email->print_debugger();
				         
		 
	}
	
	
	public function qcupdate_get()
	{
		$this->load->model('Report_model');
		//$array['data'] = $this->Report_model->listedreport_mod();
		  $array = $this->Report_model->qcupdatereport_mod();
		
		date_default_timezone_set('Asia/Kolkata');
		//echo date_default_timezone_get ();
		$i = 0;
				
		$mailBody = "QC Activities on last day<br>";			
			$mailBody.="<html>
                            <head>
                                <title>QC Update Report</title>
                                <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css'>
								<style>
										table {
											border-collapse: collapse;
											border: 1px solid black;
										}

										th {
											text-align: center;
											border: 1px solid black;
										}
										td {
												padding: 15px;
												border: 1px solid black;
											}
										</style>
                            </head>
                            <body>
                            <table class='table table-bordered'>
                                <thead>
                                    <tr>
										<th></th>
                                        <th># Product Qced</th>
                                        <th># Products Sent To Service Center</th>
                                        <th># Products Returned From Service Center</th>
										<th># Products Listed or Ready to Upload</th>";
																				
										
                        $mailBody.="</tr>
                                </thead>
                                <tbody>";
											
            	    $mailBody .= "<tr>
								<td>Delhi </td>";
					for($i=0;$i<4;$i++){					
							$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Banglore </td>";
								for($i=4;$i<8;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Total</td>";
								for($i=8;$i<12;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
										
					$mailBody .= "</tr></tbody>
								</table><br><br>
								Products Out To Service Center By # of Days
						<table class='table table-bordered'>
                                <thead>
                                    <tr>
										<th></th>
                                        <th> 0-3 Days</th>
                                        <th> 4 - 7 Days</th>
                                        <th> 1 Week</th>
										<th> 2 Weeks</th>
										<th> 3 Weeks or More</th>
										<th> Time Unknown</th>";
																				
										
                    $mailBody.="</tr>
                                </thead>
                                <tbody>";
											
            	    $mailBody .= "<tr>
								<td>Delhi </td>";
								for($i=12;$i<18;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Banglore </td>";
								for($i=18;$i<24;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Unknown </td>";
								for($i=24;$i<30;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Total</td>";
								for($i=30;$i<36;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
         				
				$mailBody .= "</tr></tbody>
                        </table> 
                        </body>
                        </html>";
				
				echo $mailBody;									
							
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
                $this->email->from('vicky.singh@overcart.com');
                $this->email->to('vicky.singh@overcart.com'); 
                $this->email->subject('TEST for Daily QC Update for : '.date("d-m-Y"));
                $this->email->message($mailBody);

				if(file_exists("./uploads/QCupdate-".date("d-m-Y").".csv"))
				{
					$this->email->attach("./uploads/QCupdate-".date("d-m-Y").".csv");
				}
				if(file_exists("./uploads/ServiceCentreUpdate-".date("d-m-Y").".csv"))
				{
					$this->email->attach("./uploads/ServiceCentreUpdate-".date("d-m-Y").".csv");
				}
								
                $this->email->send();
				//echo $this->email->print_debugger();
				         
		 
	}
	
	public function assorted_get()
	{
		$this->load->model('Report_model');
		$array = $this->Report_model->assortedreport_mod();
		
		date_default_timezone_set('Asia/Kolkata');
								
			$mailBody = "";			
			$mailBody.="<html>
                            <head>
                                <title>QC Summary Report</title>
                                <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css'>
								<style>
										table {
											border-collapse: collapse;
											border: 1px solid black;
										}

										th {
											text-align: center;
											border: 1px solid black;
										}
										td {
												padding: 15px;
												border: 1px solid black;
											}
										</style>
                            </head>
                            <body>
                            <table class='table table-bordered'>
                                <thead>
                                    <tr>
										<th></th>
                                        <th>Under QC</th>
                                        <th>Pending QC</th>
                                        <th>Pending Repair</th>
										<th>Manager's Escalation</th>
										<th>Ready to Upload</th>
										<th>Listed</th>
										<th>BER</th>
										<th>Out for Repair</th>
										<th>In Transit</th>
										<th>Data Discrepancies</th>";
										
										
                        $mailBody.="</tr>
                                </thead>
                                <tbody>";
											
            	    $mailBody .= "<tr>
								<td>Delhi Warehouse</td>";
								for($i=0;$i<10;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Bangalore Warehouse</td>";
								for($i=10;$i<20;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Warehouse Unknown</td>";
								for($i=20;$i<30;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
					$mailBody .= "</tr><tr>
								<td>Total</td>";
								for($i=30;$i<40;$i++){					
										$mailBody .= "<td>".$array[$i]."</td>";}
						
												
			$mailBody .= "</tr></tbody>
                        </table> 
                        </body>
                        </html>";			
							
        	
			echo $mailBody;			
								
				$this->load->library('email');
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
                $this->email->from('vicky.singh@overcart.com');
                $this->email->to('operations@overcart.com'); 
                $this->email->subject('QC Summary Report for : '.date("d-m-Y"));
                $this->email->message($mailBody);
				
					$status = array('Under QC','Pending QC','Pending Repair','Manager\'s Escalation',
									'Ready to Upload','Listed','BER','Out for Repair','InTransit',
									'DataDiscrepancies','DelayedQC','cw_');
					
					foreach($status as $value)
					{
						if(file_exists("./uploads/".$value."stock-".date("d-m-Y").".csv"))
							{
								$this->email->attach("./uploads/".$value."stock-".date("d-m-Y").".csv");}
							}												
				
                $this->email->send();
				//echo $this->email->print_debugger();
				
         
		 
	}
	
	

    public function index_post()
    {
        $return = array('status'=>"fail",'message'=>"Request Method Not Supported");
        echo json_encode($return);
    }
	
    
}