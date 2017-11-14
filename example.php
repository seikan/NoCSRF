<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>NoCSRF</title>

		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet">
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

		<style>
			body{margin-top:50px;margin-bottom:200px}
		</style>
	</head>

	<body>
		<div class="container">
			<h1>User Details</h1>

			<div class="row">
				<div class="col-md-6">
					<form method="post">
						<?php
						// Include core libray
						require_once 'class.NoCSRF.php';

						// Initialize NoCSRF with origin checking, and token expire after 30 minutes
						$csrf = new NoCSRF([
							'lock_ip' => true,
							'timer'   => 1800,
						]);

						// $csrf->deleteToken();

						if (isset($_POST['name'])) {
							if ($csrf->validate() != NoCSRF::PASSED) {
								echo '<div class="alert alert-danger">CSRF detected.</div>';
							} else {
								echo '<div class="alert alert-success">Passed. No CSRF detected.</div>';
							}
						}
						?>
						<div class="form-group">
							<label>Name</label>
							<input type="text" name="name" value="" class="form-control" />
						</div>

						<div class="form-group">
							<label>Email Address</label>
							<input type="text" name="emailAddress" value="" class="form-control" />
						</div>

						<div class="form-group">
							<label>Password</label>
							<input type="password" name="password" value="" class="form-control" />
						</div>

						<input type="submit" name="btn" value="Submit" class="btn btn-danger" />

						<?php echo $csrf->renderHTML(); ?>
					</form>
				</div>
			</div>
		</div>

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</body>
</html>