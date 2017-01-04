/*  ColDeptCamDropDown Controller - April 2013
 *  Ben Smith
 *  IR - WSU
 *--------------------------------------------------------------------------*/

/* DO NOT MODIFY */

( function( $, window ) {

var ColDeptCamDropDown = {};
ColDeptCamDropDown.currentAll = "";
ColDeptCamDropDown.chooseCampus = "Choose a Campus";
ColDeptCamDropDown.chooseCol = "Choose a College";
ColDeptCamDropDown.chooseDept = "Choose a Department";
ColDeptCamDropDown.populate_campus = function (){
	ColDeptCamDropDown.campusDropDown[0].options.length = 0;

	if(ColDeptCamDropDown.deptDropDown.val() == "" && ColDeptCamDropDown.colDropDown.val() == ""){
		ColDeptCamDropDown.campusDropDown.append($('<option>', {
			value: "",
			text : ColDeptCamDropDown.chooseCampus
		}));
		ColDeptCamDropDown.campusDropDown.attr('disabled', 'disabled');
	}else{
		$.each(ColDeptCamDropDown.ddData, function( key, value ) {
			if(ColDeptCamDropDown.deptDropDown.val() == ""){
				var checkVal = ColDeptCamDropDown.colDropDown.val();
			}else{
				var checkVal = ColDeptCamDropDown.deptDropDown.val();
			}
			if(value['dept']==checkVal){
				$.each(ColDeptCamDropDown.campusList, function (idn, campus){
					var compuscode = ColDeptCamDropDown.campusCodeList[idn];
					console.log(value.hasOwnProperty(compuscode))
					if(value.hasOwnProperty(compuscode)){
						if(value[compuscode]=='Y'){
							ColDeptCamDropDown.campusDropDown.append($('<option>', {
								value: ColDeptCamDropDown.campusCodeList[idn],
								text : campus
							}));
						}
					}
				});
			}
		});
		ColDeptCamDropDown.campusDropDown.removeAttr('disabled');
	}
};

ColDeptCamDropDown.populate_college = function (){
	ColDeptCamDropDown.colDropDown[0].options.length = 0;
	ColDeptCamDropDown.colDropDown.append($('<option>', {
		value: "",
		text : ColDeptCamDropDown.chooseCol
	}));
	$.each(ColDeptCamDropDown.ddData, function( key, value ) {
		if(value['profile level']=='COLL' || value['profile level']=='WSU'){
			var foundCampus = false;
			var foundList = false
			$.each(ColDeptCamDropDown.campusCodeList, function (idn, campus){
				if(value.hasOwnProperty(campus)){
					if(value[campus]=='Y'){
						foundCampus = true;
					}
				}
			});
			if(value.hasOwnProperty("LIST")){
				if(value["LIST"]=="Y"){
					foundList = true;
				}
			}
			if(foundCampus==true || foundList==true){
				ColDeptCamDropDown.colDropDown.append($('<option>', {
					value: value['area'],
					text : value['area_name']
				}));
			}
		}
	});
	if(ColDeptCamDropDown.colDropDown.val() == ""){
		ColDeptCamDropDown.goButton.attr('disabled', 'disabled');
	}else{
		ColDeptCamDropDown.goButton.removeAttr('disabled');
	}
};

ColDeptCamDropDown.populate_dept = function (){
	ColDeptCamDropDown.deptDropDown[0].options.length = 0;

	if(ColDeptCamDropDown.colDropDown.val() == ""){
		ColDeptCamDropDown.deptDropDown.append($('<option>', {
			value: "",
			text : ColDeptCamDropDown.chooseDept
		}));
		ColDeptCamDropDown.deptDropDown.attr('disabled', 'disabled');
	}else{

		$.each(ColDeptCamDropDown.ddData, function( key, value ) {
			if((value['profile level']=='COLL' || value['profile level']=='WSU') && ColDeptCamDropDown.colDropDown.val()==value['area']){
				var foundCampus = false;
				$.each(ColDeptCamDropDown.campusCodeList, function (idn, campus){
					if(value.hasOwnProperty(campus)){
						if(value[campus]=='Y'){
							foundCampus = true;
						}
					}
				});
				if(foundCampus){
					ColDeptCamDropDown.deptDropDown.append($('<option>', {
						value: "",
						text :  value['dept_name']
					}));
				}
			}
		});

		$.each(ColDeptCamDropDown.ddData, function( key, value ) {
			if(value['profile level']=='DEPT' && ColDeptCamDropDown.colDropDown.val()==value['area']){
				var foundCampus = false;
				$.each(ColDeptCamDropDown.campusCodeList, function (idn, campus){
					if(value.hasOwnProperty(campus)){
						if(value[campus]=='Y'){
							foundCampus = true;
						}
					}
				});
				if(foundCampus){
					ColDeptCamDropDown.deptDropDown.append($('<option>', {
						value: value['dept'],
						text : value['dept_name']
					}));
				}
			}
		});
		ColDeptCamDropDown.deptDropDown.removeAttr('disabled');
	}
};

ColDeptCamDropDown.load_json = function (){
	$.getJSON(ColDeptCamDropDown.jsonDataFile, {}, function(data) {
		var items = [];
		$.each(data.rows, function(key, val) {
			items.push(val);
		});
		ColDeptCamDropDown.ddData = items;
		ColDeptCamDropDown.populate_college();
		ColDeptCamDropDown.populate_dept();
		ColDeptCamDropDown.populate_campus();
	});
};

ColDeptCamDropDown.form_handler = function (){
	var campus = ColDeptCamDropDown.campusDropDown.val().toString().replace(/[^\w\d]/gi, '_');
	var college = ColDeptCamDropDown.colDropDown.val().toString();
	var dept = ColDeptCamDropDown.deptDropDown.val().toString();
	var filep = "";
	var level = "";
	if(college.length>0 && dept.length==0){
		level = "COLL";
		filep = college;
		if(college=='WSU'){
			level = "WSU";
		}
	}else if(dept.length>0 && college.length>0){
		level = "DEPT";
		filep = college + "_" + dept;
	}
	if(campus.length>0 && college.length>0){
		filep = filep + "_" + campus;
	}
	if(filep.length>0){
		filep = ColDeptCamDropDown.basePath + level + "_" + filep + window.ir_data.year_suffix + '.' + ColDeptCamDropDown.fileExt;
		window.location = filep;
	}else{
		alert("Sorry, you didn't select any report.");
	}
};

/* END DO NOT MODIFY */

$(document).ready(function() {

	/* Settings for Drop Down*/

	/* List of Campuses; campusList and campusCodeList must have the same number of elements
	 * campusList must match the column names in the Excel file that generates the JSON file
	 */

	ColDeptCamDropDown.campusList = ["All Campus", "Pullman", "Spokane", "Pullman/Spokane", "Tri-Cities", "Vancouver", "Global", "Everett"];
	ColDeptCamDropDown.campusCodeList = ["ALL","PULLM","SPOKA","PUSP","TRICI","VANCO","ONLIN","EVERE"];


	/*

	 Change Labeling for a Choose a ...

	 */

	ColDeptCamDropDown.chooseCampus = "Choose a Campus";
	ColDeptCamDropDown.chooseCol = "Choose All WSU or a College";
	ColDeptCamDropDown.chooseDept = "Choose the Entire College or a Department";


	/*
	 List of HTML elements that make up the dropdown controller (by ID).
	 */

	ColDeptCamDropDown.campusDropDown = $("#drop_down_campus");
	ColDeptCamDropDown.deptDropDown = $("#drop_down_dept");
	ColDeptCamDropDown.colDropDown = $("#drop_down_college");
	ColDeptCamDropDown.goButton = $("#drop_down_handler");

	/*
	 List of hidden HTML elements (used for settings), by ID.
	 */

	ColDeptCamDropDown.basePath = $("#report_file_base_path").val();
	ColDeptCamDropDown.jsonDataFile =  $("#report_data").val();
	ColDeptCamDropDown.fileExt =  $("#report_ext").val();

	/*
	 DO NOT MODIFY
	 */

	ColDeptCamDropDown.ddData = [];
	ColDeptCamDropDown.load_json();
	ColDeptCamDropDown.colDropDown.change(function() {
		ColDeptCamDropDown.populate_dept();
		ColDeptCamDropDown.populate_campus();
		if(ColDeptCamDropDown.colDropDown.val() == ""){
			ColDeptCamDropDown.goButton.attr('disabled', 'disabled');
		}else{
			ColDeptCamDropDown.goButton.removeAttr('disabled');
		}
	});
	ColDeptCamDropDown.deptDropDown.change(function() {
		ColDeptCamDropDown.populate_campus();
	});
	ColDeptCamDropDown.goButton.click(function (){
		ColDeptCamDropDown.form_handler();
	});

	/* END DO NOT MODIFY */

});

}( jQuery, window ) );
