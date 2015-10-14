<?php
function parse_nottingham_timetabling_get_student_name( $string ) {
	$start_pos 	= strpos( $string, "Student: " ) + strlen( "Student: " );
	$length		= strpos( $string, "</td>", $start_pos ) - $start_pos;
	
	return substr( $string, $start_pos, $length );
}

function parse_nottingham_timetabling_get_timetable_period( $string ) {
	$start_pos	= strpos( $string, "Weeks:" );
	$length		= strpos( $string, "</td>", $start_pos ) - $start_pos;
	
	return substr( $string, $start_pos, $length );
}

function parse_nottingham_timetabling_get_timetable_html( $string ) {
	$start_pos	= strpos( $string, "<!-- END REPORT HEADER -->" ) + strlen( "<!-- END REPORT HEADER -->" );
	$length		= strpos( $string, "<!-- START REPORT FOOTER -->", $start_pos ) - $start_pos;
	
	$html = substr( $string, $start_pos, $length );
	
	$html = str_replace( "style=\"border-bottom:3px solid #000000;\"", "", $html);
	return $html;
}

function parse_nottingham_timetabling( $string ) {
	$data = array();
	
	$data['student_name']	 		  = parse_nottingham_timetabling_get_student_name( $string );
	$data['timetable_period'] 		  = parse_nottingham_timetabling_get_timetable_period( $string );
	$data['timetable_html']			  = parse_nottingham_timetabling_get_timetable_html( $string );
	$data['timetable_generated_time'] = date('d/m/Y h:i:s a', time());;
	
	return json_encode( $data );
}

function is_student_id_does_not_exist( $string ) {
	return (strpos ($string, "Cannot Find")) !== False;
}

function is_cache_exists( $student_id ) {
	return file_exists( TIMETABLE_CACHE_DIR . "$student_id.cache" );
}

function save_timetable_cache( $data, $student_id ) {
	if ( !file_exists( 'timetables-cache' ) ) {
		mkdir( 'timetables-cache', 0755, true );
	}
	
	$fp = fopen( TIMETABLE_CACHE_DIR . "$student_id.cache", "w" ) or die( "Something went wrong, please contact the administrator!" );
	fwrite( $fp, $data );
	fclose( $fp );
}

function decode_and_print_json_timetable( $data ) {
	$data = json_decode( $data, TRUE );
?>
	<div class="alert alert-success" role="alert">
		<span>
			 Today is <?php echo date ("l, F dS") . " &raquo; Week : " . get_current_teaching_week(); ?> <br />
			 Howdy <strong><?php echo $data['student_name'] ?></strong> | Timetable for <?php echo $data['timetable_period']; ?>
		</span>
	</div>
	
	<div class="generated-timetable-container">
<?php
		echo $data['timetable_html'];
?>
	</div>
	
	<!--
	<div class="row row-margin text-center">
		<div class="col-sm-5"></div>
		<div class="col-sm-2"><a href="download.php?id=">
			<button type="button" class="btn btn-success">Download as JPEG</button></a>
		</div>
		<div class="col-sm-5"></div>
	</div>
	-->
<?php
}

function get_current_teaching_week() {
	$current_year = date( "y", time() );
	$base_date 	  = strtotime( $current_year . "-" . BASE_TIME_FALL_SEMESTER + ACADEMIC_WEEK_ADDITION );
	$current_date = time();
	$days_difference = floor( ( $current_date - $base_date ) / ( 60 * 60 * 24 ) );
	$current_teaching_week = ( floor( $days_difference / 7 ) <= 0 ) ? "- (Teaching week starts on September 15th)" : ( floor( $days_difference / 7 ) );
	return $current_teaching_week;
}

function get_timetable_week_based_on_time() {
	$current_year = date( "y", time() );
	$base_date 	  = strtotime( $current_year . "-" . BASE_TIME_FALL_SEMESTER + ACADEMIC_WEEK_ADDITION );
	$current_date = time();
	$days_difference = floor( ( $current_date - $base_date ) / ( 60 * 60 * 24 ) );
	
	if ( $days_difference <= ( TOTAL_WEEKS_FALL_SEMESTER * 7 ) ) {
		return "1-15"; // Fall Semester
	} else {
		return "21-36"; // Spring Semester
	}
}

function get_generate_date( $data ) {
	$data = json_decode( $data, TRUE );
	return $data['timetable_generated_time'];
}

function print_new_timetable( $student_id ) {
	$ch = curl_init();
	
	$weeks = get_timetable_week_based_on_time();
	
	curl_setopt( $ch, CURLOPT_URL, "http://timetablingunnc.nottingham.ac.uk:8005/individual.htm;Students;id;".$student_id."?template=SWSCUST+Student+Individual&weeks=$weeks&days=1-5&periods=3-20&Width=0&Height=0" );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt( $ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$return = curl_exec( $ch );
	
	if ( is_student_id_does_not_exist( $return ) ) {
		print_warning_id_does_not_exist( $student_id );	
	} else {	
		$data = parse_nottingham_timetabling( $return );
		save_timetable_cache( $data, $student_id );
		print_message_new_timetable();
		decode_and_print_json_timetable( $data );
	}
}

function print_timetable_from_cache( $student_id ) {
	$data 			= file_get_contents( TIMETABLE_CACHE_DIR . "$student_id.cache" );
	$generate_date	= get_generate_date( $data );
	$generate_date  = DateTime::createFromFormat( 'd/m/Y h:i:s a', $generate_date) -> format('F jS Y, h:i a');

	print_message_cached_timetable( $student_id, $generate_date );
	decode_and_print_json_timetable( $data );
}

function print_message_new_timetable() {
?>
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-success" role="alert">
				<strong>Success!</strong> This is a freshly taken timetable, enjoy!
			</div>
		</div>
	</div>
<?php
}

function print_message_cached_timetable( $student_id, $generate_date ) {
?>
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-warning" role="alert">
				<strong>Notice!</strong> This is cached timetable generated on [<?php echo $generate_date ?>], if you wish to get a new one, please click <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&new_timetable=1" class="alert-link">here</a>.
			</div>
		</div>
	</div>
<?php
}

function print_warning_id_does_not_exist( $student_id ) { 
?>
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-danger" role="alert">
				<strong>Warning!</strong> The Student ID: <?php echo $student_id; ?> does not exist. Please <a href="index.php" class="alert-link">click here</a> and try again.
			</div>
		</div>
	</div>
<?php
}
?>