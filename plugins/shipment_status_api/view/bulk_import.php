<?php

require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
set_time_limit(0);

$inputFileType = 'Xlsx';
//    $inputFileType = 'Xlsx';
//    $inputFileType = 'Xml';
//    $inputFileType = 'Ods';
//    $inputFileType = 'Slk';
//    $inputFileType = 'Gnumeric';
//    $inputFileType = 'Csv';
$inputFileName = @$_FILES['csv_file_driver']['tmp_name'];

$key_maping = array(
    'post_title' => "REFERENCE NUMBER",
    'post_name' => "REFERENCE NUMBER",
    'wpcargo_pickup_date_picker' => "Pickup Date",
    "wpcargo_type_of_shipment" => "Type of Shipment",
    "shipper_name" => "Shipper Name",
    "shipper_adress" => "SHIPPER ADRESS",
    "shipper_contact" => "SHIPPER CONTACT",
    "consignee_name" => "CONSIGNEE NAME",
    "wpcargo_receiver_addres" => "Address",
    "consignee_contact" => "CONSIGNEE CONTACT",
    "cod_amount" => "COD AMOUNT",
    "wpcargo_origin_field" => "Origin",
    "wpcargo_destination" => "Destination)",
    "wpcargo_courier" => "Courier",
    "payment_wpcargo_mode_field" => "Payment Mode",
    "wpc-multiple-package" => "Package Details",
    "wpcargo_status" => "Shipment Status",
    "wpcargo_comments" => "Comments",
    'wpc-pm-qty' => 'Qty',
    'wpc-pm-piece-type' => 'Piece Type',
    'wpc-pm-description' => 'Description',
    'wpc-pm-weight' =>  'Weight (kg)'
);


if ($inputFileName) {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    $spreadsheet = $reader->load($inputFileName);
    $data = $spreadsheet->getActiveSheet()->toArray();
    $rows = array();
    $head = array();
    foreach ($data as $key => $val) {
        if ($key == 0) {
            $head = ($val);
            foreach ($head as $key => $val) {
                $head[$key] = strtolower(str_replace(' ', '',  $val));
            }
        } else {

            $row = array();
            foreach ($val as $vlk => $vl) {
                $row[$head[$vlk]] = $vl;
            }

            $rows[] = $row;
        }
    }



    foreach ($key_maping as $key => $val) {
        $key_maping[$key] = strtolower(str_replace(' ', '',  $val));
    }




    $exclude = array();

    foreach ($rows as $row) {
        // var_dump($row);

        if (get_page_by_title($row[$key_maping['post_title']], OBJECT, 'wpcargo_shipment')) {

            $exclude[] = $row[$key_maping['post_title']];
            continue;
        }


        $new_post = array(
            'post_title' => $row[$key_maping['post_title']],
            'post_status' => 'publish',
            'post_type' => 'wpcargo_shipment',
            'meta_input' => [
                'post_title' => $row[$key_maping['post_title']],
                'post_name' => $row[$key_maping['post_name']],
                'reference_number' => $row[$key_maping['reference_number']],
                'wpcargo_pickup_date_picker' => $row[$key_maping['wpcargo_pickup_date_picker']],
                'wpcargo_type_of_shipment' => $row[$key_maping['wpcargo_type_of_shipment']],
                'shipper_name' => $row[$key_maping['shipper_name']],
                'shipper_adress' => $row[$key_maping['shipper_adress']],
                'shipper_contact' => $row[$key_maping['shipper_contact']],
                'consignee_name' => $row[$key_maping['consignee_name']],
                'wpcargo_receiver_addres' => $row[$key_maping['wpcargo_receiver_addres']],
                'consignee_contact' => $row[$key_maping['consignee_contact']],
                'cod_amount' => $row[$key_maping['cod_amount']],
                'wpcargo_origin_field' => $row[$key_maping['wpcargo_origin_field']],
                'wpcargo_destination' => $row[$key_maping['wpcargo_destination']],
                'wpcargo_courier' => $row[$key_maping['wpcargo_courier']],
                'payment_wpcargo_mode_field' => $row[$key_maping['payment_wpcargo_mode_field']],
                'wpcargo_status' => $row[$key_maping['wpcargo_status']],
                'wpcargo_comments' => $row[$key_maping['wpcargo_comments']],
                'wpc-multiple-package'  => array(0 => array(
                    'wpc-pm-qty' =>  $row[$key_maping['wpc-pm-qty']],
                    'wpc-pm-piece-type' =>  $row[$key_maping['wpc-pm-piece-type']],
                    'wpc-pm-description' => $row[$key_maping['wpc-pm-description']],
                    'wpc-pm-weight' =>  $row[$key_maping['wpc-pm-weight']],
                ))
            ]
        );

        $id = wp_insert_post($new_post);
        $status =  $row[$key_maping['wpcargo_status']];
        $history[] = array(
            'date' => date('Y-m-d'),
            'time' => date('H:i'),
            'location' => '',
            'status' => $status,
            'remarks' => '',
            'updated-by' => 'admin'
        );

        update_post_meta($id, 'wpcargo_shipments_update_demo', $history);
        update_post_meta($id, 'wpcargo_shipments_update', $history);
    }

    echo ("Imported Successfully<br>");
    echo "duplicated shipments: <br>";
    echo join($exclude, ',');
}







?>
<form method="post" enctype="multipart/form-data">
    <h2> Import Shipments </h2>
    <label>Select File</label><br> <br>
    <input type="file" accept=".xls" name="csv_file_driver">
    <br> <br> <br>
    <input type="submit" class="btn btn-primary btn-sm m-0" value="Import">
</form>
<br>
<br>
<a href="?getsample=true"> Download smaple </a>