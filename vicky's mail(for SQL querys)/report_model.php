<?php

class Report_model extends CI_Model {

		public function __construct()
			{
				parent::__construct();
				 $this->load->dbutil();
				 $this->load->helper('file');
			}



		public function listedreport_mod()
			{
							
					$delimiter = ",";
					$newline = "\r\n";		
						
							
					$sql = "SELECT brands.name as brand,
							products.product_name as prod_name,
							products.model_number as model,
							colors.name as color,
							products.sku,
							count(products.id) as quantity,
							ROUND(AVG(NULLIF(products.tprice, 0))) as trans_price
							FROM products
							left outer join brands on products.manufacture_id = brands.id 
							left outer join colors on products.color_id = colors.id 
							where products.lstatus = 'Listed' 
							group by products.sku 
							order by brand, model";
					
					$query = $this->db->query($sql);
					
					$count = array();
					array_push($count,$query->num_rows);
				
								
																										
					$csv =  $this->dbutil->csv_from_result($query,$delimiter,$newline); 
							//echo "".$csv."<br>";
							if ( !write_file("./uploads/Listedstock-".date("d-m-Y").".csv", $csv)){
									echo '<br>Unable to write the file<br><br>';}
							else{
									echo '<br>File written!<br><br>';}
																			
					
					$array = $query->result_array();
					$a=0;
					foreach($array as $value){$a += $value['quantity'];}
					array_push($count,$a);
					
									
					// $sql1 ="SELECT brands.name as brand,
							// products.product_name as prod_name,
							// products.model_number as model,
							// colors.name as color,products.sku,
							// products.imei as imei,
							// products.lstatus as current_status,
							// membership.user_name as updated_by,
							// addtime(status_update_log.time_stamp,053000) as updated_at 
							// from products 
							// left outer join brands on products.manufacture_id = brands.id 
							// left outer join colors on products.color_id = colors.id 
							// left outer join status_update_log on products.id = status_update_log.productid
							// left outer join membership on membership.id = status_update_log.user_id
							// where status_update_log.new_status = 'Listed' and status_update_log.time_stamp > date_sub(now(),interval 24 hour) 
							// order by brand, model";
							
					// $query1 = $this->db->query($sql1);
					// array_push($count,$query1->num_rows);
					
					// if(($query1->num_rows)>0)
					// {
						// $csv1 =  $this->dbutil->csv_from_result($query1,$delimiter,$newline);
							// if ( !write_file("./uploads/Last24hourslisting-".date("d-m-Y").".csv", $csv1)){
								// echo '<br>Unable to write the file<br><br>';}
							// else{
								// echo '<br>File written!<br><br>';}	
						// }

					return $count;
			}
			
			
		public function qcupdatereport_mod()
			{
							
					$delimiter = ",";
					$newline = "\r\n";		
						
					// $sql= "SELECT brands.name as brand,
							 // products.product_name as prod_name,
							 // products.model_number as model,
							 // colors.name as color,
							 // products.sku,
							 // products.imei as imei,
							 // products.location as location,
							 // operationcenters.name as center,
							 // status_update_log.old_status,
							 // status_update_log.new_status,
							 // membership.user_name as updated_by,
							 // addtime(status_update_log.time_stamp,053000) as updated_at 
							 // from products 
							 // left outer join brands on products.manufacture_id = brands.id 
							 // left outer join colors on products.color_id = colors.id 
							 // left outer join operationcenters on products.op_center = operationcenters.id
							 // left outer join status_update_log on products.id = status_update_log.productid
							 // left outer join membership on membership.id = status_update_log.user_id
							 // where status_update_log.time_stamp between date_sub(now(),interval 25 hour) and date_sub(now(),interval 1 hour)
							 // order by brand, model";
							 
					$sql = "SELECT brands.name as brand,
								products.product_name as prod_name,
								products.model_number as model,
								colors.name as color,
								products.sku,
								products.imei as imei,
								products.date_of_qc,
								products.lstatus as status,
								products.location as location,
								op2.name as center,
								detail_loc_log.old_location,					
								op1.name as old_opcenter,
								compniesdata.name as client,
								sul1.new_status as new_status,
								sul2.old_status as old_status,
								addtime(sul1.time_stamp,053000) as time_stamp
								from products
								left outer join (SELECT products.id as table_id,max(detail_loc_log.insert_id) as max_insert from products 
												left outer join detail_loc_log on products.id = detail_loc_log.productid 
												left outer join location_update_log on detail_loc_log.insert_id = location_update_log.id 
												where location_update_log.status = 1 
												group by products.id) as m_table on products.id = m_table.table_id
								left outer join detail_loc_log on products.id = detail_loc_log.productid and m_table.max_insert = detail_loc_log.insert_id
								left outer join (SELECT products.id as table_id,max(status_update_log.id) as insert_id from products
												left outer join status_update_log on products.id = status_update_log.productid
												group by products.id) as p_table on products.id = p_table.table_id
								left outer join status_update_log as sul1 on products.id = sul1.productid and p_table.insert_id = sul1.id
								left outer join (SELECT products.id as table_id,min(status_update_log.id) as insert_id from products
												left outer join status_update_log on products.id = status_update_log.productid
												where status_update_log.time_stamp between date_sub(now(),interval 33 hour) and date_sub(now(),interval 9 hour)
												group by products.id) as q_table on products.id = q_table.table_id
								left outer join status_update_log as sul2 on products.id = sul2.productid and q_table.insert_id = sul2.id
								left outer join brands on products.manufacture_id = brands.id 
								left outer join colors on products.color_id = colors.id
								left outer join operationcenters as op1 on detail_loc_log.old_opcenter = op1.id
								left outer join operationcenters as op2 on products.op_center = op2.id
								left outer join compniesdata on products.client_id = compniesdata.id
								where sul1.time_stamp between date_sub(now(),interval 33 hour) and date_sub(now(),interval 9 hour)
								order by brand, model";	
							
					$query = $this->db->query($sql);
					
					if(($query->num_rows)>0)
					{
						$csv =  $this->dbutil->csv_from_result($query,$delimiter,$newline);
						//echo "".$csv."<br>";
						
								if ( !write_file("./uploads/QCupdate-".date("d-m-Y").".csv", $csv)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 
								
							}
							
					$count = array();
							
					$result =$query->result_array();
					$a=0;$b=0;$c=0;$d=0;$e=0;$f=0;$g=0;$h=0;$i=0;$j=0;$k=0;$l=0;
									 foreach ($result as $value)
										 { 
											if (($value['old_status'] == 'Under QC' or $value['old_status'] == 'Pending QC') and $value['new_status'] != 'Under QC' and $value['new_status'] != 'Pending QC')	
												 {  
													if ($value['center'] == 'Kalkaji' or $value['center'] == 'Barthal' )	
														{ $a++ ;
															}
													if ($value['center'] == 'BLR1' or $value['center'] == 'BLR2')
														{ $b++ ;
															}
													$c++ ;
														
												 }
												 
											if ($value['old_status'] != 'Out for Repair' and $value['new_status'] == 'Out for Repair' )	
												{  
													if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
														{ $d++ ;
															}
													if ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
														{ $e++ ;
															}
													$f++ ;
														
												}
												
											if ($value['old_status'] == 'Out for Repair' and $value['new_status'] != 'Out for Repair' )	
												{  
													if ($value['center'] == 'Kalkaji' or $value['center'] == 'Barthal' )	
														{ $g++ ;
															}
													if ($value['center'] == 'BLR1' or $value['center'] == 'BLR2')
														{ $h++ ;
															}
												 	$i++ ;
												}
												
											if ( $value['new_status'] == 'Ready to upload' or $value['new_status'] == 'Listed')	
												{  
													if ($value['center'] == 'Kalkaji' or $value['center'] == 'Barthal' )	
														{ $j++ ;
															}
													if ($value['center'] == 'BLR1' or $value['center'] == 'BLR2')
														{ $k++ ;
															}
													 $l++ ;
												}			
													
										 }
					array_push($count,$a,$d,$g,$j);						 
					array_push($count,$b,$e,$h,$k);
					array_push($count,$c,$f,$i,$l);
					
						
					
					//$count = array_merge($fcount, array_merge($scount,$tcount));
					
					
							
							
						$sql1 = "SELECT brands.name as brand,
								products.product_name as prod_name,
								products.model_number as model,
								colors.name as color,
								products.sku,
								products.imei as imei,
								products.date_of_qc,
								products.lstatus as status,
								products.location as location,
								detail_loc_log.old_location,					
								operationcenters.name as old_opcenter,
								compniesdata.name as client,
								status_update_log.new_status as log_status,
								addtime(status_update_log.time_stamp,053000) as time_stamp, 
								datediff(now(),status_update_log.time_stamp) as no_of_days 
								from products
								left outer join (SELECT products.id as table_id,max(detail_loc_log.insert_id) as max_insert from products 
												left outer join detail_loc_log on products.id = detail_loc_log.productid 
												left outer join location_update_log on detail_loc_log.insert_id = location_update_log.id 
												where location_update_log.status = 1 
												group by products.id) as m_table on products.id = m_table.table_id
								left outer join detail_loc_log on products.id = detail_loc_log.productid and m_table.max_insert = detail_loc_log.insert_id
								left outer join (SELECT products.id as table_id,max(status_update_log.id) as insert_id from products
												left outer join status_update_log on products.id = status_update_log.productid
												group by products.id) as p_table on products.id = p_table.table_id
								left outer join status_update_log on products.id = status_update_log.productid and p_table.insert_id = status_update_log.id
								left outer join membership on membership.id = status_update_log.user_id
								left outer join brands on products.manufacture_id = brands.id 
								left outer join colors on products.color_id = colors.id
								left outer join operationcenters on detail_loc_log.old_opcenter = operationcenters.id
								left outer join compniesdata on products.client_id = compniesdata.id
								where products.lstatus = 'Out for Repair' and products.location = 's_center'
								order by brand, model";	
					
					$query1 = $this->db->query($sql1);
					
					if(($query1->num_rows)>0)
					{
						$csv1 =  $this->dbutil->csv_from_result($query1,$delimiter,$newline);
						//echo "".$csv1."<br>";
						
								if ( !write_file("./uploads/ServiceCentreUpdate-".date("d-m-Y").".csv", $csv1)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 
								
							}
							
					$result =$query1->result_array();
					
					 $a=0;$b=0;$c=0;$d=0;$e=0;$f=0;$g=0;$h=0;$i=0;$j=0;$k=0;$l=0;$m=0;$n=0;$o=0;$p=0;$q=0;$r=0;$s=0;$t=0;$u=0;$v=0;$w=0;$x=0;
							
							foreach ($result as $value)
								{ 
											
										if($value['no_of_days'] != NULL )
											{		
											
												if ($value['no_of_days'] <= 3 )	
													 {  $s++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $a++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $b++ ;
																}
														else 
															{ $c++;  
																	}
													 }
												elseif ($value['no_of_days'] <= 7 )
													{ 	$t++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $d++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $e++ ;
																}
														else 
															{ $f++ ;
																}
														}
												elseif ($value['no_of_days'] <= 14 )
													{   $u++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $g++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $h++ ;
																}
														else 
															{ $i++ ;
																}
																
														}
												elseif ($value['no_of_days'] <= 21 )
													{  $v++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $j++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $k++ ;
																}
														else 
															{ $l++ ;
																}
																
														}
												else 
													{   $w++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $m++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $n++ ;
																}
														else 
															{
															   $o++ ;}
														}
											}
										else 
											{			$x++ ;
														if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
															{ $p++ ;
																}
														elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2')
															{ $q++ ;
																}
														else 
															{
															   $r++ ;}
											}
								}
											
													
										
					array_push($count,$a,$d,$g,$j,$m,$p);	
					array_push($count,$b,$e,$h,$k,$n,$q);	
					array_push($count,$c,$f,$i,$l,$o,$r);
					array_push($count,$s,$t,$u,$v,$w,$x);
					
									
					return $count;	
			}
			
		public function assortedreport_mod()
			{
					$delimiter = ",";
					$newline = "\r\n";	
								
					$sql = "SELECT brands.name as brand,
							products.product_name as prod_name,
							products.model_number as model,
							colors.name as color,
							products.sku,
							products.imei as imei,
							products.date_of_qc,
							products.lstatus as status,
							products.location as location,
							operationcenters.name as center,
							compniesdata.name as client
							from products
							left outer join brands on products.manufacture_id = brands.id 
							left outer join colors on products.color_id = colors.id
							left outer join operationcenters on products.op_center = operationcenters.id
							left outer join compniesdata on products.client_id = compniesdata.id
							where products.lstatus = ? and products.location = 'over_werehouse'
							order by brand, model";
													
					$status = array('Under QC','Pending QC','Pending Repair','Manager\'s Escalation','Ready to Upload','Listed','BER');
					$tcount = array();
					$acount = array();
					$bcount = array();
					$ccount = array();
					
						foreach($status as $value)
								{
								
																					
									$query = $this->db->query($sql,array($value));
									array_push($tcount,$query->num_rows);
																											
									if(($query->num_rows)>0)
											{
												$csv =  $this->dbutil->csv_from_result($query,$delimiter,$newline); 
												//echo "".$csv."<br>";
																						
												if ( !write_file("./uploads/".$value."stock-".date("d-m-Y").".csv", $csv)){
													echo '<br>Unable to write the file<br><br>';}
												else{
													echo '<br>File written!<br><br>';} 	
												}
												
									 $result =$query->result_array();
									 $a=0;$b=0;$c=0;
									 foreach ($result as $value)
										 { 
											
											if ($value['center'] == 'Kalkaji' or $value['center'] == 'Barthal' )	
												 { $a++ ;
														}
											elseif ($value['center'] == 'BLR1' or $value['center'] == 'BLR2' )
												{ $b++ ;	
													}
											else
												{ $c++ ;}
										 }
									
									array_push($acount,$a);	
									array_push($bcount,$b);	
									array_push($ccount,$c);
							
								}
								
								
						$sql1 = "SELECT brands.name as brand,
								products.product_name as prod_name,
								products.model_number as model,
								colors.name as color,
								products.sku,
								products.imei as imei,
								products.date_of_qc,
								products.lstatus as status,
								products.location as location,
								detail_loc_log.old_location,					
								operationcenters.name as old_opcenter,
								compniesdata.name as client
								from products
								left outer join (SELECT products.id as table_id,max(detail_loc_log.insert_id) as max_insert from products 
												left outer join detail_loc_log on products.id = detail_loc_log.productid 
												left outer join location_update_log on detail_loc_log.insert_id = location_update_log.id 
												where location_update_log.status = 1 
												group by products.id) as m_table on products.id = m_table.table_id
								left outer join detail_loc_log on products.id = detail_loc_log.productid and m_table.max_insert = detail_loc_log.insert_id
								left outer join brands on products.manufacture_id = brands.id 
								left outer join colors on products.color_id = colors.id
								left outer join operationcenters on detail_loc_log.old_opcenter = operationcenters.id
								left outer join compniesdata on products.client_id = compniesdata.id
								where products.lstatus = 'Out for Repair' and products.location = 's_center'
								order by brand, model";	
							
					$query1 = $this->db->query($sql1);
					array_push($tcount,$query1->num_rows);
										
					if(($query1->num_rows)>0)
					{
							$csv1 =  $this->dbutil->csv_from_result($query1,$delimiter,$newline);
							//echo "".$csv5."<br>";
							
								if ( !write_file("./uploads/Out for Repairstock-".date("d-m-Y").".csv", $csv1)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 	
							}
							
					$result =$query1->result_array();
									 $a=0;$b=0;$c=0;
									 foreach ($result as $value)
										 { 
											if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
												 { $a++ ;
														}
											elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2' )
												{ $b++ ;	
													}
											else 
												{ $c++ ;}
										 }
									array_push($acount,$a);	
									array_push($bcount,$b);
									array_push($ccount,$c);
																
					
								
									
																
											
						
						$sql2="SELECT brands.name as brand,
								products.product_name as prod_name,
								products.model_number as model,
								colors.name as color,
								products.sku,
								products.imei as imei,
								products.date_of_qc,
								products.lstatus as status,
								products.location as location,
								detail_loc_log.old_location,					
								operationcenters.name as old_opcenter,
								compniesdata.name as client
								from products
								left outer join (SELECT products.id as table_id,max(detail_loc_log.insert_id) as max_insert from products 
												left outer join detail_loc_log on products.id = detail_loc_log.productid 
												left outer join location_update_log on detail_loc_log.insert_id = location_update_log.id 
												where location_update_log.status = 1 
												group by products.id) as m_table on products.id = m_table.table_id
								left outer join detail_loc_log on products.id = detail_loc_log.productid and m_table.max_insert = detail_loc_log.insert_id
								left outer join brands on products.manufacture_id = brands.id 
								left outer join colors on products.color_id = colors.id
								left outer join operationcenters on detail_loc_log.old_opcenter = operationcenters.id
								left outer join compniesdata on products.client_id = compniesdata.id
								where products.location = 'transit'
								order by brand, model";
							
					$query2 = $this->db->query($sql2);
					array_push($tcount,$query2->num_rows);
					
					if(($query2->num_rows)>0)
					{
							$csv2 =  $this->dbutil->csv_from_result($query2,$delimiter,$newline);
														
								if ( !write_file("./uploads/InTransitstock-".date("d-m-Y").".csv", $csv2)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 	
							}
							
					$result =$query2->result_array();
									 $a=0;$b=0;$c=0;
									 foreach ($result as $value)
										 { 
											if ($value['old_opcenter'] == 'Kalkaji' or $value['old_opcenter'] == 'Barthal' )	
												 { $a++ ;
														}
											elseif ($value['old_opcenter'] == 'BLR1' or $value['old_opcenter'] == 'BLR2' )
												{ $b++ ;	
													}
											else 
												{ $c++ ;}
										 }
									array_push($acount,$a);	
									array_push($bcount,$b);
									array_push($ccount,$c);
									
						
													
					
						$sql3 = "SELECT brands.name as brand,
							products.product_name as prod_name,
							products.model_number as model,
							colors.name as color,
							products.sku,
							products.imei as imei,
							products.date_of_qc,
							products.lstatus as cur_status,
							products.location as location,
							operationcenters.name as center,
							compniesdata.name as client
							from products
							left outer join brands on products.manufacture_id = brands.id 
							left outer join colors on products.color_id = colors.id
							left outer join operationcenters on products.op_center = operationcenters.id
							left outer join compniesdata on products.client_id = compniesdata.id
							where (products.lstatus = 'Pending QC' and products.location = 's_center') or
							(products.lstatus = 'Out for Repair' and products.location = 'over_warehouse') or
							(products.lstatus = 'Out for Repair' and products.location = 'cw')or
							(products.lstatus = 'Under QC' and products.location = 's_center') or
							(products.lstatus = 'Pending Repair' and products.location = 's_center') or
							(products.lstatus = 'Manager\'s Escalation' and products.location = 's_center')
							order by brand, model";
							
					$query3 = $this->db->query($sql3);
					array_push($tcount,$query3->num_rows);
					
					if(($query3->num_rows)>0)
					{
							$csv3 =  $this->dbutil->csv_from_result($query3,$delimiter,$newline);
							//echo "".$csv3."<br>";
							
								if ( !write_file("./uploads/DataDiscrepanciesstock-".date("d-m-Y").".csv", $csv3)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 	
							}
							
					$result =$query3->result_array();
									 $a=0;$b=0;$c=0;
									 foreach ($result as $value)
										 { 
											if ($value['center'] == 'Kalkaji' or $value['center'] == 'Barthal' )	
												 { $a++ ;
														}
											elseif ($value['center'] == 'BLR1' or $value['center'] == 'BLR2' )
												{ $b++ ;	
													}
											else 
												{$c++ ;}
										 }
									array_push($acount,$a);	
									array_push($bcount,$b);
									array_push($ccount,$c);
								
														
										
									
										
							
							
					$sql4 = "SELECT brands.name as brand,
							products.product_name as prod_name,
							products.model_number as model,
							colors.name as color,
							products.sku,
							products.imei as imei,
							products.date_of_qc,
							products.lstatus as cur_status,
							products.location as location,
							operationcenters.name as center,
							compniesdata.name as client
							from products
							left outer join brands on products.manufacture_id = brands.id 
							left outer join colors on products.color_id = colors.id
							left outer join operationcenters on products.op_center = operationcenters.id
							left outer join compniesdata on products.client_id = compniesdata.id
							where (products.location = 'over_werehouse' and products.lstatus = 'Pending QC' and products.date_of_qc < date_sub(now(),interval 24 hour) ) or
							(products.location = 'over_werehouse' and products.lstatus = 'Under QC' and products.date_of_qc < date_sub(now(),interval 48 hour) ) or  
							(products.location = 'over_werehouse' and products.lstatus = 'Manager\'s Escalation' and products.date_of_qc < date_sub(now(),interval 48 hour)) or
							(products.location = 'over_werehouse' and products.lstatus = 'Pending Repair' and products.date_of_qc < date_sub(now(),interval 48 hour))
							order by brand, model";	
					
					$query4 = $this->db->query($sql4);
					
					
					if(($query4->num_rows)>0)
					{
							$csv4 =  $this->dbutil->csv_from_result($query4,$delimiter,$newline);
														
								if ( !write_file("./uploads/DelayedQCstock-".date("d-m-Y").".csv", $csv4)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 
							}
							
					

					
							
					$sql6 = "SELECT brands.name as brand,
							products.product_name as prod_name,
							products.model_number as model,
							colors.name as color,
							products.sku,
							products.imei as imei,
							products.date_of_qc,
							products.lstatus as cur_status,
							products.location as location,
							operationcenters.name as center,
							compniesdata.name as client
							from products
							left outer join brands on products.manufacture_id = brands.id 
							left outer join colors on products.color_id = colors.id
							left outer join operationcenters on products.op_center = operationcenters.id
							left outer join compniesdata on products.client_id = compniesdata.id
							where products.location = 'cw'
							order by brand, model";
							
					$query6 = $this->db->query($sql6);
					
					
					if(($query6->num_rows)>0)
					{
							$csv6 =  $this->dbutil->csv_from_result($query6,$delimiter,$newline);
														
								if ( !write_file("./uploads/cw_stock-".date("d-m-Y").".csv", $csv6)){
									echo '<br>Unable to write the file<br><br>';}
								else{
									echo '<br>File written!<br><br>';} 	
							}
					
					
					$count = array_merge($acount, array_merge($bcount,array_merge($ccount,$tcount)));	
					return $count;										
			}	
			
	}
