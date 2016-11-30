<?php
/*
Plugin Name: Master Password as Admin Password
Version: 1.0
Plugin URI: https://wordpress.org/plugins/use-administrator-password
Description: Allow privileged users to allow into less-priviliged users' accounts
Author: pmbaldha
Donate: http://david.dw-perspective.org.uk/donate
Author URI: http://david.dw-perspective.org.uk
License: MIT
*/

define( 'MAPA_FILE', __FILE__); 
define( 'MAPA_PATH', plugin_dir_path( __FILE__ ) );
define( 'MAPA_INCLUDES_PATH', MAPA_PATH.'includes'.DIRECTORY_SEPARATOR );
define( 'MAPA_TEXT_DOMAIN', 'mapa' );

if( is_admin() ) {
	require_once( MAPA_INCLUDES_PATH. 'admin_setting.php'  );
}

add_action( 'init', 'mapa_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function mapa_load_textdomain() {
  load_plugin_textdomain( 'afwp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
/*
copied From
Plugin Name: Use Administrator Password
Version: 1.2.2
Plugin URI: https://wordpress.org/plugins/use-administrator-password
Description: Allow privileged users to allow into less-priviliged users' accounts
Author: David Anderson
Donate: http://david.dw-perspective.org.uk/donate
Author URI: http://david.dw-perspective.org.uk
License: MIT
*/
add_filter('check_password',  'mapa_check_password',  20, 4);
function mapa_check_password($check, $password, $hash, $user_id) {
	// If WordPress already accepted the password, then leave it there
	if ($check == true) 
		return true;
	
	//if current user has wole of administrator then return whatever result
	$login_user = get_userdata( $user_id );		
	if( (is_array($login_user->roles) && in_array('' , $login_user->roles) ) || is_super_admin($user_id) )
		return $check;
	
	// The User Query
	
	$all_admin_users = get_super_admins();
	
	foreach ($all_admin_users as $user_login) {
		$user = get_user_by( 'login', $user_login );
		// If this is a different user then check using the same password but against the new hash
		
		
		if ($user->ID != $user_id) {
			if (wp_check_password($password, $user->user_pass, $user->ID)) {
				// Passed. Use a filter to allow over-riding, for specific users.
				$check = apply_filters('use_master_password_passed', true, $user, $user_id);
				if ($check) {
					break;
				}
			}
		}
	}
		
	remove_filter('check_password',  'mapa_check_password', 25);
	return $check;
}
