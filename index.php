<?php 

require_once dirname(__FILE__) .'/PHPExcel/IOFactory.php';

$site_path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
$base_url .= '://' . $_SERVER['HTTP_HOST'];
$base_url .= $site_path;

$title_app = "Bayes App &middot; Pengenalan Pola";
$file_data = 'data.json';
$data = file_get_contents($file_data);
$data = json_decode($data);
if ($data == "") {
	$data = (object)array();
}

// upload data latih

if (isset($_FILES['file'])) {
	$start = 1;

	$exp = explode(".", $_FILES['file']['name']);
	$exp = end($exp);
	$nama_file = 'data-latih.'.$exp;
	if (!in_array($exp , array('xls', 'xlsx'))) {
		$message = array('danger', "File tidak sesuai format, gunakan .xlsx atau .xls!");
	}else if ($_FILES['file']['error'] == 0){
		$objPHPExcel 	= PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
		$data_mentah 	= $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

		$head = array();
		foreach ($data_mentah[1] as $key => $value) {
			if ($value == null) break;
			$head[$key] = $value;
		}

		$list_key 		= array_keys($head);
		$data->key_class = end($list_key);
		array_pop($head);
		$data->fiture 	= $head;

		$sum_data = 0;
		$class 			= array();
		$sum_class 		= array();
		$all_data 		= array();
		$fiture_data 	= array();

		foreach ($data_mentah as $no => $row) {
			if ($no == 1) continue;
			if ($row['A'] == null) break;
			$sum_data += 1;
			$new_row = array();
			if (!in_array($row[$data->key_class], $class)) {
				$class[] = $row[$data->key_class];
				$sum_class[array_search($row[$data->key_class], $class)] = 1;
			}else{
				$sum_class[array_search($row[$data->key_class], $class)] += 1;
			}

			foreach ($data->fiture as $key => $value) {
				$fiture_data[array_search($row[$data->key_class], $class)][$key][] = $row[$key];
			}

			foreach ($row as $key => $value) {
				if ($value == null) break;
				$new_row[] = $value;
			}

			$all_data[] = $new_row;
		}

		$data->class 		= $class;
		$data->sum_data 	= $sum_data;
		$data->sum_class 	= $sum_class;
		$data->fiture_data 	= $fiture_data;
		$data->all_data 	= $all_data;

		file_put_contents($file_data, json_encode($data));

		$message = array('success', "Sukses upload data latih.");
	}else{
		$message = array('danger', "Error upload data latih.");
	}
}

// olah data latih

$hitung = false;
if (isset($_POST['fiture'])) {
	$filed_form = false;
	foreach ($data->fiture as $key => $value) {
		$_POST['fiture'][$key] = (float)$_POST['fiture'][$key];
		if (!in_array($key, array_keys($_POST['fiture']))) {
			$filed_form = true;
			break;
		}
	}
	if ($filed_form) {
		$message = array('danger', "Error form.");
	}else{
		$hitung 	= true;
		$sum 		= array();
		$mean 		= array();
		$varian 	= array();
		$likelihood = array();

		foreach ($data->fiture_data as $key => $data_class) {
			foreach ($data_class as $key_fiture => $value) {
				$sum[$key][$key_fiture] = array_sum($value);
				$mean[$key][$key_fiture] = array_sum($value)/$data->sum_class[$key];
				$varian_pembilang = 0;
				foreach ($value as $item) {
					$varian_pembilang += pow(($item - $mean[$key][$key_fiture]), 2);
				}
				$varian[$key][$key_fiture] = $varian_pembilang/($data->sum_class[$key]-1);
				$likelihood[$key][$key_fiture] = 
					(
						1 / sqrt($varian[$key][$key_fiture] * 2 * M_PI)
					) * 
					pow(
						M_E, 
						(-1 * (
							(
								pow(
									((float)$_POST['fiture'][$key_fiture] - $mean[$key][$key_fiture]), 
									2
								)
							) /
							(
								2 * $varian[$key][$key_fiture]
							)
						))
					);
			}
		}

		$kali_likelihood = array();
		foreach ($data->class as $key => $value) {
			$kali_likelihood[$key] = 1;
			foreach ($likelihood[$key] as $key2 => $value2) {
				$kali_likelihood[$key] *= $value2;
			}
		}

		$posterior = array();
		foreach ($data->class as $key => $value) {
			$posterior[$key] = $data->sum_class[$key] / $data->sum_data;
		}

		$prior = array();
		foreach ($data->class as $key => $value) {
			$prior[$key] = $kali_likelihood[$key] * $posterior[$key];
		}

		$max_prior = 0;
		$max_class = "";
		foreach ($data->class as $key => $value) {
			if ($prior[$key] > $max_prior) {
				$max_prior = $prior[$key];
				$max_class = $value;
			}
		}
	}
	$tab = 'olah';
}

// tampilkan

if (!isset($tab)) {
	$tab = "upload";
}

include 'view.php';
