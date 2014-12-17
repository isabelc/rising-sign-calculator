<?php
/*
Plugin Name: Rising Sign Calculator
Plugin URI: http://isabelcastillo.com/docs/category/rising-sign-calculator-wordpress-plugin
Description: Let visitors calculate their rising sign by inputting date and time of birth. Option to add your own custom interpretations.
Version: 1.3.2-alpha2.4.2
Author: Isabel Castillo
Author URI: http://isabelcastillo.com
License: GPL2
Text Domain: rsc
Domain Path: languages

Copyright 2013 - 2014 Isabel Castillo

This file is part of Rising Sign Calculator plugin.

Rising Sign Calculator plugin is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

Rising Sign Calculator plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Rising Sign Calculator plugin; if not, If not, see <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>.
*/
if(!class_exists('Rising_Sign_Calculator')) {
	class Rising_Sign_Calculator{

		public function __construct() {

			add_action( 'admin_init', array($this, 'updater'), 5 );
			add_action( 'admin_init', array($this, 'register_options') );
			add_action( 'admin_init', array($this, 'activate_license') );
			add_action( 'admin_init', array($this, 'deactivate_license') );
			add_action( 'admin_init', array($this, 'check_status') );
			add_action( 'admin_menu', array($this, 'add_plugin_page') );
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			// @new update. CONSTANT SHOULD BE UNIQUE
			if( ! defined( 'RSC_ISABEL_STORE_URL' ) )
				define( 'RSC_ISABEL_STORE_URL', 'http://isabelcastillo.com' );
			if( ! defined( 'RISING_SIGN_CALC_DLNAME' ) )
				define( 'RISING_SIGN_CALC_DLNAME', 'Rising Sign Calculator WP Plugin' );// @new name match exactly
			if( ! defined( 'RSC_PLUGIN_DIR' ) )
				define( 'RSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				include RSC_PLUGIN_DIR . 'includes/EDD_SL_Plugin_Updater.php';
			}
			require_once RSC_PLUGIN_DIR . 'includes/rsc-widget.php';

	    }
	
		/**
		* Set up easy updates.
		* @since 1.3
		*/

		public function updater() {
		
			$license_key = trim( get_option( 'isa_rsc_license_key' ) );// @new
			// @new check file path in second param		
			$edd_updater = new EDD_SL_Plugin_Updater( RSC_ISABEL_STORE_URL, __FILE__, array( 
					'version' 	=> '1.3.1',  // @todo update current version number
					'license' 	=> $license_key,
					'item_name' => RISING_SIGN_CALC_DLNAME,
					'author' 	=> 'Isabel Castillo'
				)
			);
		
		}
		
		/**
		* Output the license page.
		* @since 1.3
		*/

		public function rsc_license_page() {
			$license 	= get_option( 'isa_rsc_license_key' );
			$status 	= get_option( 'isa_rsc_license_status' );// @new
			?>
			<div class="wrap">
				<h2><?php _e( 'Rising Sign Calculator License Options', 'rsc' ); ?></h2>
		
		<p><?php _e( 'A plugin license will grant you access to support and updates. If you wish to update to the latest version of Rising Sign Calculator or get support for this plugin, you need an active license.', 'rsc' ); ?>
		&nbsp; <a href="<?php echo RSC_ISABEL_STORE_URL; ?>/downloads/" target="_blank"><?php _e( 'Purchase a license', 'rsc' ); ?></a></p>
		
				<form method="post" action="options.php">
				
					<?php settings_fields('isa_rsc_license'); ?>
					
					<table class="form-table">
						<tbody>
							<tr valign="top">	
								<th scope="row" valign="top">
									<?php _e('License Key', 'rsc'); ?>
								</th>
								<td>
									<input id="isa_rsc_license_key" name="isa_rsc_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
									<label class="description" for="isa_rsc_license_key"><?php _e('Enter your license key', 'rsc'); ?></label>
								</td>
							</tr>
							<?php if( false !== $license ) { ?>
								<tr valign="top">	
									<th scope="row" valign="top">
										<?php _e('Activate License', 'rsc' ); ?>
									</th>
									<td>
										<?php if( $status !== false && 'valid' == $status ) { ?>
		
				<span style="color:green;font-weight:bold;padding:12px;"><?php _e('Status: active  ', 'rsc' ); ?></span>
		
											<?php wp_nonce_field( 'isa_rsc_nonce', 'isa_rsc_nonce' ); ?>
											<input type="submit" class="button-secondary" name="isa_rsc_license_deactivate" value="<?php _e('Deactivate License', 'rsc' ); ?>"/><br/ ><br/ ><br/ >
		
		<input type="submit" class="button-secondary" name="isa_rsc_license_check" value="<?php _e('Check Status', 'rsc' ); ?>"/>
		
										<?php } else {
		if( empty( $status ) ) $status == 'not active'; ?>
				<span style="color:red;font-weight:bold;padding:12px;"><?php printf( __('Status: %s', 'rsc'), $status ); ?></span>
		
		<?php
										wp_nonce_field( 'isa_rsc_nonce', 'isa_rsc_nonce' ); ?>
											<input type="submit" class="button-secondary" name="isa_rsc_license_activate" value="<?php _e('Activate License', 'rsc' ); ?>"/>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>	
					<?php submit_button(); ?>
				
				</form>
			<?php
		}
		
		public function add_plugin_page(){
			add_options_page(__('Rising Sign Calculator', 'rsc'), __('Rising Sign Calculator', 'rsc'), 'manage_options', 'rsc', array($this, 'create_admin_page'));

			add_plugins_page( __( 'Rising Sign Calculator License', 'rsc' ), __( 'Rising Sign Calculator License', 'rsc' ), 'manage_options', 'rising-sign-calc-license', array($this, 'rsc_license_page') );// @new


	    }
	
		
		/**
		* Gets rid of the local license status option when adding a new one
		* @since 1.3
		*/

		public function isa_sanitize_license( $new ) {
			$old = get_option( 'isa_rsc_license_key' );
			if( $old && $old != $new ) {
				delete_option( 'isa_rsc_license_status' ); // new license has been entered, so must reactivate
			}
			return $new;
		}
		
		
		/**
		* Activate license key.
		* @since 1.3
		*/
		public function activate_license() {
		
			if( isset( $_POST['isa_rsc_license_activate'] ) ) {
		
				// run a quick security check 
			 	if( ! check_admin_referer( 'isa_rsc_nonce', 'isa_rsc_nonce' ) ) 	
					return; // get out if we didn't click the Activate button

				$license = trim( get_option( 'isa_rsc_license_key' ) );
		
				$api_params = array( 
					'edd_action'=> 'activate_license', 
					'license' 	=> $license, 
					'item_name' => urlencode( RISING_SIGN_CALC_DLNAME )
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, RSC_ISABEL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		
				if ( is_wp_error( $response ) )
					return false;
		
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				
				// $license_data->license will be either "active" or "inactive"
				update_option( 'isa_rsc_license_status', $license_data->license );
			}
		}

		/**
		* Deactivate a license key, allows user to transfer license to another site. 
		* @since 1.3
		*/
		
		public function deactivate_license() {
		
			if( isset( $_POST['isa_rsc_license_deactivate'] ) ) {
		
			 	if( ! check_admin_referer( 'isa_rsc_nonce', 'isa_rsc_nonce' ) ) 	
					return; // get out if we didn't click the Activate button
		
				$license = trim( get_option( 'isa_rsc_license_key' ) );
				$api_params = array( 
					'edd_action'=> 'deactivate_license', 
					'license' 	=> $license, 
					'item_name' => urlencode( RISING_SIGN_CALC_DLNAME )
				);
				
				$response = wp_remote_get( add_query_arg( $api_params, RSC_ISABEL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		
				if ( is_wp_error( $response ) )
					return false;
		
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' )
					delete_option( 'isa_rsc_license_status' );
		
			}
		}

		/**
		* Checks license status in options page when secondary button is clicked.
		* @return string expired/active/inactive
		* @since 1.3
		*/
		
		public function check_status() {
		
			if( isset( $_POST['isa_rsc_license_check'] ) ) {
		
				global $wp_version;
		
				$license = trim( get_option( 'isa_rsc_license_key' ) );
			
				$api_params = array(
					'edd_action' => 'check_license',
					'license' => $license,
					'item_name' => urlencode( RISING_SIGN_CALC_DLNAME )
				);
			
				$response = wp_remote_get( add_query_arg( $api_params, RSC_ISABEL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
			
				if ( is_wp_error( $response ) )
					return false;
			
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
				if( 'expired' == $license_data->license )
					$status = 'expired';
				elseif( 'valid' == $license_data->license )
					$status = 'active';
				else
					$status = 'inactive';
		
				update_option( 'isa_rsc_license_status', $status );
		
			}
		}

		public function create_admin_page(){
	        ?>
		<div class="wrap">
		    <?php screen_icon(); ?>
		    <h2><?php _e( 'Rising Sign Calculator - Custom Interpretations', 'rsc' ); ?></h2>			
		    <form method="post" action="options.php">
		        <?php
	                    // This prints out all hidden setting fields
					settings_fields('rsc_options');	// @param 1 must be same as register settings'
					do_settings_sections('rsc');// page slug must match 4th param of add_settings_section
			?>
		        <?php submit_button(); ?>
		    </form>
		</div>
		<?php
	    }
	
		public function register_options(){	

			register_setting( 'isa_rsc_license', 'isa_rsc_license_key', array( $this, 'isa_sanitize_license' ) );

			register_setting( 'rsc_options', 'rsc_options', array( $this, 'sanitize' ) );
			// @param 1 must be same as the group name in settings_fields'
			// 2nd param is name of the option, will be an array
			add_settings_section(
				'rsc_options_main',// unique id for the section
				__( 'Enter your custom interpretation for each Rising sign.', 'rsc' ),
				false,// function callback to display
				'rsc'// page name. Must match the do_settings_sections function call. and match options menu page
			);	
			
			$rsc_settings = array(
				
						'aries' => array(
										'id' => 'aries',
										'name' => __('Aries', 'rsc')// just capital sign
						),
			
						'taurus' => array(
										'id' => 'taurus',
										'name' => __('Taurus', 'rsc')
						),
			
						'gemini' => array(
										'id' => 'gemini',
										'name' => __('Gemini', 'rsc')
						),
			
						'cancer' => array(
										'id' => 'cancer',
										'name' => __('Cancer', 'rsc')
						),
			
						'leo' => array(
										'id' => 'leo',
										'name' => __('Leo', 'rsc')
						),
			
						'virgo' => array(
										'id' => 'virgo',
										'name' => __('Virgo', 'rsc')
						),
			
						'libra' => array(
										'id' => 'libra',
										'name' => __('Libra', 'rsc')
						),
			
						'scorpio' => array(
										'id' => 'scorpio',
										'name' => __('Scorpio', 'rsc')
						),
			
						'sagittarius' => array(
										'id' => 'sagittarius',
										'name' => __('Sagittarius', 'rsc'),
						),
			
						'capricorn' => array(
										'id' => 'capricorn',
										'name' => __('Capricorn', 'rsc')
						),
						'aquarius' => array(
										'id' => 'aquarius',
										'name' => __('Aquarius', 'rsc')
						),
			
						'pisces' => array(
										'id' => 'pisces',
										'name' => __('Pisces', 'rsc')
						),
			
			); // end $rsc_settings
			
			foreach($rsc_settings as $rsc_setting) {
					
				add_settings_field(
					$rsc_setting['id'], // unique id for the field
					sprintf(__( 'Ascendant in %s:', 'rsc' ), $rsc_setting['name'] ),
					array($this, 'rscci_textarea_callback'), // callback
					'rsc',// page name that this is attached to (same as the do_settings_sections)
					'rsc_options_main',	// the id of the settings section that this goes into (same as the first argument to add_settings_section).
					array( 
						'sign' => $rsc_setting['id']
					)
				);	
				
			} // end foreach
			
		} // end register_options
		
	
		public function rscci_textarea_callback($args){
	
			$options = get_option('rsc_options');
			
			if( isset( $options[ $args['sign'] ] ) ) { $value = $options[ $args['sign'] ]; } else {	$value = ''; }

			// name must start with the second argument passed to register_setting
		
?><textarea class="large-text" cols="50" rows="2" id='rsc_<?php echo $args['sign']; ?>' name='rsc_options[<?php echo $args['sign']; ?>]'><?php echo esc_textarea( $value ); ?></textarea><?php			
	
	    }

	    public function sanitize($input){
			return $input;
	    }
		/**
		* Load textdomain and set file permission
		*/	   	
		public function plugins_loaded() {
			load_plugin_textdomain( 'rsc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			
			$wantedPerms = 0755;

			$actualPerms = substr(sprintf('%o', fileperms(RSC_PLUGIN_DIR . 'sweph/isabelse')), -4);

 			if($actualPerms != $wantedPerms) {
				chmod(RSC_PLUGIN_DIR . 'sweph/isabelse', $wantedPerms);
			}

		}
		
		/** 
		 * Registers the widget.
		 * @since 1.0
		 */
		public function register_widgets() {
			register_widget( 'rsc_widget' );
		}

		/** 
		 * Shortcode to insert widget anywhere
		 * @since 1.0
		 */
		public function risingcalc_shortcode($atts) {
			// Configure defaults and extract the attributes into variables
			extract( shortcode_atts( 
				array( 
					'title'  => __('Rising Sign Calculator', 'rsc'),
				), 
				$atts 
			));
			ob_start();
			the_widget( 'rsc_widget', $atts ); 
			$output = ob_get_clean();
			return $output;
		}	
	}
}
$Rising_Sign_Calculator = new Rising_Sign_Calculator();
add_shortcode( 'risingcalc', array( $Rising_Sign_Calculator, 'risingcalc_shortcode' ) );


/** @todo delete 
 * Log my own debug messages
 */
function isa_log( $message ) {
    if (WP_DEBUG === true) {
        if ( is_array( $message) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}