( function( $ ) {
	$( document ).ready( function() {
		var json_url = $( "#report_data" ).val();
		var report_suffix = $( "#report_suffix" ).val();
		var container = $( ".dropdown-container" );
		var area = $( "#drop_down_area" );
		var program = $( "#drop_down_program" );
		var campus = $( "#drop_down_campus" );
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

				menu_data[ val.area_name ][ val.retention_prog_descr ] = {
					code: val.retention_code,
					area: val.area,
					level: val.profile_level,
					campus: {
						"ALL": { value: val.ALL, text: "All Campuses" },
						"PULLM": { value: val.PULLM, text: "Pullman" },
						"SPOKA": { value: val.SPOKA, text: "Spokane" },
						"TRICI": { value: val.TRICI, text: "Tricities" },
						"VANCO": { value: val.VANCO, text: "Vancouver" },
						"ONLIN": { value: val.ONLIN, text: "Online" },
						"EVERE": { value: val.EVERE, text: "Everett" }
					}
				};
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

			// Clear all existing choices.
			campus.html( "" );
			campus.append( $( "<option>", { value: "", text: "--- Select Campus ---" } ) );

			for ( var key in menu_data[ area.val() ][ el.target.value ].campus ) {
				if ( "Y" === menu_data[ area.val() ][ el.target.value ].campus[ key ].value ) {
					campus.append( $( "<option>", {
						value: key,
						text: menu_data[ area.val() ][ el.target.value ].campus[ key ].text
					} ) );
				}
			}
		} );

		container.on( "change", "#drop_down_campus", function( el ) {
			var retention_code = ( "0000" + menu_data[ area.val() ][ program.val() ].code ).slice( -4 );
			var area_code = menu_data[ area.val() ][ program.val() ].area;
			var profile_level = menu_data[ area.val() ][ program.val() ].level;
			var campus_code = el.target.value;

			file = profile_level + "_" + area_code + "_" + retention_code + "_" + campus_code + report_suffix + ".xls";
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
