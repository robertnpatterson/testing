<?php


# time array 
global $days ,$hours ,$ampm,$timearray,$login_url;
$login_url = "http://26-life.com/login/";
$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$hours =  array('01','02','03','04','05','06','07','08','09','10','11','12');
$minuts = array("00","30");
$ampm = array('AM',"PM");
$timearray = array(
		'08:00'=>'8AM',
		'09:00'=>'9AM',
		'10:00'=>'10AM',
		'11:00'=>'11AM',
		'12:00'=>'12PM',
		'13:00'=>'1PM',
		'14:00'=>'2PM',
		'15:00'=>'3PM',
		'16:00'=>'4PM',
		'17:00'=>'5PM',
		'18:00'=>'6PM',
		'19:00'=>'7PM',
		'20:00'=>'8PM',
);
	
	
//make array to optoin
function arrtooption($array)
{
	if(is_array($array))
	{
		$option = '';
		foreach($array as $hour) {
			$option .="<option value='{$hour}'>{$hour}</option>";
		}
		return $option;
	}
}

function make_weekdropdown()
{
	global $days;
	global $hours;
	global $minuts;
	global $ampm;
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; hours minute <br />";
	foreach($days as $day)
	{
		
		echo "$day : ";
		$daytolower = strtolower($day);
		$nameh = "$daytolower".'h';
		$namem = "$daytolower".'m';
		$nameampm = "$daytolower".'ampm';
		echo "	<select name='$nameh' id='$nameh'>
						".arrtooption($hours)."
				</select>
				<select name='$namem' id='$namem'>
					".arrtooption($minuts)."
				</select>
				<select name='$nameampm' id='$nameampm'>
					".arrtooption($ampm)."
				</select> <br />";
	}
	
}

// make insert query for timing

function insert_client_order_timing($orderid)
{
	$table = "client_order_time";
	$insquery ='';
	$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	//geting days values
	foreach($days as $day)
	{	
		$daytolower = strtolower($day);
		
		$nameh = "$daytolower".'h';
		$namem = "$daytolower".'m';
		$nameampm = "$daytolower".'ampm';
		// getting values from request
		$valueh = $_REQUEST["$daytolower".'h'];
		$valuem = $_REQUEST["$daytolower".'m'];
		$valueampm = $_REQUEST["$daytolower".'ampm'];
		
		$dayvalues[$daytolower] = "$valueh:$valuem:$valueampm";
	}
	
	$order_date = $_REQUEST["order_date"];
	$client_id = $_REQUEST["client"];
	$insquery = "INSERT INTO $table SET
		`order_id` = '$orderid',
		`order_date` = '$order_date',
		`client_id` = '$client_id',
		`sunday_time` = '{$dayvalues['sunday']}',
		`monday_time` = '{$dayvalues['monday']}',
		`tuesday_time` = '{$dayvalues['tuesday']}',
		`wednesday_time` = '{$dayvalues['wednesday']}',
		`thursday_time` = '{$dayvalues['thursday']}',
		`friday_time` = '{$dayvalues['friday']}',
		`saturday_time` = '{$dayvalues['saturday']}'
	";
		
		
	
	$insquery;
	mysql_query($insquery) or die(mysql_error());
}


/*
* fetching table fields
*
**/
 
function getColoumn($table) { 
     $result = mysql_query("SHOW COLUMNS FROM ". $table); 
      if (!$result) { 
        echo 'Could not run query: ' . mysql_error(); 
      } 
      $fieldnames=array(); 
      if (mysql_num_rows($result) > 0) { 
        while ($row = mysql_fetch_assoc($result)) { 
          $fieldnames[] = $row['Field']; 
        } 
      } 

      return $fieldnames; 
} 


/**
 * Retrive current week 
 * string @prm  m/d/Y
 */
 function week_day($date){
  $ts = strtotime($date);
  // calculate the number of days since Monday
  $dow = date('w', $ts);
  $offset = $dow - 1;
  if ($offset < 0) {
   $offset = 6;
  }
  // calculate timestamp for the Monday
  $ts = $ts - $offset*86400;
  // loop from Monday till Sunday
  for ($i = 0; $i < 7; $i++, $ts += 86400){
   $week[] = date("Y-m-d", $ts);
  }
  return $week;
 }
 

 /**
 * Retrive days of current week after today
 * string @prm  m/d/Y as today date
 */
 function week_dayaftertoday($today){
  $ts = strtotime($today);
  $strtoday = strtotime($today);
  // calculate the number of days since Monday
  $dow = date('w', $ts);
  $offset = $dow - 1;
  if ($offset < 0) {
   $offset = 6;
  }
  // calculate timestamp for the Monday
  $ts = $ts - $offset*86400;
  // loop from Monday till Sunday
  for ($i = 0; $i < 7; $i++, $ts += 86400){
	// only for the day after today
	if($ts >= $strtoday)
	$week[] = date("Y-m-d", $ts);
  }
  return $week;
 }

/*
* Return next sevent date
*
**/

function next10date($date)
{
	$ts = strtotime($date);
	
	for ($i = 0; $i < 10; $i++, $ts += 86400){
	$week[] = date("Y-m-d", $ts);
	}
	return $week;
}




/*
* Print Schedule table
*
**/
function printclientSchedule($client_id=null,$disweek=true)
{
	//var_dump($disweek);die;
	// sheduling table here
	if(!empty($_REQUEST['q'])){
		$date = $_REQUEST['q'];
	}else{
		$date = date("Y-m-d");
	}
	$daysshsplay = week_dayaftertoday($date);
	/* echo "<pre>";
	print_r($daysshsplay); */
	if($client_id == null)
	{
		die("No client selected");
	}
		
	$buttons = array(
		'08:00'=>'8AM',
		'09:00'=>'9AM',
		'10:00'=>'10AM',
		'11:00'=>'11AM',
		'12:00'=>'12PM',
		'13:00'=>'1PM',
		'14:00'=>'2PM',
		'15:00'=>'3PM',
		'16:00'=>'4PM',
		'17:00'=>'5PM',
		'18:00'=>'6PM',
		'19:00'=>'7PM',
		'20:00'=>'8PM',
	);
	// style section
	$btnpad = "padding: 5px;";
	
	// table 
	$table ="";
	$table .= "
	<table>
		<tr>
			<td width='100'>
				<!--<b>Green available for drop</b>-->
			</td>
			<!--<td align='center' style='color:red' id='schedulmessage'>-->";
			$c =1;
			foreach($buttons as $key=>$text) {
				$c++; # increasing colom
				// table head
				$table .= "<td width='50' align='center'><span id='shbutton'>
					<input class='all' style='$btnpad background-color:green;' title='Select All' type='button' time='".$key."' clientid=".base64_encode($client_id)." value='ALL' onclick='s_c_allc(this,$c)' size='10' />
				</span><br />
				<span id='shbutton'>
					<input class='' style='$btnpad background-color:red;' title='Remove All' type='button' time='".$key."' clientid=".base64_encode($client_id)." value='Clear' onclick='r_c_allc(this,$c)' />
				</span>
				</td>";
				
			}
		$table	.="<!--</td>-->
		</tr>";
	$r = 1;
	
	foreach($daysshsplay as $day) {
		$r++; #increasing row
		$table .= "	<tr>
			
			<td width='100'>
				<b>
					".date("l , F dS",strtotime($day))."
				</b>
			</td>
			
			";
				$c = 1;
				foreach($buttons as $button=>$text) {
				$c++; # increasing colom
				// set active to already scheduled
				$sid = is_scheduled($client_id,$day,$button,"$day $button");
				if(!empty($sid)){
					$backgournd = "background-color:green;";
				}else{
					$backgournd = "";
				}
				
				$onclick = "onclick='s_c_available(this)'";
				
				//  check for if clent is booked for this time
				$asid = isclientbusy($client_id,$day,$button,"$day $button");
						
				if(!empty($asid)) // if client is busy
				{
					$backgournd = "background-color:yellow;";
					$onclick = "onclick='alert(\"Sechedule is booked for this time\")'";
				}
				
				$table .= "<td width='50'><span id='shbutton'>
					<input class='c$c scol $day ' style='{$btnpad}{$backgournd}' date='".$day."' time='".$button."' type='button' datetime='".$day." ".$button."' clientid=".base64_encode($client_id)." size='10' value='".$text."' $onclick />
				</span></td>";
				
				}
				
			$table .="
		</tr>";
		} 
		
		// next perivious calander
		if($disweek===true){
		$nxt8mon =next8monday(date('Y-m-d'));
		$table .= "<tr>
			<td colspan='14'>
				<div id='weeklist' style='float: left;text-align: center;width:100%;border:1px solid black;'>	
					<!--<h4>Week of Year</h4>-->";
			// print next weeks
			$w=0;
			foreach($nxt8mon as $week){
				$w++;
				$cw = date('m',strtotime($week));
				$monthtext = date('F',strtotime($week));
				//echo "<br />";
				if($w == 1){
					$pr = $cw;
					$table .= "<span style='float:left;width: 33%;'><h4>$monthtext</h4>";
				}
				if($pr < $cw){
					$table .= "</span>";
					$table .= "<span style='float:left;width: 33%;' ><h4>$monthtext</h4>";
				}
				$pr = $cw;
				// set anchor bg color
				$res = client_availableinweek($client_id,$week);
				if($res ){
					$acolor = "style='color:green'";
				}else{
					$acolor = "";
				}
				//echo "<br />";
				if($w==1){
					
					$table .= "<a $acolor href='javascript:void(0)' onclick='setpage(\"client_div\",\"client_drop_sch.php\",\"\",\"\",\"\")'>Schedule for current week </a>";
				}else{
					
					$table .= "<a $acolor href='javascript:void(0)' onclick='setpage(\"client_div\",\"client_drop_sch.php\",\"$week\",\"\",\"\")'>
					Schedule for ".date(" F dS",strtotime($week))."</a>";
				}
				$table .= "<br />"; // adding break to list
			}
			// scheduled 
			
			$table .= "</div>
				
			</td>
		</tr>";
		}
	$expboooking = bookingexplain();
	$table .= "<tr>
		<td colspan='14'>$expboooking</td>
	</tr>";
	$table .= "</table>";
	return $table;
}



/*
* This function is for new_re_sch_page.php or my used on re_sch_page.php
* 
*
**/

function reschedule_location_client_av($client_id=null,$disweek=true,$rescheduletext=false)
{
	//var_dump($disweek);die;
	// sheduling table here
	if(!empty($_REQUEST['date1'])){
		$date = $_REQUEST['date1'];
	}else{
		$date = date("Y-m-d");
	}
	$daysshsplay = week_dayaftertoday($date);
	/* echo "<pre>";
	print_r($daysshsplay); */
	if($client_id == null)
	{
		die("No client selected");
	}
		
	$buttons = array(
		'08:00'=>'8AM',
		'09:00'=>'9AM',
		'10:00'=>'10AM',
		'11:00'=>'11AM',
		'12:00'=>'12PM',
		'13:00'=>'1PM',
		'14:00'=>'2PM',
		'15:00'=>'3PM',
		'16:00'=>'4PM',
		'17:00'=>'5PM',
		'18:00'=>'6PM',
		'19:00'=>'7PM',
		'20:00'=>'8PM',
	);
	// style section
	$btnpad = "padding: 5px;";
	
	// table 
	$table ="";
	$table .= "
	<table>
		<tr>
			<td width='100'>
				<!--<b>Green available for drop</b>-->
			</td>
			<!--<td align='center' style='color:red' id='schedulmessage'>-->";
			$c =1;
			foreach($buttons as $key=>$text) {
				$c++; # increasing colom
				// table head
				$table .= "<td width='50' align='center'><span id='shbutton'>
					<input class='all' style='$btnpad background-color:green;' title='Select All' type='button' time='".$key."' clientid=".base64_encode($client_id)." value='ALL' onclick='s_c_allc(this,$c)' size='10' />
				</span><br />
				<span id='shbutton'>
					<input class='' style='$btnpad background-color:red;' title='Remove All' type='button' time='".$key."' clientid=".base64_encode($client_id)." value='Clear' onclick='r_c_allc(this,$c)' />
				</span>
				</td>";
				
			}
		$table	.="<!--</td>-->
		</tr>";
	$r = 1;
	
	foreach($daysshsplay as $day) {
		$r++; #increasing row
		$table .= "	<tr>
			
			<td width='100'>
				<b>
					".date("l , F dS",strtotime($day))."
				</b>
			</td>
			
			";
				$c = 1;
				foreach($buttons as $button=>$text) {
				$c++; # increasing colom
				// set active to already scheduled
				$sid = is_scheduled($client_id,$day,$button,"$day $button");
				if(!empty($sid)){
					$backgournd = "background-color:green;";
				}else{
					$backgournd = "";
				}
				
				$onclick = "onclick='s_c_available(this)'";
				
				//  check for if clent is booked for this time
				$asid = isclientbusy($client_id,$day,$button,"$day $button");
						
				if(!empty($asid)) // if client is busy
				{
					$backgournd = "background-color:yellow;";
					$onclick = "onclick='alert(\"Sechedule is booked for this time\")'";
				}
				
				$table .= "<td width='50'><span id='shbutton'>
					<input class='c$c scol $day ' style='{$btnpad}{$backgournd}' date='".$day."' time='".$button."' type='button' datetime='".$day." ".$button."' clientid=".base64_encode($client_id)." size='10' value='".$text."' $onclick />
				</span></td>";
				
				}
				
			$table .="
		</tr>";
		} 
		
		// next perivious calander
		if($disweek===true){
		$nxt8mon =next8monday(date('Y-m-d'));
		$table .= "<tr>
			<td colspan='14'>
				<div id='weeklist' style='float: left;text-align: center;width:100%;border:1px solid black;'>	
					<!--<h4>Week of Year</h4>-->";
			// print next weeks
			$w=0;
			foreach($nxt8mon as $week){
				$w++;
				$cw = date('m',strtotime($week));
				$monthtext = date('F',strtotime($week));
				//echo "<br />";
				if($w == 1){
					$pr = $cw;
					$table .= "<span style='float:left;width: 33%;'><h4>$monthtext</h4>";
				}
				if($pr < $cw){
					$table .= "</span>";
					$table .= "<span style='float:left;width: 33%;' ><h4>$monthtext</h4>";
				}
				$pr = $cw;
				// set anchor bg color
				$res = client_availableinweek($client_id,$week);
				if($res ){
					$acolor = "style='color:green'";
				}else{
					$acolor = "";
				}
				//echo "<br />";
				if($w==1){
					
					//$table .= "<a $acolor href='javascript:void(0)' onclick='setpage(\"client_div\",\"client_drop_sch.php\",\"\",\"\",\"\")'>Schedule for current week </a>";
					$table .= "<a $acolor href='javascript:void(0)' onclick='load_cal($client_id,\"\")'>Schedule for current week </a>";

				}else{
					
					$table .= "<a $acolor href='javascript:void(0)' onclick='load_cal($client_id,\"$week\")'>
					Schedule for ".date(" F dS",strtotime($week))."</a>";
				}
				$table .= "<br />"; // adding break to list
			}
			// scheduled 
			
			$table .= "</div>
				
			</td>
		</tr>";
		}
	$expboooking = bookingexplain($rescheduletext);
	$table .= "<tr>
		<td colspan='14'>$expboooking</td>
	</tr>";
	$table .= "</table>";
	return $table;
}





/*
* Print client availblity table
* $client id int
*
***/

function cleint_availabletable($client_id,$nextweek=false,$order_id=null,$funtype=null)
{
	//$data = clientschedule($client);
	//var_dump($nextweek);die;
	// sheduling table here
	if(!empty($_REQUEST['date'])){
		$date = $_REQUEST['date'];
	}else{
		$date = date("Y-m-d");
	}
	$daysshsplay = week_dayaftertoday($date);
	/* if($client_id != 0 && is_int($client_id) && !empty($client_id)){
		$onclicktd = "book_c_sechdule(this)";
	}else{
		$onclicktd = "alert(\'no client here\')";
	} */
	//print_r($array1);
	$reschuleatt ='';
	$bkdata ='';
	if($funtype == 'reschedule'){
		$onclicktd = "resh_c_sechdule(this)";
		$reschuleatt = "scheduleid ='$order_id' "; 
		$bkdata = get_booked($order_id);
		$monthweekid =  "myreschedule";
	}else{
		$onclicktd = "book_c_sechdule(this)";
		$monthweekid =  "clientbooking";
	}
	//echo $monthweekid;
	$buttons = array(
		'08:00'=>'8AM',
		'09:00'=>'9AM',
		'10:00'=>'10AM',
		'11:00'=>'11AM',
		'12:00'=>'12PM',
		'13:00'=>'1PM',
		'14:00'=>'2PM',
		'15:00'=>'3PM',
		'16:00'=>'4PM',
		'17:00'=>'5PM',
		'18:00'=>'6PM',
		'19:00'=>'7PM',
		'20:00'=>'8PM',
	);
	// style section
	$btnpad = "padding: 5px;";
	
	// table 
	$table ="";
	$table .= "
	<table width='785' >
		<tr>
			<td width='100'>
				<!--<b>Green available for drop</b>-->";
				if($bkdata){
					$xdata = "<div id='schmessage'><b color='red'>Previous time Setted : {$bkdata['date']} {$bkdata['time']}</b></div>";
				}
				$table .="
			</td>
			<td align='center' style='color:red;float:left;position:absolute' id='schedulmessage'>$xdata</td></tr>";
			
	$r = 1;
	$onclicktd1 = $onclicktd;
	foreach($daysshsplay as $day) {
		$r++; #increasing row
		$table .= "	<tr>
			
			<td width='100'>
				<b>
					".date("l , F dS",strtotime($day))."
				</b>
			</td>
			
			";
				$c = 1;
				foreach($buttons as $button=>$text) {
					$c++; # increasing colom
					// set active to already scheduled
					$sid = is_scheduled($client_id,$day,$button,"$day $button");
					if(!empty($sid)){ // if client available
						
						$backgournd = "background-color:green;";
						$asid = isclientbusy($client_id,$day,$button,"$day $button");
						
						if(!empty($asid)) // if client is busy
						{
							$backgournd = "background-color:yellow;";
							$onclicktd = "alert(\"Already booked\")";
						}else{
							$onclicktd = $onclicktd1;
						}
						
						$table .= "<td width='50'><span id='shbutton'>
						<input class='c$c scol' style='{$btnpad}{$backgournd}' date='".$day."' time='".$button."' type='button' datetime='".$day." ".$button."' clientid=".base64_encode($client_id)." size='10' orderid='$order_id' value='".$text."' onclick='$onclicktd' $reschuleatt />
						</span></td>";
					}else{
						$backgournd = "";
						$table .= "<td width='50' align='center'>&nbsp;NA</td>";
					}
				
							
				}
				
			$table .="
		</tr>";
		} 
		
		// next perivious calander
		if($nextweek===true){
		$nxt8mon =next8monday(date('Y-m-d'));
		$table .= "<tr>
			<td colspan='14'>
				<div id='weeklist' style='float: left;text-align: center;width: 100%;border:1px solid black;'>	
					<!--<h4>Week of Year</h4>-->";
			// print next weeks
			$w=0;
			foreach($nxt8mon as $week){
				$w++;
				$cw = date('m',strtotime($week));
				$monthtext = date('F',strtotime($week));
				//echo "<br />";
				if($w == 1){
					$pr = $cw;
					$table .= "<span style='float:left;width: 33%;'><h4>$monthtext</h4>";
				}
				if($pr < $cw){
					$table .= "</span>";
					$table .= "<span style='float:left;width: 33%;' ><h4>$monthtext</h4>";
				}
				$pr = $cw;
				
				// set anchor bg color
				$res = client_availableinweek($client_id,$week);
				if($res ){
					$acolor = "style='color:green'";
				}else{
					$acolor = "";
				}
				
				//echo "<br />";
				if($w==1){
					
					$table .= "<a $acolor href='javascript:void(0)' onclick='loadbooked(\"$monthweekid\",$client_id,$order_id,\"\")'>Schedule for current week </a>";
				}else{
					
					$table .= "<a $acolor href='javascript:void(0)' onclick='loadbooked(\"$monthweekid\",$client_id,$order_id,\"$week&\")'>
					Schedule for ".date(" F dS",strtotime($week))."</a>";
				}
				$table .= "<br />"; // adding break to list
			}
			// scheduled 
			
			$table .= "</div>
				
			</td>
		</tr>";
		}
	$expboooking = bookingexplain();
	$table .= "<tr>
		<td colspan='14'>$expboooking</td>
	</tr>";
	$table .= "</table>";
	return $table;
	
}


/*
* Print client availblity table
* $client id int
*
***/

function rescheduled($client_id,$nextweek=false,$scheduleid=null,$funtype=null)
{
	// sheduling table here
	if(!empty($_REQUEST['date'])){
		$date = $_REQUEST['date'];
	}else{
		$date = date("Y-m-d");
	}
	$daysshsplay = week_dayaftertoday($date);
	
	$reschuleatt ='';
	$bkdata ='';
	
	$onclicktd = "resh_c_sechdule(this)";
	$reschuleatt = "scheduleid ='$scheduleid' ";
	$bkdata = get_booked($scheduleid);
	$monthweekid =  "myreschedule";
	
	
	$buttons = array(
		'08:00'=>'8AM',
		'09:00'=>'9AM',
		'10:00'=>'10AM',
		'11:00'=>'11AM',
		'12:00'=>'12PM',
		'13:00'=>'1PM',
		'14:00'=>'2PM',
		'15:00'=>'3PM',
		'16:00'=>'4PM',
		'17:00'=>'5PM',
		'18:00'=>'6PM',
		'19:00'=>'7PM',
		'20:00'=>'8PM',
	);
	// style section
	$btnpad = "padding: 5px;";
	
	// table 
	$table ="";
	$table .= "
	<table width='785' >
		<tr>
			<td width='100'>
				<!--<b>Green available for drop</b>-->";
				$btime ='';
				$bdate = '';
				if($bkdata){
					// getting booked data coloumn class
					$btime = date("H:s",strtotime($bkdata['time']));
					$bdate = $bkdata['date'];
					
					$xdata = "<div id='schmessage'><b color='red'>Previous time Setted : {$bkdata['date']} {$bkdata['time']}</b></div>";
				}
				$table .="
			</td>
			<td align='center' style='color:red;height: 28px;' id='schedulmessage' colspan='13'>$xdata</td></tr>";
			
	$r = 1;
	$onclicktd1 = $onclicktd;
	foreach($daysshsplay as $day) {
		$r++; #increasing row
		$table .= "	<tr>
			
			<td width='100'>
				<b>
					".date("l , F dS",strtotime($day))."
				</b>
			</td>
			
			";
				$c = 1;
				foreach($buttons as $button=>$text) {
					$c++; # increasing colom
					// set active to already scheduled
					$sid = is_scheduled($client_id,$day,$button,"$day $button");
					if(!empty($sid)){ // if client available
						
						$backgournd = "background-color:green;";

						$asid = isclientbusy($client_id,$day,$button,"$day $button");
						
						if(!empty($asid)) // if client is busy
						{
							$backgournd = "background-color:yellow;";
							$onclicktd = "alert(\"Already booked\")";
						}else{
							$onclicktd = $onclicktd1;
						}
						if(($btime ==$button) &&($bdate==$day)){ // last booked orange
							$backgournd = "background-color:orange;";
						} 
						
						$table .= "<td width='50'><span id='shbutton'>
						<input class='c$c scol' style='{$btnpad}{$backgournd}' date='".$day."' time='".$button."' type='button' datetime='".$day." ".$button."' clientid=".base64_encode($client_id)." size='10' scheduleid='$scheduleid'  value='".$text."' onclick='$onclicktd'  />
						</span></td>";
					}else{
						$backgournd = "";
						$table .= "<td width='50' align='center'>&nbsp;NA</td>";
					}
				
							
				}
				
			$table .="
		</tr>";
		} 
		
		// next perivious calander
		if($nextweek===true){
		$nxt8mon =next8monday(date('Y-m-d'));
		$table .= "<tr>
			<td colspan='14'>
				<div id='weeklist' style='float: left;text-align: center;width: 100%;border:1px solid black;'>	
					<!--<h4>Week of Year</h4>-->";
			// print next weeks
			$w=0;
			foreach($nxt8mon as $week){
				$w++;
				
				$cw = date('m',strtotime($week));
				$monthtext = date('F',strtotime($week));
				//echo "<br />";
				if($w == 1){
					$pr = $cw;
					$table .= "<span style='float:left;width: 33%;'><h4>$monthtext</h4>";
				}
				if($pr < $cw){
					$table .= "</span>";
					$table .= "<span style='float:left;width: 33%;' ><h4>$monthtext</h4>";
				}
				$pr = $cw;
				
				// set anchor bg color
				$res = client_availableinweek($client_id,$week);
				if($res ){
					$acolor = "style='color:green'";
				}else{
					$acolor = "";
				}
				//echo "<br />";
				if($w==1){
					$table .= "<a $acolor href='javascript:void(0)' scheduleid='$scheduleid' clientid='".base64_encode($client_id)."' week='' onclick='getresechdule(this)'>Schedule for current week </a>";
				}else{
					$table .= "<a $acolor href='javascript:void(0)' scheduleid='$scheduleid' clientid='".base64_encode($client_id)."' week='$week' onclick='getresechdule(this)'>
					Schedule for ".date(" F dS",strtotime($week))."</a>";
				}
				$table .= "<br />"; // adding break to list
			}
			// scheduled 
			
			$table .= "</div>
				
			</td>
		</tr>";
			
		}
	$expboooking = bookingexplain();
	$table .= "<tr>
		<td colspan='14'>$expboooking</td>
	</tr>";
	$table .= "</table>";
	return $table;
	
}



/*
* Print schedule on staff
* page
**/

function schedulestaffside($client,$order_id)
{
	//echo $client;die;
	$table =cleint_availabletable($client,true,$order_id);
	return $table;
}

/*
* Get client schedule
* @clientid int
*
****/
function clientschedule($clientid)
{
	$table = 'client_schedule';
	$query = "Select id FROM $table WHERE
				`client_id` = '$clientid' ";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	while($data = mysql_fetch_array($response))
	{
		$dataR[] = $data;
	}
	return $dataR;
}

/*
* function to add schedule 
* @cllientid int
* @$date date
* @time time
* @datetime datetime
**/
function arff($text=''){
	return "$text<br />FILE : ".__FUNCTION__." Function : ".__FUNCTION__;
}
function add_schedule($clientid,$date,$time,$datetime)
{
	$table = 'client_schedule';
	$query = "INSERT INTO $table SET
				`client_id` = '$clientid',
				`date` ='$date',
				`time` ='$time',
				`date_time`='$datetime'
		";
	if($clientid !='0' && (!is_int($clientid))){
		$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	}else{
		die(arff('No client id here'));
	}
	
	return $response;
}

/*
* Check if already scheduled 
* @cllientid int
* @$date date
* @time time
* @datetime datetime
**/

function is_scheduled($clientid,$date,$time,$datetime)
{
	$table = 'client_schedule';
	$query = "Select id FROM $table WHERE
				`client_id` = '$clientid' AND 
				`date` ='$date' AND 
				`time` ='$time' AND
				`date_time`='$datetime'
		";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	$data = mysql_fetch_array($response);
	return $data['id'];
}

/*
* Remove schedule
* $shedulid int
**/

function remove_schedule($shedulid)
{
	$table = 'client_schedule';
	
	$query = "DELETE FROM $table WHERE id = $shedulid LIMIT 1";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	return $response;
}

/*
* Retrived schedule of the client
* @clientid int
**/

function getc_schedule($clientid)
{
	$table = 'client_schedule';
	$query = "Select * FROM $table WHERE
				`client_id` = '$clientid'
		";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	while($row = mysql_fetch_array($response))
	{
		$data[] = $row;
	}
	return $data;
}

/*
* Next week date start from monday
* @date date
**/

function nextweekmonday($date)
{
	$date = date("Y-m-d",strtotime($date));
	$lastmonday = date("Y-m-d",strtotime("$date last Monday"));
	return date("Y-m-d",strtotime("$lastmonday +1 week"));
}

/*
* Next for Monday dates
* @date date
**/

function next8monday($date)
{	
	//$date = "2013-09-02";
	$datecount = 8;
	$inc_c_mon = TRUE;

	$dates[] = $nmonday = date('Y-m-d',strtotime("$date last Monday")); // current 
	
	for($x=0;$x<$datecount;$x++){
	
		// get for input date
		$dates[] = $nmonday = date('Y-m-d',strtotime("$nmonday +1 week"));
		
	}
	return $dates;
}

/*
* return true if client is busy
* 
*
***/
function isclientbusy($clientid,$date,$time,$datetime)
{
	$table = 'client_booking';
	
	$query = "Select id FROM $table WHERE
				`client_id` = '$clientid' AND 
				`date` ='$date' AND 
				`time` ='$time' AND
				`date_time`='$datetime'
		";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	$data = mysql_fetch_array($response);
	return $data['id'];
}

/*
* Book client schedule for drop machine
*
**/

function addbook_sechedule($clientid,$date,$time,$datetime,$orderid)
{
	$table = 'client_booking';
	$scheduleid = is_scheduled($clientid,$date,$time,$datetime);
	$query = "INSERT INTO $table SET
				`client_id` = '$clientid',
				`date` ='$date',
				`time` ='$time',
				`date_time`='$datetime',
				`location_id`='$orderid',
				`schedule_id`='$scheduleid'
		";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	return $response;
}


/*
* Book client schedule for drop machine
*
**/

function removebook_sechedule($clientid,$date,$time,$datetime,$orderid)
{
	$table = 'client_booking';
	$scheduleid = is_scheduled($clientid,$date,$time,$datetime);
	$query = "DELETE FROM $table WHERE
				`client_id` = '$clientid' AND 
				`date` ='$date' AND
				`time` ='$time' AND
				`date_time`='$datetime' AND
				`location_id`='$orderid' AND
				`schedule_id`='$scheduleid' LIMIT 1
		";
	$response = mysql_query($query) or die(mysql_error()."<br />Function : ".__FUNCTION__);
	return $response;
}

/*
* Reschedule client appointment
*
*
***/
function resheduleap($clientid,$location)
{
	$table = 'location';
	$update = "Update $table set `re_sch_yn`='Y' , status='3' WHERE lid ='$location' AND cid = '$clientid' LIMIT 1";
	/* $update = "Update $table set `re_sch_yn`='Y'  WHERE lid ='$location' AND cid = '$clientid' LIMIT 1"; */
	
	return mysql_query($update) or die(arff(mysql_error()));
	
	return false;
}

/*
* Reschedule location
*
***/

function rsh_location($scheduleid,$date,$time,$datetime){
	//echo $scheduleid = is_scheduled($clientid,$date,$time,$datetime);
	$table = 'client_booking';
	$update = "Update $table set 
		`date` = '$date',
		`time` = '$time',
		`date_time` = '$datetime'
		WHERE location_id ='$scheduleid'
	 LIMIT 1";
	return mysql_query($update);
}

/*
* Get booked
*
***/

function get_booked($scheduleid){
	//echo $scheduleid = is_scheduled($clientid,$date,$time,$datetime);
	$table = 'client_booking';
	$update = "SELECT * FROM $table WHERE location_id ='$scheduleid' LIMIT 0,1";
	$data = mysql_query($update);
	return $data = mysql_fetch_array($data);
}

/*
* Comman booking function 
* use to dynami of
*
****/
function commanbooking($type,$clientid)
{
	/*
	* Type should have 
	* setschedule --  to set availblity schedule
	* bookschedule -- to book client location
	* rebookschedule -- to re book client location
	***/
	$mytype = array('setschedule','bookschedule','rebookschedule');

	if(!in_array($type, $mytype)){
		die('Not valid type');
	}
	if(empty($clientid)){
		die('client id missing :(');
	}
	
	
	
	/*
	* Function to create Row of time
	*
	*
	**/
	
	function create_row($date,$clientid)
	{
		
		$day = $date;
		//$day = "$day";
		$class = "class=\"c$c scol $day\"";
		$style = "style=\"{$btnpad}{$backgournd}\"";
		$time = "time=\"$button\"";
		$date = "date=\"$day\"";
		$datetime = "datetime=\"$day $button\" ";
		$clientid = "clientid=\"$clientid\" ";
		$value = "value = \"$value\"";
		$onclick = "onclick = 'alert(\"Ok\")'";
		global $timearray;
		foreach($timearray as $text=>$value){
			$datetime = "datetime=\"$day $text\" ";
			$time = "time=\"$text\"";
			$value = "value = \"$value\"";
			$sid = is_scheduled($client_id,$day,$button,"$day $button");
			if(!empty($sid)){
				$backgournd = "background-color:green;";
			}else{
				$backgournd = "";
			}
			
			$onclick = "onclick='s_c_available(this)'";
			
			//  check for if clent is booked for this time
			$asid = isclientbusy($client_id,$day,$button,"$day $button");
					
			if(!empty($asid)) // if client is busy
			{
				$backgournd = "background-color:yellow;";
				$onclick = "onclick='alert(\"Sechedule is booked for this time\")'";
			}
			
			$style = "style=\"{$btnpad}{$backgournd}\"";
			
			$tdarray = array(
				'day'=>$day,
				'class'=>$class,
				'style'=>$style,
				'time'=>$time,
				'date'=>$date,
				'datetime'=>$datetime,
				'clientid'=>$clientid,
				'value'=>$value,
				'onclick'=>$onclick,
			);
			echo create_td($day,$tdarray);
		}
	}
	
	/*
	* create td for table
	*
	**/
	function create_td($day,$tdaray=array('day'=>''))
	{
		
		extract($tdaray);
		
		return $table = "<td width='50'><span id='shbutton'>
				<input type='button'  size='10' $class $style $time $date $datetime $clientid $value $onclick />
			</span></td>";
	}
	
	
	########### Making table ######
	if(!empty($_REQUEST['date'])){
		$date = $_REQUEST['date'];
	}else{
		$date = date("Y-m-d");
	}
	$daysshsplay = week_dayaftertoday($date);
	$clientid = base64_encode($clientid);
	foreach ($daysshsplay as $date){
		create_row($date,$clientid);die;
	}
	
	
}

/*
* get unavailable client in next week
* @date start date
*
***/

function get_client_not_available_in_next_week()
{

	$selectc = "Select cid from client WHERE cid IN (SELECT cid FROM `admin_order` WHERE cid=74)";
	$result = mysql_query($selectc);
	if(is_resource($result))
	while($clientid = mysql_fetch_array($result))
	{
			//echo "<br/>";
			//echo "client: {$clientid['cid']}";
			$cid = $clientid['cid'];
			$selectord = "Select oid,lordered from admin_order where cid=$cid AND lordered > 0";

			$clients[]=0;
			$oresult = mysql_query($selectord);
			if(is_resource($oresult ))
			while($roid = mysql_fetch_array($oresult))
			{
				//echo "<br/>";
				//echo "-------- order id".$oid['oid'];
				$lordered = $roid['lordered'];
				$oid = $roid['oid'];
				$sveryfi = "select oid, count(*) as verify from location where cid=$cid and oid=$oid and status=1";
				$rveryfi = mysql_query($sveryfi);
				$rveryfi = mysql_fetch_array($rveryfi);
				$rveryfi = $rveryfi['verify'];
				
				$splaced = "select oid, count(*) as placed from location where cid=$cid and oid=$oid and status=2";
				$splaced = mysql_query($splaced);
				$splaced = mysql_fetch_array($splaced);
				$splaced = $splaced['placed'];
				//echo "<br/>";
				//echo "-------- * ------- Location ordered :$lordered | verified : $rveryfi | placed : $splaced";
				if(($lordered -($splaced+$rveryfi)) > 0){
					//echo "boundcl".$x++;
					//echo "<b style='color:red'>-------- * ------- * -------Bound</b>";
					$clients[] = $cid;
				}
				
			}
	}
	//die;
	//echo "<pre>";
	//print_r($clients);
	$clstring = implode(',',$clients);
	//$clstring = $clients;
	
	$weekstartday = date('Y-m-d',strtotime("Next Monday"));
	$weeklastday = end(week_dayaftertoday($weekstartday));
	$query = "SELECT cid,fname,email1 FROM `client` WHERE (cid NOT IN(SELECT client_id    
			FROM `client_schedule`    
			where `date` BETWEEN '$weekstartday' AND '$weeklastday')) AND (cid IN ($clstring))";
			
	$result = mysql_query($query) or die(mysql_error());
	$clients='';
	while($numrows = mysql_fetch_array($result))
	{
		$clients[] = $numrows;
	}
	//print_r($clients);
	return $clients;
	
}

/*
* return true if client available in this week
*
**/

function client_availableinweek($clientid,$weekstartday)
{	//$clientid,$weekstartday
	//$weekstartday = date('Y-m-d');
	$weeklastday = end(week_dayaftertoday($weekstartday));
	$query = "SELECT id    
			FROM `client_schedule`    
			where `date` BETWEEN '$weekstartday' AND '$weeklastday' AND client_id='$clientid'";
	//die;
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if($numrows > 0)
	{
		return true;
	}
	return false;
	
}

/*
* Get email template
* return with template with 
**/
function getemailtemp($title='',$body='')
{
	$homepage = file_get_contents('emailtemp.php');
	$homepage = str_replace('_MY_EMAIL_BODY_',$body, $homepage);
	$homepage = str_replace('_MY_EMAIL_TITLE_',$title, $homepage);
	return $homepage;
}

/*
* Booking page explaination
*
*
**/
function bookingexplain($reschedule=false)
{
	$tableexp = "
		<table style='font-family:arial,sans-serif;font-size: 12px;text-align:left;border:1px solid black;'>
			<tr>	
				<td>
					<b >How It Works:</b> <br />";
	if($reschedule===true){
		$tableexp .= "<p>
						Let us reschedule your appointment for you!  Simply update your drop off schedule above and let us know when you're available.  Then our team will call the business back and reschedule the appointment for you.  When finished click the Reschedule My Appointment Now button.  Once the new appointment has been set we will email you with the updated appointment time.
					</p>";
	}
	$tableexp .= "<b >Appointment Scheduling System</b> <br />
					<b style='color:green'>GREEN </b> = Yes, I can drop a machine off at this time! <br />
					<b style='color:gray'>GREY </b> = No, don&#96;t schedule any appointments for this time. <br />
					<b style='color:yellow'>YELLOW  </b> = Appointment is already scheduled for you at this time. <br /><br />
					<p >Select the appointment times that fit your schedule!  Our team will only schedule appointments during the times you select.  You can update your time blocks up to 2 months in the future.  And, you can update your schedule anytime you like if something changes.  </p><br />
					
					<ul><li>Select the appointment times that fit your schedule.</li>
					<li>Provide us your schedule up to 2 months in the future.</li>
					<li>We will only schedule appointments during the time blocks you select.</li>
					<li>Only 1 Appointment may be scheduled for each time block selected.  This ensures you have enough time to drop off a machine and drive to the next appointment.  Appointments will never be less than one hour apart.  </li>
					</ul>

				</td>
			</tr>
		</table>
	";
	return $tableexp;
}

/*
* Email of wich are not available 
* in next month
***/

function emailninweek($firstname,$cid)
{

	$cid = base64_encode($cid);
	$anchor = "<a style='text-decoration:none' href='http://26-life.com/login/update_client_schedule.php?cid=$cid' >CLICK HERE</a>";
	$text = "$firstname, <br /><br/>

	<p>Your location drop off schedule has no available time slots for the next 7 days.  In order for us to set new appointments for you next week we need to know what time slots you're available.</p>

	<b>UPDATE YOUR DROP OFF SCHEDULE NOW - $anchor</b>


	<li>Select the appointment times that fit your schedule.</li>
	<li>Provide us your schedule up to 2 months in the future.</li>
	<li>We will only schedule appointments during the time blocks you select.</li>
	<li>Only 1 Appointment may be scheduled for each time block selected.  This ensures you have enough time to drop off a machine and drive to the next appointment.  Appointments will never be less than one hour apart.  </li>


 
	<b>UPDATE YOUR DROP OFF SCHEDULE NOW - $anchor</b>
	";
	return getemailtemp($title='',$text);
}

function testemailninweek($cid)
{
	$title ="Please Update Your Drop Off Schedule";
	$firstname = "Admin";
	$cid = base64_encode($cid);
	$anchor = "<a style='text-decoration:none' href='http://26-life.com/login/update_client_schedule.php?cid=$cid' >CLICK HERE</a>";
	$text = "$firstname, <br /><br/>

	<p>Your location drop off schedule has no available time slots for the next 7 days.  In order for us to set new appointments for you next week we need to know what time slots you're available.</p>

	<b>UPDATE YOUR DROP OFF SCHEDULE NOW - $anchor</b>


	<li>Select the appointment times that fit your schedule.</li>
	<li>Provide us your schedule up to 2 months in the future.</li>
	<li>We will only schedule appointments during the time blocks you select.</li>
	<li>Only 1 Appointment may be scheduled for each time block selected.  This ensures you have enough time to drop off a machine and drive to the next appointment.  Appointments will never be less than one hour apart.  </li>


 
	<b>UPDATE YOUR DROP OFF SCHEDULE NOW - $anchor</b>
	";
	return getemailtemp($title,$text);
}

/*
* Send email
*
***/
function emailsend($to,$subject,$emailbody)
{
	$header="from:noreply@kickstartlocations.com\r\n";
	$header.="Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\n";
	return mail($to,$subject,$emailbody,$header);
}

/*
* function remove undefined location
*
**/

function remove_undefined_location()
{
	$query = "DELETE FROM `client_booking` WHERE location_id NOT IN (SELECT location_id from location) ";
	mysql_query($query) or die(mysql_error());
}

/*
* Client not scheduled 
* for current week
* 
***/
function non_schedule_client_week()
{
	$date = date("Y-m-d");
	if('Mon' !=date('D',strtotime("$date"))){
		$weekstartday = date('Y-m-d',strtotime("$date last Monday")); // current 
	}else{
		$weekstartday = $date;
	}	
	$weeklastday = end(week_dayaftertoday($weekstartday));
	$cquery = "SELECT * FROM scheduled_issueclient WHERE issue_date BETWEEN '$weekstartday' AND '$weeklastday'";
	$chresult = mysql_query($cquery) or die(mysql_error());
	$numrow = mysql_num_rows($chresult);
	if($numrow > 0)
	return false;
	
	$selectc = "Select cid from client WHERE cid IN (SELECT cid FROM `admin_order`)";
	$result = mysql_query($selectc);
	if(is_resource($result))
	while($clientid = mysql_fetch_array($result))
	{
			//echo "<br/>";
			//echo "client: {$clientid['cid']}";
			$cid = $clientid['cid'];
			$selectord = "Select oid,lordered from admin_order where cid=$cid AND lordered > 0";

			//$clients[]=0;
			$oresult = mysql_query($selectord);
			if(is_resource($oresult ))
			while($roid = mysql_fetch_array($oresult))
			{
				//echo "<br/>";
				//echo "-------- order id".$oid['oid'];
				$lordered = $roid['lordered'];
				$oid = $roid['oid'];
				$sveryfi = "select oid, count(*) as verify from location where cid=$cid and oid=$oid and status=1";
				$rveryfi = mysql_query($sveryfi);
				$rveryfi = mysql_fetch_array($rveryfi);
				$rveryfi = $rveryfi['verify'];
				
				$splaced = "select oid, count(*) as placed from location where cid=$cid and oid=$oid and status=2";
				$splaced = mysql_query($splaced);
				$splaced = mysql_fetch_array($splaced);
				$splaced = $splaced['placed'];
				//echo "<br/>";
				//echo "-------- * ------- Location ordered :$lordered | verified : $rveryfi | placed : $splaced";
				if(($lordered -($splaced+$rveryfi)) > 0){
					//echo "boundcl".$x++;
					//echo "<b style='color:red'>-------- * ------- * -------Bound</b>";
					$clients[] = $cid;
				}
				
			}
	}
	
	$clstring = implode(',',$clients);
	
	$date = date('Y-m-d');
	if('Mon' !=date('D',strtotime("$date"))){
		$weekstartday = date('Y-m-d',strtotime("$date last Monday")); // current 
	}else{
		$weekstartday = $date;
		//echo "asdf";
	}	
	//$weekstartday = date('Y-m-d',strtotime("last Monday"));
	$weeklastday = end(week_dayaftertoday($weekstartday));
	$query = "SELECT * FROM `client` WHERE (cid NOT IN(SELECT client_id    
			FROM `client_schedule`    
			where `date` BETWEEN '$weekstartday' AND '$weeklastday')) AND (cid IN ($clstring))";
			
	$result = mysql_query($query) or die(mysql_error());
	$clients='';
	while($numrows = mysql_fetch_array($result))
	{
		$clients[] = $numrows;
	}
	
	//print_r($clients);
	return $clients;
}

/*
* Insert non_schedule_client_week()
* clients into data base 
* all client details array
***/
function ins_issue_client($clientdataarray)
{
	$date = date("Y-m-d");
	
	$insert = FALSE;
	/* check if alareay done for this week*/
	if('Mon' !=date('D',strtotime("$date"))){
		$weekstartday = date('Y-m-d',strtotime("$date last Monday")); // current 
	}else{
		$weekstartday = $date;
	}	
	$weeklastday = end(week_dayaftertoday($weekstartday));
	$cquery = "SELECT * FROM scheduled_issueclient WHERE issue_date BETWEEN '$weekstartday' AND '$weeklastday'";
	$chresult = mysql_query($cquery) or die(mysql_error());
	$numrow = mysql_num_rows($chresult);
	if($numrow == 0)
	$insert = TRUE;
	/* End checking */
	
	
	$query = "INSERT INTO scheduled_issueclient
	  (cid, issue_date)
	VALUES";
	if(is_array($clientdataarray))
	foreach($clientdataarray as $client){
		$query .="({$client['cid']}, '$date'),"; 
	}
	$query = rtrim($query,',');
	if($insert===TRUE)
	return mysql_query($query) or die(mysql_error());
	else
	return false;
}

/*
* function get issued client
*
***/
function get_issued_clients()
{
	$clients = '';
	$cquery = "SELECT * FROM scheduled_issueclient si
		LEFT JOIN client c ON c.cid = si.cid
	WHERE  si.issue_status =0 ";
	$chresult = mysql_query($cquery) or die(mysql_error());
	while($numrows = mysql_fetch_array($chresult))
	{
		$clients[] = $numrows;
	}
	return $clients;
}

/*
* Issue Report
*
**/

function get_issued_report()
{
	$clients = '';
	$cquery = "SELECT * FROM scheduled_issueclient si
		LEFT JOIN client c ON c.cid = si.cid ORDER BY si.issue_status DESC
	";
	$chresult = mysql_query($cquery) or die(mysql_error());
	while($numrows = mysql_fetch_array($chresult))
	{
		$clients[] = $numrows;
	}
	return $clients;
}


//echo bookingexplain();

//client_availableinweek();
//print_r(next8monday(date('Y-m-d')));
