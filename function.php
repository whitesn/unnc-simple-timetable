<?php
require_once 'function_timetable.php';
require_once 'function_roombalances.php';

function print_warning_no_input() { 
?>
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-danger" role="alert">
				<strong>Warning!</strong> You did not input anything on the previous page. Please <a href="index.php">click here</a> and try again.
			</div>
		</div>
	</div>
<?php
}

function print_back_to_home_button() {
?>
	<div class="row row-margin text-center">
			<div class="col-sm-5"></div>
			<div class="col-sm-2">
				<a href="index.php"><button type="button" class="btn btn-success">&laquo; Back to Home</button></a>
			</div>
			<div class="col-sm-5"></div>
	</div>
<?php
}

function grab_main_handler( $form_data ) {
	$student_id 		= (isset( $form_data['student-id'] )) ? $form_data['student-id'] : 0;
	$building_number	= (isset( $form_data['building-number'] )) ? $form_data['building-number'] : 0;
	$room_number		= (isset( $form_data['room-number'] )) ?  $form_data['room-number'] : 0;
	$is_requested_new_timetable = (isset( $form_data['new_timetable'] )) ? 1 : 0;
	
	if( $student_id ) {
		if ( is_cache_exists( $student_id ) && !$is_requested_new_timetable ) {			
			print_timetable_from_cache( $student_id );
		} else {
			print_new_timetable( $student_id );
		}
	}
	
	if( $building_number && $room_number )
		print_room_balance( $building_number, $room_number );
	
	if( ($student_id === False) && (($building_number === False) || ($room_number === False)) )
		print_warning_no_input();
	
	print_back_to_home_button();
}
?>