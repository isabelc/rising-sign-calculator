<?php
/**
 * Calculate timezone offset and output back into form fields
 * @author Isabel Castillo
 * 
 * takes in time in 12-hour format and geo timezone id
 */

// post incoming form data: birthdate, birthtime, geo timezone id

$month1 = $_POST['month'];
$day1 = $_POST['day'];
$year1 = $_POST['year'];
$hour1 = $_POST['hour'];
$minute1 = $_POST['minute'];
$ampm1 = $_POST['ampm'];
$zon1_geo = $_POST['zon1_geo'];

// functions ready

/** 
 * Get time offset from UTC for a designated datetime & zonename. Returns offset in hours.
 * Can backtrack for old DST rules.
 * @param $zonename, timezone name (or GeoNames timezoneID)
 * @param birthdatetime, time stamp string 'YYYY-MM-DD HH:MM'
 */

function isa_timezone_offset($zonename, $birthdatetime) {
	
    	$tz = new DateTimeZone($zonename);
		$dtobj = new DateTime($birthdatetime);
		$offset = timezone_offset_get($tz, $dtobj);
		$inhours = $offset / 3600;
		return $inhours;
}

/**
 * convert just the hour part of a time from 12-hour format into 24-hour format
 * @param $h12, hour in 12-hour format, 1 - 12
 * @param $meridiem, accepts only string "a.m." or "p.m."
 * returns string "00" or "1" - "12", or integer 13 - 23
 */

function hour12to24($h12, $meridiem) {
 		if(($meridiem == 'a.m.') && ($h12 == '12')) {
			$mh = '00';
		} elseif( (($meridiem == 'a.m.') && ($h12 != '12')) || (($meridiem == 'p.m.') && ($h12 == '12')) ) {
			$mh = $h12;
		} elseif( ($meridiem == 'p.m.') && in_array($h12, array('1','2','3','4','5','6','7','8','9','10','11')) )  {
			$avc = (int) $h12;// conver to integer, then add 12.
			$mh = $avc + 12;
		}
		return $mh;// military hour
}

// CALC GEO OFFSET

// hour1 posted incoming from order form is 12 hour format, but i need it in 24 hour here
$hour1_in24 = hour12to24($hour1, $ampm1);

// get datetime stamp
$dtstamp1 = strftime("%Y-%m-%d %H:%M:%S", mktime($hour1_in24, $minute1, 0, $month1, $day1, $year1));

// get time offset
$offset1_geo = $zon1_geo ? isa_timezone_offset($zon1_geo, $dtstamp1) : __('calculating...', 'rsc');

// output

$offset = array("ofs1g" => $offset1_geo);
$offsetData = json_encode($offset);
echo $offsetData;
?>