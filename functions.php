<?php

add_action( 'init', 'ir_register_extra_menus' );
/**
 * Registers additional portions of the main Spine menu.
 */
function ir_register_extra_menus() {
	register_nav_menus( array( 'site-restricted' => 'Restricted Pages', 'site-bottom' => 'Spine Bottom' ) );
}

add_action( 'init', 'ir_college_dept_profiles_shortcode' );
/**
 * Sets up the shortcode used to display a list of college department profiles.
 */
function ir_college_dept_profiles_shortcode() {
	add_shortcode( 'ir_college_dept_profiles', 'display_ir_college_dept_profiles' );
}

/**
 * Displays the HTML markup used in combination with JavaScript to select from
 * a list of colleges, departments, and campuses to download a profile report.
 *
 * @param array $atts
 */
function display_ir_college_dept_profiles( $atts ) {
	if ( ! isset( $atts['json_url'] ) ) {
		$json_url = get_stylesheet_directory_uri() . '/js/menu.json';
	} else {
		$json_url = $atts['json_url'];
	}

	ob_start();
	?>
	<div class="dropdown-container">
		<label for="drop_down_college">College:</label><select id="drop_down_college"></select><br/>
		<label for="drop_down_dept">Department:</label><select id="drop_down_dept"></select><br/>
		<label for="drop_down_campus">Campus:</label><select id="drop_down_campus"></select><br/>

		<input type="hidden" id="report_file_base_path" value="/files/profiles_startingFall2012/active_employee/" />
		<input type="hidden" id="report_data" value="<?php echo esc_url( $json_url ); ?>" />
		<input type="hidden" id="report_ext" value="xls" />

		<input type="button" id="drop_down_handler" value="Download Profile Report" />
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	wp_enqueue_script( 'ir-college-dropdown', get_stylesheet_directory_uri() . '/js/ir-dropdown.js', array( 'jquery' ), false, true );

	return $content;
}

add_filter( 'pre_site_option_upload_filetypes', 'ir_set_upload_filetypes', 99, 1 );
/**
 * Filters the upload_filetypes option to allow for JSON uploads on this site.
 *
 * @param string $option
 *
 * @return string
 */
function ir_set_upload_filetypes( $option ) {
	$option .= ' json';

	return $option;
}

add_filter( 'upload_mimes', 'ir_set_upload_mimes', 99, 1 );
/**
 * Filters the recognized mime types to allow for JSON uploads.
 *
 * @param array $mime_types
 *
 * @return array
 */
function ir_set_upload_mimes( $mime_types ) {
	$mime_types['json'] = 'application/json';

	return $mime_types;
}

add_action( 'wsuwp_sso_set_authentication', 'ir_set_auth_cookies', 10, 2 );
/**
 * Sets an additional cookie containing the user's AD group information
 * after a successful authentication.
 *
 * @param WP_User $user
 * @param array   $user_ad_data
 */
function ir_set_auth_cookies( $user, $user_ad_data ) {
	if ( ! isset( $user_ad_data['memberof'] ) || empty( $user_ad_data['memberof'] ) ) {
		return;
	}

	$user_ad_groups = implode( ',', $user_ad_data['memberof'] );
	$expire = time() + 1 * DAY_IN_SECONDS;
	$secure = is_ssl();

	setcookie( 'ir_ad_groups', $user_ad_groups, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true);
}
