<?php
 class Currency{
   public function form(){
     $zar_list = $this->getCurrencyList(0, "ONCHANGE='convertRandToForeign(this.form)'");
     $foreign_list = $this->getCurrencyList(0, "ONCHANGE='convertForeignToRand(this.form)'");
     $string = "<BR/><BR/><BR/>
	 <TABLE CELLPADDING='4' CELLSPACING='4'>
	  <TR>
	   <TD><A HREF='javascript:void();' ONCLICK='switchTabs(\"from_zar_container\",\"to_zar_container\");'>Convert ZAR to Foreign</A></TD>
	   <TD><A HREF='javascript:void();' ONCLICK='switchTabs(\"to_zar_container\",\"from_zar_container\");'>Convert Foreign to ZAR</A></TD>
	   <TD></TD>
	  </TR>
	 </TABLE>
	 <DIV ID='order_response' STYLE='display:none;'></DIV>
	 <DIV ID='from_zar_container'><FORM METHOD='post' ID='buy_currency_form_from_zar'>
	  <TABLE>
	   <TR>
	    <TD>ZAR</TD>
	    <TD><INPUT TYPE='text' ONCHANGE='convertRandToForeign(this.form)' VALUE='0' NAME='zar_value' onkeyup='javascript:this.value = this.value.replace(/[^0-9]/g,\"\");'></TD>
	   </TR>
	   <TR>
	    <TD>Convert To</TD>
	    <TD>{$zar_list}</TD>
	   </TR>
	   <TR>
	    <TD>Foreign Currency Value</TD>
	    <TD><DIV ID='foreign_currency_div'>0</DIV></TD>
	   </TR>
	   <TR>
	    <TD COLSPAN='2'><INPUT TYPE='button' ONCLICK='confirmSale(this.form)' VALUE='Purchase Foreign Currency'></TD>
	   </TR>
	  </TABLE>
	  <INPUT TYPE='hidden' VALUE='0' NAME='foreign_value' ID='foreign_value'>
	  </FORM>
	  </DIV>
	  
	  <DIV ID='to_zar_container' STYLE='display:none;'><FORM METHOD='post' ID='buy_currency_form_to_zar'>
	  <TABLE>
	   <TR>
	    <TD>Amount To Buy</TD>
	    <TD><INPUT TYPE='text' ONCHANGE='convertForeignToRand(this.form)' VALUE='0' NAME='foreign_value' onkeyup='javascript:this.value = this.value.replace(/[^0-9]/g,\"\");'></TD>
	   </TR>
	   <TR>
	    <TD>Foreign Current</TD>
	    <TD>{$foreign_list}</TD>
	   </TR>
	   <TR>
	    <TD>ZAR Value</TD>
	    <TD><DIV ID='zar_currency_div'>0</DIV></TD>
	   </TR>
	   <TR>
	    <TD COLSPAN='2'><INPUT TYPE='button' ONCLICK='confirmSale(this.form)' VALUE='Purchase Foreign Currency'></TD>
	   </TR>
	  </TABLE>
	  <INPUT TYPE='hidden' VALUE='0' NAME='zar_value' ID='zar_value'>
	  </FORM>
	  </DIV>
	  <SCRIPT>
	   function confirmSale(form_object){
	     if(!confirm('Are you sure you want to proceed?')){
		   return false;
		 }
		 
		 var zar_value = form_object.zar_value.value;
		 var currency_id = form_object.currency_id.value;
		 foreign_value = form_object.foreign_value.value;
		 
		 $.ajax({
		  url: 'index.php',
		  method: 'POST',
		  data: 'module=currency&action=placeorder&no_header=1&currency_id=' + currency_id + '&zar_amount=' + zar_value + '&foreign_value=' + foreign_value,
		  success: function(order_confirmation){
			$('#from_zar_container').attr('style', 'display:none');
			$('#to_zar_container').attr('style', 'display:none;');
			$('#order_response').attr('style', 'display:block;');
			$('#order_response').html(order_confirmation);
		  }
		 });		 
		 
	   }
	   
	   function switchTabs(show_tab, hide_tab){
		$('#'+show_tab).attr('style', 'display:block');
		$('#'+hide_tab).attr('style', 'display:none;');
		$('#order_response').attr('style', 'display:none;');
	   }

	  
	  
	   function convertRandToForeign(form_object){
	    var rand_value = form_object.zar_value.value;
	    if(rand_value==0){
		 $('#foreign_currency_div').html('0');
		 return true;
		}
		
		var currency_id = form_object.currency_id.value;
	    if(currency_id==0){
		 $('#foreign_currency_div').html('0');
		 return true;
		}
		$.ajax({
		  url: 'index.php',
		  method: 'POST',
		  data: 'module=currency&action=convertto&no_header=1&currency_id=' + currency_id + '&amount=' + rand_value,
		  success: function(foreign_value){
			 $('#foreign_currency_div').html(foreign_value);
			 $('#foreign_value').attr('value', foreign_value);
		  }
		 });
	   }
	   
	   function convertForeignToRand(form_object){
	    var foreign_value = form_object.foreign_value.value;
	    if(foreign_value==0){
		 $('#foreign_currency_div').html('00');
		 return true;
		}
		
		var currency_id = form_object.currency_id.value;
	    if(currency_id==0){
		 $('#foreign_currency_div').html('00');
		 return true;
		}
		$.ajax({
		  url: 'index.php',
		  method: 'POST',
		  data: 'module=currency&action=convertfrom&no_header=1&currency_id=' + currency_id + '&amount=' + foreign_value,
		  success: function(zar_value){
			 $('#zar_currency_div').html(zar_value);
			 $('#zar_value').attr('value', zar_value);
		  }
		 });
	   }
	  </SCRIPT>";
	 return $string;
   }
   
   public function convertFrom(){
	$currency_id = $_REQUEST['currency_id'];
	$amount = $_REQUEST['amount'];
	$exchange_configs = $this->getLatestRates($currency_id);
	$foreign_amount = number_format(($amount / $exchange_configs['exchange_rate']), 2, '.', '');
	return $foreign_amount;
   }
   
   public function convertTo(){
	$currency_id = $_REQUEST['currency_id'];
	$amount = $_REQUEST['amount'];
	$exchange_configs = $this->getLatestRates($currency_id);
	$zar_amount = number_format(($amount * $exchange_configs['exchange_rate']), 2, '.', '');
	return $zar_amount;
   }
   
   function placeOrder(){
	extract($_REQUEST);
	$settings = $this->getCurrencySettings($currency_id);
	$surcharge_percent = $settings['surcharge'];
	$discount = $settings['discount'];
	$exchange_rate = $settings['exchange_rate'];
	$surcharge_amount = $zar_amount * $surcharge_percent / 100;
	$surcharge_amount = number_format($surcharge_amount, 2, '.', '');
	$discount = $settings['discount'];
	$sql = "INSERT INTO `mukuru`.`orders` (`user_id`, `currency_id`, `exchange_rate`, `order_total_amount`, `order_foreign_currency_total_amount`, `order_surcharge_percent`, `order_surcharge_amount`, `order_discount_percent`,`created`) 
	  VALUES ('{$_SESSION['user_id']}', '{$currency_id}', '{$exchange_rate}', '{$zar_amount}', '{$foreign_value}', '{$surcharge_percent}', '{$surcharge_amount}', '{$discount}', CURRENT_TIMESTAMP);";
	mysql_query($sql);
	$order_id = mysql_insert_id();
	
	$sql = "SELECT * FROM currency_notifications WHERE currency_id='{$currency_id}'";
	$res = mysql_query($sql);
	if(mysql_num_rows($res)>0){
	  $list_array = array();
	  while($row = mysql_fetch_assoc($res)){
	   $list[$row['mail_to']] = $row['mail_to'];
	  }
	  $mail_from = 'keorapetseb@gmail.com';
	  $headers  = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: '. $mail_from . "\r\n";
	  $send_to = join($list, ',');
	  $message_body = "New Order: {$order_id}<br/>
	   Foreign Value: {$foreign_value}<br/>Foreign Currency {{$settings['description']} ({$settings['code']})}<br/>
	   Order Amount: {$zar_amount}<br/>Surcharge @ {$surcharge_percent} % : {$surcharge_amount}";
	  mail($send_to, "Order Confirmation: {$order_id}", $message_body, $headers, '-f'.$mail_from);
	}
	
	return "<DIV><IMG SRC='images/tick.png'> Your order have been placed.<BR/>
	 Order #: {$order_id}</DIV>";
   }
   
   public function getLatestRates($id=0){
	$sql = "SELECT c.*,minute(timediff(updated,now()) ) last_update FROM currencies c WHERE id='{$id}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if((int)$row['last_update']<=5){ // check if last update was in the last 5 minutes
	  return $row;
	}
	$currency_code = $row['code'];

	$crl = curl_init();
	$timeout = 10;
	curl_setopt ($crl, CURLOPT_URL, API_URL);
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$json_rates = curl_exec($crl);
	curl_close($crl);

	$array_rates = json_decode($json_rates, true);
	$exchange_rates = $array_rates['quotes'];
	extract($exchange_rates);

 # Covert Rates to use ZAR as Source.	
	$usd_rate = $USDUSD / $USDZAR;
	$gbp_rate = $usd_rate * $USDGBP;
	$eur_rate = $usd_rate * $USDEUR;
	$kes_rate = $usd_rate * $USDKES;

	$converted = array('USD'=>$usd_rate, 'GBP'=>$gbp_rate, 'EUR'=>$eur_rate, 'KES'=>$kes_rate);
	$current_rate = array('code'=>$currency_code, 'exchange_rate'=>$converted[$currency_code], 
		'discount'=>$row['discount'], 'surcharge'=>$row['surcharge']);
	
	foreach($converted as $code=>$exchange_rate){
	  $sql = "UPDATE currencies SET exchange_rate='{$exchange_rate}' WHERE code='{$code}'";
	  mysql_query($sql);
	}
	return $current_rate;
   }
   
   public function getCurrencySettings($id=0){
	$sql = "SELECT * FROM currencies WHERE id='{$id}'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	return $row;
   }
   
   public function getCurrencyList($currency_id=0, $extras=''){
	$sql = "SELECT * FROM currencies ORDER BY description";
	$res = mysql_query($sql);
	$list = "<SELECT NAME='currency_id' {$extras}><OPTION VALUE='0'>--Select--</OPTION>";
	while($row = mysql_fetch_assoc($res)){
	 $list .= "<OPTION VALUE='{$row['id']}'>{$row['description']} ({$row['code']})</OPTION>";
	}
	$list .= "</SELECT>";
	return $list; 
   }
 
 }
?>