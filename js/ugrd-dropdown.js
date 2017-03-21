( function( $ ) {
	$( document ).ready( function() {
		var json_url = $( "#report_data" ).val();
		var report_suffix = $( "#report_suffix" ).val();
		var container = $( ".dropdown-container" );
		var area = $( "#drop_down_area" );
		var program = $( "#drop_down_program" );
		var file = "";

		var menu_data = [];

		$.getJSON( json_url, {}, function( data ) {
			$.each( data.rows, function( key, val ) {
				if ( "Y" !== val.LIST ) {
					return;
				}

				if ( "undefined" === typeof menu_data[ val.area_name ] ) {
					menu_data[ val.area_name ] = [];
					area.append( $( "<option>", { value: val.area_name, text: val.area_name } ) );
				}

				menu_data[ val.area_name ][ val.retention_prog_descr ] = { code: val.Retention_Code, area: val.area, level: val.profile_level };
			} );
		} );

		container.on( "change", "#drop_down_area", function( el ) {
			// Clear all existing choices.
			program.html( "" );

			if ( "undefined" === typeof menu_data[ el.target.value ] ) {
				return;
			}

			program.append( $( "<option>", { value: "", text: "--- Select Program ---" } ) );

			for ( var key in menu_data[ el.target.value ] ) {
				program.append( $( "<option>", { value: key, text: key } ) );
			}
		} );

		container.on( "change", "#drop_down_program", function( el ) {
			var retention_code = ( "0000" + menu_data[ area.val() ][ el.target.value ].code ).slice( -4 );
			var area_code = menu_data[ area.val() ][ el.target.value ].area;
			var profile_level = menu_data[ area.val() ][ el.target.value ].level;

			file = profile_level + "_" + area_code + "_" + retention_code + report_suffix + ".xls";
		} );

		container.on( "click", "#drop_down_handler", function( el ) {
			if ( "" === file ) {
				alert( "Please select from the above options." );
				return;
			}

			window.location = $( "#report_file_base_path" ).val() + file;
		} );
	} );
}( jQuery ) );
