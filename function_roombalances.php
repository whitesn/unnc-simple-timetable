<?php
function is_room_exist( $string ) {
	return strpos( $string, "<script>" ) === False;
}

function get_moon_ship_balance ( $string, $table_header ) {
	$start_pos = strpos( $string, $table_header );
	$start_pos = strpos( $string, "<td align=\"center\" bgcolor=\"#E8E8E8\">", $start_pos) + strlen( "<td align=\"center\" bgcolor=\"#E8E8E8\">" );
	$start_pos = strpos( $string, "<td align=\"center\" bgcolor=\"#E8E8E8\">", $start_pos) + strlen( "<td align=\"center\" bgcolor=\"#E8E8E8\">" );
	$len   	   = strpos( $string, "</td>", $start_pos) - $start_pos;
	
	$balance = substr( $string, $start_pos, $len );
	return preg_replace('/\s+/', '', $balance);
}

function get_cold_water_balance( $string ) {
	return get_moon_ship_balance( $string, "water meter" );
}

function get_hot_water_balance( $string ) {
	return get_moon_ship_balance( $string, "hot water meter" );
}

function get_electricity_balance( $string ) {
	return get_moon_ship_balance( $string, "ammeter" );
}


function parse_balance_result( $string ) {
	$data['cold_water'] 	= get_cold_water_balance( $string );
	$data['hot_water'] 		= get_hot_water_balance( $string );
	$data['electricity'] 	= get_electricity_balance( $string );
	
	return $data;
}

function get_single_balance_statuses( $data, $index ) {
	
	if( $data[$index] <= BALANCE_CRIT ) {
		return "balance-crit";
	} else if ( $data[$index] <= BALANCE_LOW ) {
		return "balance-low";
	}
	
	return "balance-normal";
}

function get_balance_statuses( $data ) {
	$status['cold_water'] = get_single_balance_statuses( $data, 'cold_water' );
	$status['hot_water'] = get_single_balance_statuses( $data, 'hot_water' );
	$status['electricity'] = get_single_balance_statuses( $data, 'electricity' );
	
	return $status;
}

function print_room_balance( $building_number, $room_number ) {
	$post_data['build'] = $building_number;
	$post_data['room'] = $room_number;

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, "http://60.190.19.138:7080/stu/sel_result.jsp" );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt( $ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec( $ch );
	
	if ( !is_room_exist( $result ) ) {
		?>
			<div class="row row-margin">
				<div class="col-sm-12">
					<div class="alert alert-danger" role="alert">
						<strong>Warning!</strong> Either the Building Number or Room Number does not exist, please try again <a href="index.php" class="alert-link">here</a>.
					</div>
				</div>
			</div>
		<?php
	} else {
		$data = parse_balance_result( $result );
		$status = get_balance_statuses( $data );
		?>
		<div class="room-balance-row">
			<div class="building-room-number-label row text-center">
				<div class="col-sm-12">
					<h3><span class="label label-default">Building <?php echo $building_number . " # " . $room_number; ?></span></h3>
				</div>
			</div>
			
			<div class="row-margin row text-center">
				<div class="col-sm-1"></div>
				<div class="col-sm-2">
					<div class="input-group">
					  <span class="input-group-addon <?php echo $status['cold_water'] ?>" id="sizing-addon3"><span class="glyphicon glyphicon-tint" aria-hidden="true"></span></span>
					  <input type="text" class="form-control text-center" placeholder="<?php echo $data['cold_water']; ?>" aria-describedby="sizing-addon3" readonly />
					</div>
				</div>
				
				<div class="col-sm-2"></div>
				
				<div class="col-sm-2">
					<div class="input-group">
					  <span class="input-group-addon <?php echo $status['hot_water'] ?>" id="sizing-addon1"><span class="glyphicon glyphicon-fire" aria-hidden="true"></span></span>
					  <input type="text" class="form-control text-center" placeholder="<?php echo $data['hot_water']; ?>" aria-describedby="sizing-addon1" readonly />
					</div>
				</div>
				
				<div class="col-sm-2"></div>
				
				<div class="col-sm-2">
					<div class="input-group">
					  <span class="input-group-addon <?php echo $status['electricity'] ?>" id="sizing-addon1"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span></span>
					  <input type="text" class="form-control text-center" placeholder="<?php echo $data['electricity']; ?>" aria-describedby="sizing-addon1" readonly />
					</div>
				</div>
				<div class="col-sm-1"></div>
			</div>
		</div>
		<?php
	}
}
?>