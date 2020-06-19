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
 * Sets up shortcodes used by the theme.
 */
function ir_college_dept_profiles_shortcode() {
	add_shortcode( 'ir_ugrd_retention_reports', 'display_ir_ugrd_retention_reports' );
	add_shortcode( 'ir_college_dept_profiles', 'display_ir_college_dept_profiles' );
}

/**
 * Displays a list of selections to generate a URGD retention report.
 *
 * @since 0.0.4
 *
 * @param array $atts
 *
 * @return string
 */
function display_ir_ugrd_retention_reports( $atts ) {
	if ( ! isset( $atts['json_url'] ) ) {
		$json_url = get_stylesheet_directory_uri() . '/js/ugrd-retention-menu.json';
	} else {
		$json_url = $atts['json_url'];
	}

	if ( ! isset( $atts['suffix'] ) ) {
		$suffix = '_16';
	} else {
		$suffix = '_' . absint( $atts['suffix'] );
	}

	if ( ! isset( $atts['file_base_path'] ) ) {
		$file_base_path = 'UGRD_retention_by_program/';
	} else {
		$file_base_path = $atts['file_base_path'];
	}

	ob_start();
	?>
	<div class="dropdown-container">
		<label for="drop_down_area">College or Interdisciplinary School:</label><select id="drop_down_area"><option value="">--- Select College or Interdisciplinary School ---</option></select><br/>
		<label for="drop_down_program">Program:</label><select id="drop_down_program"></select><br/>
		<label for="drop_down_campus">Campus:</label><select id="drop_down_campus"></select><br />

		<input type="hidden" id="report_file_base_path" value="/files/<?php echo esc_attr( $file_base_path ); ?>" />
		<input type="hidden" id="report_data" value="<?php echo esc_url( $json_url ); ?>" />
		<input type="hidden" id="report_suffix" value="<?php echo esc_attr( $suffix ); ?>" />

		<input type="button" id="drop_down_handler" value="Download Retention Report" />
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	wp_enqueue_script( 'ir-ugrd-dropdown', get_stylesheet_directory_uri() . '/js/ugrd-dropdown.js', array( 'jquery' ), '0.0.8', true );
	wp_localize_script( 'ir-ugrd-dropdown', 'ir_data', array( 'year_suffix' => $suffix ) );

	return $content;
}

/**
 * Displays the HTML markup used in combination with JavaScript to select from
 * a list of colleges, departments, and campuses to download a profile report.
 *
 * @param array $atts
 *
 * @return string
 */
function display_ir_college_dept_profiles( $atts ) {
	if ( ! isset( $atts['json_url'] ) ) {
		$json_url = get_stylesheet_directory_uri() . '/js/menu.json';
	} else {
		$json_url = $atts['json_url'];
	}

	if ( ! isset( $atts['suffix'] ) ) {
		$suffix = '';
	} else {
		$suffix = '_' . absint( $atts['suffix'] );
	}

	if ( ! isset( $atts['file_base_path'] ) ) {
	    $file_base_path = 'profiles_startingFall2012/active_employee/';
	} else {
	    $file_base_path = $atts['file_base_path'];
	}
	ob_start();
	?>
	<div class="dropdown-container">
		<label for="drop_down_college">College:</label><select id="drop_down_college"></select><br/>
		<label for="drop_down_dept">Department:</label><select id="drop_down_dept"></select><br/>
		<label for="drop_down_campus">Campus:</label><select id="drop_down_campus"></select><br/>

		<input type="hidden" id="report_file_base_path" value="/files/<?php echo esc_attr( $file_base_path ); ?>" />
		<input type="hidden" id="report_data" value="<?php echo esc_url( $json_url ); ?>" />
		<input type="hidden" id="report_ext" value="xls" />

		<input type="button" id="drop_down_handler" value="Download Profile Report" />
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	wp_enqueue_script( 'ir-college-dropdown', get_stylesheet_directory_uri() . '/js/ir-dropdown.js', array( 'jquery' ), '1.0.0', true );
	wp_localize_script( 'ir-college-dropdown', 'ir_data', array( 'year_suffix' => $suffix ) );

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
 * Set an additional cookie after successful authentication containing the AD groups
 * a user belongs to that are required to view certain content on ir.wsu.edu.
 *
 * @param WP_User $user
 * @param array   $user_ad_data
 */
function ir_set_auth_cookies( $user, $user_ad_data ) {
	if ( ! isset( $user_ad_data['memberof'] ) || empty( $user_ad_data['memberof'] ) ) {
		return;
	}

	// List of AD groups used to restrict IR content.
	$ir_ad_groups = array(
		'IR_Managers',
		'IR_Web_Users',
		'IR_Admin',
		'HRS_Appointing_Authority',
		'Employees.Active.Courtesy',
		'Employees.Active.Assistantship',
		'Employees.Active.AdministrativeProfessional',
		'Employees.Active.Faculty',
		'Employees.Active.Hourly',
		'Employees.Active.CivilService',
	);

	$user_ad_groups = array();
	foreach ( $user_ad_data['memberof'] as $group ) {
		if ( in_array( $group, $ir_ad_groups, true ) ) {
			$user_ad_groups[] = $group;
		}
	}

	$user_ad_groups = implode( ',', $user_ad_groups );
	$expire = time() + 1 * DAY_IN_SECONDS;
	$secure = is_ssl();

	setcookie( 'ir_ad_groups', $user_ad_groups, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
}

add_filter( 'content_visibility_default_groups', 'ir_add_visibility_groups', 99 );
/**
 * Extends the list of groups offered in the Content Visibility plugin.
 *
 * @param array $groups
 *
 * @return array
 */
function ir_add_visibility_groups( $groups ) {
	$ir_groups = array(
		array( 'id' => 'ir-managers', 'name' => 'IR Managers' ),
		array( 'id' => 'ir-web-users', 'name' => 'IR Web Users' ),
		array( 'id' => 'ir-admin', 'name' => 'IR Admin' ),
		array( 'id' => 'ir-hrs-aa', 'name' => 'HRS Appointing Authority' ),
	);
	$groups = array_merge( $groups, $ir_groups );

	return $groups;
}

add_filter( 'user_in_content_visibility_groups', 'ir_process_content_visibility_permissions', 10, 3 );
/**
 * Determines if a given user is a member of the passed groups.
 *
 * @param bool  $allowed Whether the user is associated with the passed groups.
 * @param int   $user_id ID of the user.
 * @param array $groups  List of groups to check the user against.
 *
 * @return bool False if the user is not a group member. True if the user is.
 */
function ir_process_content_visibility_permissions( $allowed, $user_id, $groups ) {
	// Don't override a previous success.
	if ( true === $allowed ) {
		return $allowed;
	}

	// The WSUWP SSO Authentication plugin is required for this check to work.
	if ( ! class_exists( 'WSUWP_SSO_Authentication' ) ) {
		return $allowed;
	}

	if ( 'nid' !== WSUWP_SSO_Authentication()->get_user_type( $user_id ) ) {
		return $allowed;
	}

	// Skip any further checking if AD is disabled locally.
	if ( defined( 'WSU_LOCAL_CONFIG' ) && WSU_LOCAL_CONFIG && false === apply_filters( 'wsuwp_sso_force_local_ad', false ) ) {
		return $allowed;
	}

	$user = new WP_User( $user_id );

	$user_ad_data = WSUWP_SSO_Authentication()->refresh_user_data( $user );

	if ( in_array( 'ir-managers', $groups, true ) && in_array( 'IR_Managers', $user_ad_data['memberof'], true ) ) {
		return true;
	}

	if ( in_array( 'ir-web-users', $groups, true ) && in_array( 'IR_Web_Users', $user_ad_data['memberof'], true ) ) {
		return true;
	}

	if ( in_array( 'ir-admin', $groups, true ) && in_array( 'IR_Admin', $user_ad_data['memberof'], true ) ) {
		return true;
	}

	if ( in_array( 'ir-hrs-aa', $groups, true ) && in_array( 'HRS_Appointing_Authority', $user_ad_data['memberof'], true ) ) {
		return true;
	}

	return $allowed;
}
