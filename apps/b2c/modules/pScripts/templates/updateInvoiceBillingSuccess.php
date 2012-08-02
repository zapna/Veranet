<?php
	use_helper('Number');
	ob_start(); 
	$totalcost = 0.00;
	$totalSubFee = 0.00;
	$totalPayments = 0.00;
	$totalEventFee = 0.00;
    //$call_rate_table = CallRateTablePeer::doSelect(new Criteria());
?>
<html>
    <head>
        <title><?php echo date('dmy') . $invoice_meta->getId() ?>- <?php echo $company_meta->getName() ?></title>
        <style>
            fieldset {
                 -moz-border-radius:10px;
                 border-radius: 10px;
                 -webkit-border-radius: 10px;
                 border: 2px solid #000;
             }
             .border{
                border-bottom: 1px solid #000 !important;
                border-top:1px solid #000 !important;
             }
			 .borderleft{
			 	border-left: 1px solid #000 !important;
			 }
			 .borderright{
			 	border-right:1px solid #000 !important;
			 }
             .padding{
                padding-top:10px;
                padding-bottom:10px;
				padding-left:5px;
             }
			 .padbot{
			 	padding-bottom:10px;
			 }
			 .trbg{
			 	font-weight:bold;  
				background-color:#CCCCCC; 
			 }
			 .table{
			 	padding-top:30px; 
			 }
			 .table td{
			 	padding-left:5px;
				padding-top:5px;
			 }
			 table td{
			 	border:none!important;
			 }
			 h2{ color:#000!important;}
        </style>
    </head>
    <body>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:30px">
	<tr>
		<td colspan="2">
			<fieldset>
			<table width="100%">
				<tr>
					<td> <?php echo image_tag(sfConfig::get('app_web_url').'images/veranet-log.jpg',array('width' => '300'));?></td>
					<td>
						<b>DRUSTVO ZA TELEKOMUNIKACIJE Telecom "VERAT" D.O.O.</b><br />
						Bulevar Vojvode Misica 37<br />
						11000 Beograd<br />
						Republika Srbija
					</td>
					<td>
						PIB: 100221009<br />
						Maticini broj: 06901093<br />
						Registarski broj: SR135301811<br />
						Sifra delatnosti: 6110
					</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	
	<tr>
		<td width="50%">
			<table width="60%" border="0" style="padding-top:30px">
				<tr>
					<td>Invoice Number:</td>
					<td><?php echo $invoice_meta->getId(); ?></td>
				</tr>
				<tr>
					<td>Invoice Period:</td>
					<td><?php echo $invoice_meta->getBillingStartingDate('d M.') . ' - ' . $invoice_meta->getBillingEndingDate('d M.') ?></td>
				</tr>
				<tr>
					<td>Invoice Date:</td>
					<td><?php echo $invoice_meta->getCreatedAt('d M. Y') ?></td>
				</tr>
				<tr>
					<td>Due date:</td>
					<td><?php echo $invoice_meta->getDueDate('d M. Y') ?></td>
				</tr>
				<tr>
					<td>Customer Number:</td>
					<td><?php echo $company_meta->getVatNo() ?></td>
				</tr>
				
			</table>
		</td>
		<td width="50%" align="right">
			<table width="50%" border="0" cellpadding="0" cellspacing="0">
				<tr style="background-color:#CCCCCC;">
					<td colspan="2" style="padding:5px">
						<?php echo $company_meta->getName() ?><br />
                    	<?php echo $company_meta->getAddress() ?><br />
                    	<?php echo $company_meta->getPostCode() ?>,
                    	<?php echo $company_meta->getCity() ?><br />
	    				Att: <?php echo $company_meta->getContactName() ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" class="table">
				<tr><td colspan="3" class="padbot"><h2>Invoice Call</h2></td></tr>
				<tr height="40px" class="trbg">
					<td class="border borderleft">Calls</td>
					<td class="border">Destination</td>
					<td class="border">Duration</td>
					<td class="border borderright" >Charged Amount (<?php echo sfConfig::get('app_currency_code')?>)</td>
				</tr>
			<?php
				$billings = array();
				$ratings = array();
				$bilcharge = 00.00;
				$invoiceFlag = false;
				$count = 1;
				$billing_details = array();
				foreach ($employees as $employee) {                            
					$subFlag = false;
					$regFlag = false;
					$billingFlag = false;
					
					$prdPrice = 0;
					$startdate = strtotime($billing_start_date);
					$enddate = strtotime($billing_end_date);

					$bc = new Criteria();
					$bc->add(EmployeeCustomerCallhistoryPeer::PARENT_TABLE, "employee");
					$bc->addAnd(EmployeeCustomerCallhistoryPeer::PARENT_ID, $employee->getId());
					$bc->addAnd(EmployeeCustomerCallhistoryPeer::CONNECT_TIME, " connect_time > '" . $billing_start_date . "' ", Criteria::CUSTOM);
					$bc->addAnd(EmployeeCustomerCallhistoryPeer::DISCONNECT_TIME, " disconnect_time < '" . $billing_end_date . "' ", Criteria::CUSTOM);
					$bc->addGroupByColumn(EmployeeCustomerCallhistoryPeer::COUNTRY_ID);
					if (EmployeeCustomerCallhistoryPeer::doCount($bc) > 0) {
						$billingFlag = true;
					}
					if ($billingFlag) {
			?>
				<tr>
					<td colspan="4" class="padding"><strong><?php echo 'From Number: ' . $employee->getMobileNumber() ?></strong></td>
				</tr>
			<?php
				$invoiceFlag = true;
				$billings = EmployeeCustomerCallhistoryPeer::doSelect($bc);
				foreach ($billings as $billing) {?>
				<tr>
					<td><?php echo $billing->getCountry()->getName()//.'-'.$billing->getCountryId(); ?></td>
					<td><?php echo $billing->getPhoneNumber(); ?></td>
						<td>
							<?php
								$dc = new Criteria();
								$dc->add(EmployeeCustomerCallhistoryPeer::PARENT_TABLE, "employee");
								$dc->add(EmployeeCustomerCallhistoryPeer::PARENT_ID, $employee->getId());
								$dc->add(EmployeeCustomerCallhistoryPeer::COUNTRY_ID,$billing->getCountryId());
								$dc->addAnd(EmployeeCustomerCallhistoryPeer::CONNECT_TIME, " connect_time > '" . $invoice_meta->getBillingStartingDate('Y-m-d 00:00:00') . "' ", Criteria::CUSTOM);
								$dc->addAnd(EmployeeCustomerCallhistoryPeer::DISCONNECT_TIME, " disconnect_time  < '" . $invoice_meta->getBillingEndingDate('Y-m-d 23:59:59') . "' ", Criteria::CUSTOM);
							  //  $dc->addGroupByColumn(EmployeeCallhistoryPeer::COUNTRY_ID);
								$temp = EmployeeCustomerCallhistoryPeer::doSelect($dc);
								$minutes_count = 0;
								$calculated_cost = 0;
								foreach ($temp as $t) {
									$calculated_cost += $t->getChargedAmount();
									$call_duration = EmployeeCustomerCallhistoryPeer::getTotalCallDuration($employee, $billing->getCountryId());
								}
							?>
							<?php echo $call_duration ?>
						</td>
						<td>
							<?php echo  number_format($calculated_cost, 2) ;
							$temp_cost = $calculated_cost;
							?>
						</td>
					</tr>
					<?php  $totalcost += $temp_cost;
			}
		 }
	}  
	$invoice_cost = ($invoiceFlag) ? $invoice_cost : '0.00'; ?>  
	</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" class="table">
				<tr><td colspan="3" class="padbot"><h2>Subscriptions</h2></td></tr>
				<tr  class="trbg" height="40px">
					<td class="border borderleft">Mobile Number</td>
					<td class="border">Description</td>
					<td class="border borderright">Amount (<?php echo sfConfig::get('app_currency_code')?>)</td>
				</tr>
			   <?php
				 foreach ($employees as $emps) { 
				  $cSub = new Criteria();
				  $cSub->add(OdrsPeer::PARENT_TABLE,'employee');
				  $cSub->addAnd(OdrsPeer::PARENT_ID,$emps->getId());
				  $cSub->addAnd(OdrsPeer::I_SERVICE,4);
				  $scount = OdrsPeer::doCount($cSub);
				  if($scount > 0){
				   	$subscriptions =  OdrsPeer::doSelect($cSub);
    				foreach($subscriptions as $subs){
				?>
				<tr>
				   <td><?php echo $emps->getMobileNumber();?></td>
				   <td><?php echo $subs->getDescription();?></td>
				   <td><?php echo number_format($subs->getChargedAmount(),2);$totalSubFee += $subs->getChargedAmount();?></td>
				</tr>  
				<?php      
					}
				  }
				 } //end employee second foreach loop   
			    ?>   
			</table>
		</td>
	</tr>
	<?php  if(isset ($otherevents) && $otherevents !=""){ ?>			
	<tr>
		<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" class="table" style="padding-bottom:30px;">
				<tr><td colspan="2" class="padbot"><h2>Other Events</h2></td></tr>
				<tr class="trbg" height="40px">
					<td class="border borderleft">Description</td>
					<td class="border borderright">Amount (<?php echo sfConfig::get('app_currency_code')?>)</td>
				</tr>
                <?php foreach($otherevents as $event){?>
				<tr>
                    <td><?php echo $event->getDescription();?></td>
                    <td><?php echo number_format($event->getChargedAmount(),2);$totalEventFee += $event->getChargedAmount();?></td>
                </tr>  
				<?php }?>
			</table>
		</td>
	</tr>
   <?php        
	  }
	   $totalcost = $totalcost + $totalSubFee + $totalEventFee;
   ?>
   <tr height="30px">
		<td colspan="2" class="border borderleft borderright" style="background-color:#CCCCCC;">&nbsp;</td>
	</tr>
	<tr>
		<td class="padding"><strong>Total cost</strong></td>
		<td><?php echo number_format($totalcost, 2) ?></td>
	</tr>
	<tr>
		<td class="padding"><strong>Invoice Cost</strong></td>
		<td><?php echo number_format($invoice_cost, 2) ?></td>
	</tr>
	<tr>
		<td colspan="2" ><hr /></td>
	</tr>
	<tr>
		<td class="padding"><strong>Total Inc. invoice cost</strong></td>
		<td><?php echo number_format($net_cost = $totalcost + $invoice_cost, 2); ?></td>
	</tr>
	<tr>
		<td class="padding"><strong>Vat</strong></td>
		<td><?php echo number_format($moms = $net_cost * sfConfig::get("app_vat_percentage"), 2); ?></td>
	</tr>
	<tr>
		<td colspan="2" ><hr /></td>
	</tr>
	<tr>
		<td class="padding"><strong>Total Inc. Vat</strong></td>
		<td>
			<?php echo number_format($net_cost = $net_cost + $moms, 2);echo "&nbsp;".sfConfig::get('app_currency_code');
                  util::saveTotalPayment($invoice_meta->getId(),$net_cost); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2" ><hr /></td>
	</tr>
	<tr>
		<td class="padding"><strong>Previous Balance</strong></td>
		<td><?php echo number_format($netbalance , 2);  ?></td>
	</tr>
	<tr>
		<td class="padding"><strong>Total Payable Balance</strong></td>
		<td><?php echo number_format($net_cost + $netbalance , 2);  ?></td>
	</tr>
	<tr height="30px">
		<td colspan="2" class="border borderleft borderright" style="background-color:#CCCCCC;">&nbsp;</td>
	</tr>
	<?php if(isset ($payments) && $payments !=""){ ?>
  	<tr>
		<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" class="table">
				<tr><td colspan="2" class="padbot"><h2>Payment History</h2></td></tr>
				<tr class="trbg" height="40px">
					<td class="border borderleft">Description</td>
					<td class="border">Airtime (<?php echo sfConfig::get('app_currency_code')?>)</td>
					<td class="border">Vat (<?php echo sfConfig::get('app_currency_code')?>)</td>
					<td  class="border borderright">Total (<?php echo sfConfig::get('app_currency_code')?>)</td>
				</tr>
				<?php foreach($payments as $payment){?>
				<tr>
				   <td><?php echo $payment->getDescription();?></td>
				   <td><?php echo number_format($chargedAmount = $payment->getChargedAmount(),2);$totalPayments += $payment->getVatIncludedAmount();?></td>
				   <td><?php echo number_format($vat = $payment->getVatIncludedAmount()-$chargedAmount,2);?></td>
				   <td><?php echo number_format($payment->getVatIncludedAmount(),2);?></td>
				</tr>  
				<?php }?>
			</table>
		</td>
	</tr>
	<?php } ?>			
	<?php if(isset ($preInvoices) && $preInvoices !=""){?>
	<tr>
		<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" class="table" style="padding-bottom:30px">
				<tr><td colspan="2" class="padbot"><h2>Previous Invoices</h2></td></tr>
				<tr height="40px" class="trbg">
					<td class="border borderleft">Bill Duration</td>
					<td class="border borderright">Invoice Total (<?php echo sfConfig::get('app_currency_code')?>)</td>
				</tr>
                <?php foreach($preInvoices as $preInvoice){ ?>
				<tr>
				   <td><?php echo $preInvoice->getBillingStartingDate("M d");?> - <?php echo $preInvoice->getBillingEndingDate("M d");?></td>
				   <td><?php echo number_format($preInvoice->getTotalPayment(),2);?></td>
                </tr>  
                <?php  }?>
			</table>
		</td>
	</tr>	
	 <?php }?> 
	 <tr>
		<td colspan="2">
			<fieldset>
			<table width="100%">
				<tr>
					<td>Tel: XXXXXXXXXXXXXXXX</td>
					<td>Fax: XXXXXXXXXXXXXXX</td>
					<td>Email: veranet@zerocall.com</td>
					<td>Web: www,veranet.com</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
</body>
</html>			
<?php
	$html_content = ob_get_contents();
	util::saveHtmlToInvoice($invoice_meta->getId(), $html_content);
	$ci = new Criteria();
	$ci->add(InvoicePeer::ID,$invoice_meta->getId());
	$in = InvoicePeer::doSelectOne($ci);
	$in->setSubscriptionFee($totalSubFee);
	$in->setRegistrationFee($totalEventFee);
	$in->setPaymentHistoryTotal($totalPayments);
	$in->setMoms($moms);
	$in->setTotalusage($totalcost);
	$in->setCurrentBill($net_cost);
	$in->setInvoiceCost($invoice_cost);
	$in->setNetPayment($net_cost);
	$in->setInvoiceStatusId(1);
	$in->save();
	
	$fileName = str_replace("/", "_", $in->getCompany()->getName());
	$fileName = str_replace(" ", "_", $fileName);
	
	$fileName = $in->getId().'-'.$fileName;

	util::html2pdf($invoice_meta->getId(),$html_content,$fileName);
?>
