<?php

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
 */
function display_ir_college_dept_profiles() {

	ob_start();
	?>
	<div class="dropdown-container">
		<label for="drop_down_college">College:</label><select id="drop_down_college"></select><br/>
		<label for="drop_down_dept">Department:</label><select id="drop_down_dept"></select><br/>
		<label for="drop_down_campus">Campus:</label><select id="drop_down_campus"></select><br/>

		<input type="hidden" id="report_file_base_path" value="/files/profiles_startingFall2012/active_employee/" />
		<input type="hidden" id="report_data" value="<?php echo esc_url( get_stylesheet_directory_uri() . '/js/menu.json' ); ?>" />
		<input type="hidden" id="report_ext" value="xls" />

		<input type="button" id="drop_down_handler" value="Download Profile Report" />
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	wp_enqueue_script( 'ir-college-dropdown', get_stylesheet_directory_uri() . '/js/ir-dropdown.js', array( 'jquery' ), false, true );

	return $content;
}
