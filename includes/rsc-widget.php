<?php
/**
 * Adds Rising Sign Calculator Widget
 *
 * @author 	Isabel Castillo
 * @package 	Rising Sign Calculator
 * @extends 	WP_Widget
 */

class rsc_widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'rsc_widget',
			__('Rising Sign Calculator', 'rsc'),
			array( 'description' => __( 'Let visitors calculate their rising sign and get an interpretation.', 'rsc' ), )
		);
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action('wp_ajax_rsc_interp', array( $this, 'rsc_interp_callback') );
		add_action('wp_ajax_nopriv_rsc_interp', array( $this, 'rsc_interp_callback') );
	}

	/**
	 * Since 1.0
	 */

	public function enqueue() {

		wp_register_style('rsc', plugins_url('/rsc.css', dirname(__FILE__)));
		wp_enqueue_style('rsc');
		
		wp_register_style('rsc-jquery-ui', 'http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css');
		wp_enqueue_style('rsc-jquery-ui');

		wp_register_style('rsc-style-rtl', plugins_url('rtl.css', __FILE__) );

		if ( is_rtl() )
			wp_enqueue_style( 'rsc-style-rtl' );
	
		wp_enqueue_script('jquery');

		wp_register_script('rsc-jquery-ui','http://code.jquery.com/ui/1.9.2/jquery-ui.js', array('jquery'));
		wp_enqueue_script('rsc-jquery-ui');

		wp_register_script('rsc', plugins_url('rsc.js', __FILE__), array('rsc-jquery-ui', 'jquery'));
		wp_enqueue_script('rsc');

		// get language code to tranlsate Autocomplete cities list if needed

		$wplang = get_locale();
		$langcode = substr( $wplang, 0, 2 ); // take 2 letter lang code only

		// if wp lang returns something other than 'en', then set the $city_list_lang
		
		$city_list_lang = ( 'en' != $langcode ) ? $langcode : '';

		$params = array(
			'tzoffset' => plugins_url('ajax-tz-offset.php', __FILE__),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'sele' => __('Selected:', 'rsc'),
			'lati' => __('Latitude:', 'rsc'),
			'longit' => __('Longitude:', 'rsc'),
			'gmt' => __('GMT time offset:', 'rsc'),
			'lang' => $city_list_lang
		);
		wp_localize_script( 'rsc', 'isa_ajax_object', $params );

	}

/** @todo delete 
 * Log my own debug messages
 */
public function isa_log( $message ) {
    if (WP_DEBUG === true) {
        if ( is_array( $message) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}
	
	/**
	 * convert just the hour part of a time from 12-hour format into 24-hour format
	 * @param $h12, hour in 12-hour format, 1 - 12
	 * @param $meridiem, accepts only string "a.m." or "p.m."
	 * returns string "00" or "1" - "12", or integer 13 - 23
	 * Since 1.0
	 */
		
	public function hour12to24($h12, $meridiem) {
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

	
	/**
	 * Convert Asc decimal longitude into zodiac sign degree, minute, icon, and interpretation
	 * Since 1.0
	 */
	
	public function isa_get_rising_sign($longitude) {


		// convert longitude decimal to sign num
		$sign_num = floor($longitude / 30);

		// split decimal longitude into degree, min, sec of sign
		$pos_in_sign = $longitude - ($sign_num * 30);
		$deg = floor($pos_in_sign);
		$full_min = ($pos_in_sign - $deg) * 60;
		$min = floor($full_min);
		$full_sec = round(($full_min - $min) * 60);
		
		$dms_numbers_range = range(0, 59);
				
		$localize_dms_numbers = array(__('00', 'rsc'),__('01', 'rsc'),__('02', 'rsc'),__('03', 'rsc'),__('04', 'rsc'),__('05', 'rsc'),__('06', 'rsc'),__('07', 'rsc'),__('08', 'rsc'),__('09', 'rsc'),__('10', 'rsc'),__('11', 'rsc'),__('12', 'rsc'),__('13', 'rsc'),__('14', 'rsc'),__('15', 'rsc'),__('16', 'rsc'),__('17', 'rsc'),__('18', 'rsc'),__('19', 'rsc'),__('20', 'rsc'),__('21', 'rsc'),__('22', 'rsc'),__('23', 'rsc'),__('24', 'rsc'),__('25', 'rsc'),__('26', 'rsc'),__('27', 'rsc'),__('28', 'rsc'),__('29', 'rsc'),__('30', 'rsc'),__('31', 'rsc'),__('32', 'rsc'),__('33', 'rsc'),__('34', 'rsc'),__('35', 'rsc'),__('36', 'rsc'),__('37', 'rsc'),__('38', 'rsc'),__('39', 'rsc'),__('40', 'rsc'),__('41', 'rsc'),__('42', 'rsc'),__('43', 'rsc'),__('44', 'rsc'),__('45', 'rsc'),__('46', 'rsc'),__('47', 'rsc'),__('48', 'rsc'),__('49', 'rsc'),__('50', 'rsc'),__('51', 'rsc'),__('52', 'rsc'),__('53', 'rsc'),__('54', 'rsc'),__('55', 'rsc'),__('56', 'rsc'),__('57', 'rsc'),__('58', 'rsc'),__('59', 'rsc'));
				
		$localized_dms = array_combine($dms_numbers_range,$localize_dms_numbers);
				
		$localized_deg = $localized_dms[$deg];
		$localized_min = $localized_dms[$min];
		$localized_full_sec = $localized_dms[$full_sec];

		$localized_full_sec = $localized_full_sec . chr(34);



		// @todo add default fallback interps
		// @todo issue in which any existing custom interps in admin may be deleted upon re-install.

		$isa_rising_signs = array(
			array( 
				'id' => 'aries',
				'name' => __( 'Aries', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'taurus',
				'name' => __( 'Taurus', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'gemini',
				'name' => __( 'Gemini', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'cancer',
				'name' => __( 'Cancer', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'leo',
				'name' => __( 'Leo', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'virgo',
				'name' => __( 'Virgo', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'libra',
				'name' => __( 'Libra', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'scorpio',
				'name' => __( 'Scorpio', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'sagittarius',
				'name' => __( 'Sagittarius', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'capricorn',
				'name' => __( 'Capricorn', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'aquarius',
				'name' => __( 'Aquarius', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
			array( 
				'id' => 'pisces',
				'name' => __( 'Pisces', 'rsc' ),
				'interp' => __( '', 'rsc' )
			),
		);

		foreach($isa_rising_signs as $isa_rising_sign) {
	
			$options = get_option('rsc_options');
				// if custom interp is entered, use it, else use default

			$interp = ( isset( $options[$isa_rising_sign['id']] ) && !empty($options[$isa_rising_sign['id']]) ) ? 
				$options[$isa_rising_sign['id']] : $isa_rising_sign['interp'];
		
			$isa_rising_interp[] = '<h3 class="' . $isa_rising_sign['id'] . '">' . sprintf( __( '%s Rising', 'rsc' ) , $isa_rising_sign['name'] ) . '</h3><p>' . $interp . '</p>';
			$rname[] = $isa_rising_sign['name'];

		}
//			$prepout = $isa_rising_interp[$sign_num] . '<p>' . __( 'Ascendant: ', 'rsc' ) . $rname[$sign_num] . " " . $deg . "&#176; " . $min . "' " . $full_sec . '"</p>';// @test replace


		$prepout = $isa_rising_interp[$sign_num] . '<p>' . __( 'Ascendant: ', 'rsc' ) . 
sprintf('%s %s&#176; %s\' %s', $rname[$sign_num], $localized_deg , $localized_min, $localized_full_sec) . '</p>';// @test replace

		return $prepout;
	}

	/**
	* ajax callback. Process form
	*
	*/

	public function rsc_interp_callback() {
		global $wpdb;
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$hour = $_POST['hour'];
		$minute = $_POST['minute'];
		$ampm = $_POST['ampm'];
		$timezone = $_POST['offset_geo'];
		$lat_decimal_1 = $_POST['lat_decimal_1'];
		$long_decimal_1 = $_POST['long_decimal_1'];
		$place1 = $_POST['place1'];

		// validate

		$my_error = '';

		if ( ($month != "") And ($day != "") And ($year != "") ) {
			if (!$validdate = checkdate($month, $day, $year)) {
				$my_error .= __('The date of birth you entered is not valid.', 'rsc') . '<br><br>';
			}
		}
			
		if( empty($month) ) {
			$my_error .= __('Month is empty.', 'rsc') . '<br><br>';
		}

		if( empty($day) ) {
			$my_error .= __('Day of birth is empty.', 'rsc') . '<br><br>';
		}

		if( empty($place1) || empty($lat_decimal_1) || empty($long_decimal_1) ) {
			$my_error .= __('Birth City is empty.', 'rsc') . '<br><br>';
		}

		$nextYr = date("Y")+1;
		if (($year < 1900) Or ($year > $nextYr)) {
			$my_error .= sprintf(__('Please enter a year between 1900 and %s.', 'rsc'), $nextYr) . '<br><br>';
		}
		
		if (($hour < 1) Or ($hour > 12) Or empty($hour)) {
			$my_error .= __('Birth hour must be between 1 and 12.', 'rsc') . '<br><br>';
		}
		
		if (($minute < 0) Or ($minute > 59) Or empty($minute)) {
			$my_error .= __('Birth minute must be between 0 and 59.', 'rsc') . '<br><br>';
		}
		
		$tz_length = strlen((string)$timezone);
		
		if( $tz_length > 6 ) {
			$my_error .= __('Birth City is empty. After choosing a city, please wait 1 second before clicking Submit.', 'rsc') . '<br><br>';
		}

		if( empty($ampm) ) {
			$my_error .= __('Please choose a.m. or p.m. for the birth time.', 'rsc') . '<br><br>';
		}

		if( $lat_decimal_1 == "-" ) {
			$my_error .= __('Latitude cannot be a only a dash.', 'rsc') . '<br><br>';
		} elseif( !empty($lat_decimal_1) && !is_numeric($lat_decimal_1) ) {
			$my_error .= __('Latitude must be a number, and may include a negative sign and decimal point like: -80.5', 'rsc') . '<br><br>';
		}

		if( $long_decimal_1 == "-" ) {
			$my_error .= __('Longitude cannot be a only a dash.', 'rsc') . '<br><br>';
		} elseif( !empty($long_decimal_1) && !is_numeric($long_decimal_1) ) {
			$my_error .= __('Longitude must be a number, and may include a negative sign and decimal point like: -80.5', 'rsc') . '<br><br>';
		}

		if($my_error) { 
				
			$error_msg = '<p id="rsc-error">' . __('Error! The following error(s) occurred', 'rsc') . ':</p><p>' . $my_error . __('Please re-enter your birth data. Thank you.', 'rsc'). '</p>';

		} else {
		
			// no errors, so process form

			$error_msg = '';
	
			// hour1 posted incoming from order form is 12 hour format, but i need it in 24 hour here
			$hour1_in24 = $this->hour12to24($hour, $ampm);

			//assign birth data from form to local variables
			$inmonth = intval($month);
			$inday = $day;
			$inyear = $year;
			$inhours = $hour1_in24;
			$inmins = $minute;
			$insecs = "0";
			
			
			// adjust date and time for minus hour due to time zone taking the hour negative
			
			$intz = $timezone;
			
			if ($intz >= 0) {
				$whole = floor($intz);
				$fraction = $intz - floor($intz);
			} else {
				$whole = ceil($intz);
				$fraction = $intz - ceil($intz);
			}
			
			$inhours = $inhours - $whole;
			$inmins = $inmins - ($fraction * 60);
			
			if ($inyear >= 2000) {
				$utdatenow = strftime("%d.%m.20%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
			} else {
				$utdatenow = strftime("%d.%m.19%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
			}
			
			$utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

			$sweph = RSC_PLUGIN_DIR . 'sweph'; // set path to isabelse
			unset($PATH,$out,$longitude);
			$PATH = '';// WordPress is picky picky
			putenv("PATH=$PATH:$sweph");

			// get CAMPANUS houses
			exec ("isabelse -edir$sweph -b$utdatenow -ut$utnow -p -eswe -house$long_decimal_1,$lat_decimal_1,c -fl -head", $out);

			// output array index 0 - 11=houses, index 12=ASC, 13=MC, 14=ARMC, 15=vertex

			$ascendantlong = empty($out[12]) ? '' : $out[12];

			$final_interp = $this->isa_get_rising_sign($ascendantlong);

		} // end else no errors, done processing the form
						
		if( isset($error_msg) && !empty($error_msg) ) {

			$output = $error_msg;

		} else {

			$output = ($final_interp) ? $final_interp : __('Something is wrong...', 'rsc');

		}

		$json = json_encode(array('interp' => $output));
		die($json);

	}
	/**
	 * Get current page url using PHP, not native WP
	 * Since 1.0
	 */
	
	function current_url() {
			$pageURL = 'http';
			if( isset($_SERVER["HTTPS"]) ) {
				if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			}
			$pageURL .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			return $pageURL;
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Rising Sign Calculator', 'rsc' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		?>
<div id="rscform"><form id="orderform" type="post"><?php if ( ! empty( $title ) ) echo '<h3 class="widget-title">'. $title . '</h3>'; ?>
<div id="ajaxbirthdt"><p><label><?php _e('Birth Date:', 'rsc'); ?> </label> <select id="month" class="isa" name="month" required><option value=""><?php _e('Month:', 'rsc'); ?></option><option value="1"><?php _e('January', 'rsc'); ?></option><option value="2"><?php _e('February', 'rsc'); ?></option><option value="3"><?php _e('March', 'rsc'); ?></option><option value="4"><?php _e('April', 'rsc'); ?></option><option value="5"><?php _e('May', 'rsc'); ?></option><option value="6"><?php _e('June', 'rsc'); ?></option><option value="7"><?php _e('July', 'rsc'); ?></option><option value="8"><?php _e('August', 'rsc'); ?></option><option value="9"><?php _e('September', 'rsc'); ?></option><option value="10"><?php _e('October', 'rsc'); ?></option><option value="11"><?php _e('November', 'rsc'); ?></option><option value="12"><?php _e('December', 'rsc'); ?></option></select> <select id="day" class="isa" name="day" required><option value=""><?php _e('Day:', 'rsc'); ?></option>

<?php $dayrange = range(1, 31);

$localized_numbers = array(__('1', 'rsc'),__('2', 'rsc'),__('3', 'rsc'),__('4', 'rsc'),__('5', 'rsc'),__('6', 'rsc'),__('7', 'rsc'),__('8', 'rsc'),__('9', 'rsc'),__('10', 'rsc'),__('11', 'rsc'),__('12', 'rsc'),__('13', 'rsc'),__('14', 'rsc'),__('15', 'rsc'),__('16', 'rsc'),__('17', 'rsc'),__('18', 'rsc'),__('19', 'rsc'),__('20', 'rsc'),__('21', 'rsc'),__('22', 'rsc'),__('23', 'rsc'),__('24', 'rsc'),__('25', 'rsc'),__('26', 'rsc'),__('27', 'rsc'),__('28', 'rsc'),__('29', 'rsc'),__('30', 'rsc'),__('31', 'rsc'));

foreach ($dayrange as $dayr) { ?>

    <option value="<?php echo $dayr; ?>"><?php echo $localized_numbers[$dayr - 1]; ?></option>

<?php } ?>

</select><select id="year" class="isa" name="year" required><option value=""><?php _e('Year:', 'rsc'); ?></option>

<?php

$accepted_yrs = range(1900, 2015); // @todo must update year manually to avoid mismatch of key=>value with localized year.

$localize_year_numbers = array(__('1900', 'rsc'),__('1901', 'rsc'),__('1902', 'rsc'),__('1903', 'rsc'),__('1904', 'rsc'),__('1905', 'rsc'),__('1906', 'rsc'),__('1907', 'rsc'),__('1908', 'rsc'),__('1909', 'rsc'),__('1910', 'rsc'),__('1911', 'rsc'),__('1912', 'rsc'),__('1913', 'rsc'),__('1914', 'rsc'),__('1915', 'rsc'),__('1916', 'rsc'),__('1917', 'rsc'),__('1918', 'rsc'),__('1919', 'rsc'),__('1920', 'rsc'),__('1921', 'rsc'),__('1922', 'rsc'),__('1923', 'rsc'),__('1924', 'rsc'),__('1925', 'rsc'),__('1926', 'rsc'),__('1927', 'rsc'),__('1928', 'rsc'),__('1929', 'rsc'),__('1930', 'rsc'),__('1931', 'rsc'),__('1932', 'rsc'),__('1933', 'rsc'),__('1934', 'rsc'),__('1935', 'rsc'),__('1936', 'rsc'),__('1937', 'rsc'),__('1938', 'rsc'),__('1939', 'rsc'),__('1940', 'rsc'),__('1941', 'rsc'),__('1942', 'rsc'),__('1943', 'rsc'),__('1944', 'rsc'),__('1945', 'rsc'),__('1946', 'rsc'),__('1947', 'rsc'),__('1948', 'rsc'),__('1949', 'rsc'),__('1950', 'rsc'),__('1951', 'rsc'),__('1952', 'rsc'),__('1953', 'rsc'),__('1954', 'rsc'),__('1955', 'rsc'),__('1956', 'rsc'),__('1957', 'rsc'),__('1958', 'rsc'),__('1959', 'rsc'),__('1960', 'rsc'),__('1961', 'rsc'),__('1962', 'rsc'),__('1963', 'rsc'),__('1964', 'rsc'),__('1965', 'rsc'),__('1966', 'rsc'),__('1967', 'rsc'),__('1968', 'rsc'),__('1969', 'rsc'),__('1970', 'rsc'),__('1971', 'rsc'),__('1972', 'rsc'),__('1973', 'rsc'),__('1974', 'rsc'),__('1975', 'rsc'),__('1976', 'rsc'),__('1977', 'rsc'),__('1978', 'rsc'),__('1979', 'rsc'),__('1980', 'rsc'),__('1981', 'rsc'),__('1982', 'rsc'),__('1983', 'rsc'),__('1984', 'rsc'),__('1985', 'rsc'),__('1986', 'rsc'),__('1987', 'rsc'),__('1988', 'rsc'),__('1989', 'rsc'),__('1990', 'rsc'),__('1991', 'rsc'),__('1992', 'rsc'),__('1993', 'rsc'),__('1994', 'rsc'),__('1995', 'rsc'),__('1996', 'rsc'),__('1997', 'rsc'),__('1998', 'rsc'),__('1999', 'rsc'),__('2000', 'rsc'),__('2001', 'rsc'),__('2002', 'rsc'),__('2003', 'rsc'),__('2004', 'rsc'),__('2005', 'rsc'),__('2006', 'rsc'),__('2007', 'rsc'),__('2008', 'rsc'),__('2009', 'rsc'),__('2010', 'rsc'),__('2011', 'rsc'),__('2012', 'rsc'),__('2013', 'rsc'),__('2014', 'rsc'),__('2015', 'rsc'));

$form_years = array_combine($accepted_yrs,$localize_year_numbers);

arsort($form_years);


foreach ($form_years as $form_year => $locale_form_year) { ?>
	<option value="<?php echo $form_year; ?>"><?php echo $locale_form_year; ?></option>

<?php } ?>

</select></p><p><label><?php _e('Exact Birth Time:', 'rsc'); ?> </label><select id="hour" class="isa" name="hour" required><option value="">-</option>

<?php
$hourrange = range(1, 12);
foreach ($hourrange as $hourr) { ?>
	<option value="<?php echo $hourr; ?>"><?php echo $localized_numbers[$hourr - 1]; ?></option>
<?php } ?>

</select><select id="minute" class="isa" name="minute" required>	<option value="">-</option>


<?php 
$prepend = array('00','01','02','03','04','05','06','07','08','09');
$minuterange = array_merge($prepend,range(10, 59));


$localize_minute_numbers = array(__('00', 'rsc'),__('01', 'rsc'),__('02', 'rsc'),__('03', 'rsc'),__('04', 'rsc'),__('05', 'rsc'),__('06', 'rsc'),__('07', 'rsc'),__('08', 'rsc'),__('09', 'rsc'),__('10', 'rsc'),__('11', 'rsc'),__('12', 'rsc'),__('13', 'rsc'),__('14', 'rsc'),__('15', 'rsc'),__('16', 'rsc'),__('17', 'rsc'),__('18', 'rsc'),__('19', 'rsc'),__('20', 'rsc'),__('21', 'rsc'),__('22', 'rsc'),__('23', 'rsc'),__('24', 'rsc'),__('25', 'rsc'),__('26', 'rsc'),__('27', 'rsc'),__('28', 'rsc'),__('29', 'rsc'),__('30', 'rsc'),__('31', 'rsc'),__('32', 'rsc'),__('33', 'rsc'),__('34', 'rsc'),__('35', 'rsc'),__('36', 'rsc'),__('37', 'rsc'),__('38', 'rsc'),__('39', 'rsc'),__('40', 'rsc'),__('41', 'rsc'),__('42', 'rsc'),__('43', 'rsc'),__('44', 'rsc'),__('45', 'rsc'),__('46', 'rsc'),__('47', 'rsc'),__('48', 'rsc'),__('49', 'rsc'),__('50', 'rsc'),__('51', 'rsc'),__('52', 'rsc'),__('53', 'rsc'),__('54', 'rsc'),__('55', 'rsc'),__('56', 'rsc'),__('57', 'rsc'),__('58', 'rsc'),__('59', 'rsc'));


$form_minutes = array_combine($minuterange,$localize_minute_numbers);

foreach ($form_minutes as $form_minute => $locale_form_minute) { ?>
	<option value="<?php echo $form_minute; ?>"><?php echo $locale_form_minute; ?></option>

<?php } ?>
</select>
<select id="ampm" class="isa" name="ampm" required>
<option value="">-</option><option value="a.m."><?php _e( 'a.m.', 'rsc' ); ?></option><option value="p.m.">
<?php _e( 'p.m.', 'rsc' ); ?></option>
</select>
</p>
<p class="ui-widget"> <label><?php _e('Birth City:', 'rsc'); ?> </label><input id="city" name="city" class="inputtext isa" /></p>
<span id="hidTzgeo1"></span><p id="nocity1"><?php _e('No City Selected!', 'rsc'); ?></p>
<p id="newlog1"><br /><label id="city1label" for="place1"></label><input id="place1" name="place1" type="text" tabindex="-1" /><br /><span id="lat1label"></span><input id="lat_decimal_1" name="lat_decimal_1" type="text" tabindex="-1" /><br />
<span id="lng1label"></span><input id="long_decimal_1" name="long_decimal_1" type="text" tabindex="-1" /><br />
</p>	</div><!-- end # ajaxbirthdt -->
<span id="offsetwrap"><span id="gmt1label"></span>
<input id="offset_geo" name="offset_geo" type="text" tabindex="-1" />
</span><br /><button id="fetchOffset" class="button"><?php _e('Calculate Time Offset', 'rsc'); ?></button><br /><input type="hidden" name="action" value="rsc_interp"/><input type="button" id="fetchFields" value="<?php _e('Submit', 'rsc'); ?>" /></form></div><!-- #rscform -->
<div id="risingreport">
	<div id="risinginterp"></div><!-- will be filled by ajax -->
	<p class="centr"><a class="button" href="<?php echo $this->current_url(); ?>"><?php _e('Back To Rising Sign Calculator', 'rsc'); ?></a></p>
</div>

<?php echo $args['after_widget'];
	}// end widget

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults = array( 
					'title' => __('Rising Sign Calculator', 'rsc'),
					);
 		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:', 'rsc' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
				name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php 
	}
}
?>