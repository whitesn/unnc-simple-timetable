<?php
require_once 'define.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_TITLE; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/timetable.css" rel="stylesheet">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  	<?php include_once 'header.php' ?>
	<div class="container theme-showcase" role="main">
	
		<form action="grab.php" method="GET">
			<div class="jumbotron">
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-info alert-dismissible" role="alert">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <strong>Note:</strong> Building Number and Room Number are optional, you don't have to input them to see timetable vice versa.
						</div>
					</div>
				</div>
			
				<div class="row row-margin">
					<div class="col-sm-3"></div>
					<div class="col-sm-6">
						<div class="input-group input-group-lg">
						  <span class="input-group-addon input-label-color" id="sizing-addon1">Student Number</span>
						  <input name="student-id" type="text" class="form-control" placeholder="6512345" aria-describedby="sizing-addon1">
						</div>
					</div>
					<div class="col-sm-3"></div>
				</div>
				
				<div class="row row-margin">
					<div class="col-sm-3"></div>
					<div class="col-sm-3">
						<div class="input-group">
						  <span class="input-group-addon input-label-color" id="sizing-addon2">Building Number</span>
						  <input name="building-number" type="text" class="form-control" placeholder="" aria-describedby="sizing-addon2">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
						  <span class="input-group-addon input-label-color" id="sizing-addon2">Room Number</span>
						  <input name="room-number" type="text" class="form-control" placeholder="" aria-describedby="sizing-addon2">
						</div>
					</div>
					<div class="col-sm-3"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-12 text-center"><button type="submit" class="btn btn-primary">Submit</button></div>
				</div>
				
				<hr />
				
				<div class="wechat-barcode">
					<div class="alert alert-info alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>Notice:</strong> I recently made a bot in WeChat to check Room Balances, try scanning the barcode below:
					</div>
					 
					<img src="images/QR_Small.jpg" width = "200px" />
				</div>
			</div>
		</form>
	</div>
	
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
