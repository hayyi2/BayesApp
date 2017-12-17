<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title><?php echo $title_app ?></title>
	
	<!-- Bootstrap Core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<!-- dataTables -->
	<link href="assets/css/dataTables.bootstrap4.min.css" rel="stylesheet"/>
	<!-- Custom CSS -->
	<link href="assets/css/style.css" rel="stylesheet">

</head>
<body>

	<nav class="navbar navbar-expand-sm navbar-dark bg-dark text-center mb-5">
		<div class="collapse navbar-collapse"></div>
		<a class="navbar-brand" href="<?php echo $base_url ?>"><?php echo $title_app ?></a>
		<div class="collapse navbar-collapse"></div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-lg-9 ml-auto mr-auto">
				<?php if (isset($message)): ?>
					<div class="alert alert-<?php echo $message[0]; ?> alert-dismissible fade show" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<?php echo $message[1]; ?>
					</div>
				<?php endif ?>
				<div class="card mb-5">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#upload" role="tab">Upload Data Latih</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#useit" role="tab">Gunakan Data Latih</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane" id="upload" role="tabpanel">
							<div class="card-body">
								<form method="post" enctype="multipart/form-data">
									<div class="form-group row">
										<div class="col-sm-8">
											<input type="file" name="file" accept=".xls,.xlsx" required="" class="form-control">
										</div>
										<div class="col-sm-4">
											<button name="upload_file" value="true" type="submit" class="btn btn-primary btn-block">
											<i class="fa fa-fw fa-upload"></i> Upload File</button>
										</div>
									</div>
								</form>
							</div>
							<h5 class="card-header text-primary">
								Data Latih
							</h5>
							<div class="card-body">
								<table class="datatable table table-hover">
									<thead>
										<tr>
											<?php foreach ($data->fiture as $th):?>
												<th><?php echo $th; ?></th>
											<?php endforeach ?>
											<th>Class</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($data->all_data as $row):?>
										<tr>
											<?php foreach ($row as $column): ?>
												<td class="text-nowrap"><?php echo $column ?></td>
											<?php endforeach ?>
										</tr>
										<?php endforeach ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="useit" role="tabpanel">
							<div class="card-body">
								<form method="POST">
									<?php foreach ($data->fiture as $key => $value): ?>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?php echo ucwords($value); ?></label>
											<div class="col-sm-7">
												<input type="text" name="fiture[<?php echo $key ?>]" <?php if(isset($_POST['fiture'][$key])) echo 'value="' .$_POST['fiture'][$key]. '"'; ?> class="form-control" placeholder="<?php echo ucwords($value); ?>">
											</div>
										</div>
									<?php endforeach ?>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label"></label>
										<div class="col-sm-7">
											<button type="submit" class="btn btn-primary">Hitung Sekarang</button>
										</div>
									</div>
								</form>
							</div>
							<?php if ($hitung): ?>
								<h5 class="card-header text-primary">
									Hasil Hitung
								</h5>
								<div class="card-body">
									<div class="row">
										<div class="col-sm-8">
											<table class="table table-hover">
												<tbody>
													<?php foreach ($data->class as $key => $value): ?>
														<tr>
															<th width="50%"><?php echo $value; ?></th>
															<th><?php echo $prior[$key]; ?></th>
														</tr>
													<?php endforeach ?>
												</tbody>
											</table>
											<table class="table table-hover">
												<tbody>
													<tr>
														<th width="50%">Kesimpulan</th>
														<th><button class="btn btn-primary btn-sm"><?php echo $max_class; ?></button></th>
													</tr>
													<!-- <tr>
														<th width="50%">Error</th>
														<th></th>
													</tr> -->
												</tbody>
											</table>
										</div>
										<div class="col-sm-4">
											<canvas class="chart" id="doughnut-chart" ></canvas>
										</div>
									</div>
								</div>
								<h5 class="card-header text-primary">
									Penghitungan
								</h5>
								<div class="card-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Sum Data</th>
												<?php foreach ($data->class as $key => $value): ?>
													<th><?php echo $value; ?></th>
												<?php endforeach ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($data->fiture as $key => $value): ?>
											<tr>
												<td><?php echo $value; ?></td>
												<?php foreach ($data->class as $key_class => $value_class): ?>
													<td><?php echo $sum[$key_class][$key]; ?></td>
												<?php endforeach ?>
											</tr>
											<?php endforeach ?>
										</tbody>
										<thead>
											<tr>
												<th>Mean Data</th>
												<?php foreach ($data->class as $key => $value): ?>
													<th><?php echo $value; ?></th>
												<?php endforeach ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($data->fiture as $key => $value): ?>
											<tr>
												<td><?php echo $value; ?></td>
												<?php foreach ($data->class as $key_class => $value_class): ?>
													<td><?php echo $mean[$key_class][$key]; ?></td>
												<?php endforeach ?>
											</tr>
											<?php endforeach ?>
										</tbody>
										<thead>
											<tr>
												<th>Varian Data</th>
												<?php foreach ($data->class as $key => $value): ?>
													<th><?php echo $value; ?></th>
												<?php endforeach ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($data->fiture as $key => $value): ?>
											<tr>
												<td><?php echo $value; ?></td>
												<?php foreach ($data->class as $key_class => $value_class): ?>
													<td><?php echo $varian[$key_class][$key]; ?></td>
												<?php endforeach ?>
											</tr>
											<?php endforeach ?>
										</tbody>
										<thead>
											<tr>
												<th>Likelihood Data</th>
												<?php foreach ($data->class as $key => $value): ?>
													<th><?php echo $value; ?></th>
												<?php endforeach ?>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($data->fiture as $key => $value): ?>
											<tr>
												<td><?php echo $value; ?></td>
												<?php foreach ($data->class as $key_class => $value_class): ?>
													<td><?php echo $likelihood[$key_class][$key]; ?></td>
												<?php endforeach ?>
											</tr>
											<?php endforeach ?>
											<td>Total kali</td>
											<?php foreach ($data->class as $key => $value): ?>
												<td><?php echo $kali_likelihood[$key]; ?></td>
											<?php endforeach ?>
										</tbody>
									</table>
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Class</th>
												<th>Posterior</th>
												<th>Prior</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($data->class as $key => $value): ?>
												<tr>
													<td><?php echo $value; ?></td>
													<td><?php echo $posterior[$key]; ?></td>
													<td><?php echo $prior[$key]; ?></td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								</div>
							<?php endif ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="assets/js/jquery-3.2.1.min.js"></script>
	<!-- Bootstrap Core JavaScript -->
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<!-- dataTables -->
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap4.min.js"></script>
	<!-- chart -->
	<script src="assets/js/chart.min.js"></script>
	<!-- <script src="assets/js/chart-data.js"></script> -->
	<script>
		<?php if ($hitung): ?>
			var doughnutData = [
				<?php 
				$color = [
					"#62b9fb",
					"#fac878",
					"#3cdfce",
					"#f6495f",
				];
				foreach ($data->class as $key => $value): ?>
				{
					value: <?php echo ($prior[$key]/array_sum($prior))*100; ?>,
					color:"<?php echo $color[$key%4]; ?>",
					highlight: "<?php echo $color[$key%4]; ?>",
					label: "<?php echo $value; ?>(%)"
				}<?php if ($key < count($data->class) - 1) echo ",";?>
				<?php endforeach ?>
			];
			var chart3 = document.getElementById("doughnut-chart").getContext("2d");
		<?php endif ?>
		$(function() {
			$('.datatable').DataTable({});
			<?php if (isset($tab) && $tab == "upload"): ?>
				$('#myTab a:first').tab('show');
			<?php else: ?>
				$('#myTab a:last').tab('show');
			<?php endif ?>
			<?php if ($hitung): ?>
				window.myDoughnut = new Chart(chart3).Doughnut(doughnutData, {
					responsive: true,
					segmentShowStroke: false
				});
			<?php endif ?>
		});
	</script>

</body>
</html>