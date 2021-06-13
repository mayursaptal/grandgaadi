<?php

require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;





// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (@$_GET['ssi_download']) {

//     ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    ini_set("memory_limit", -1);
    ini_set('max_execution_time', 0); //0=NOLIMIT

    $query_args = array(
        'orderby' => 'ID',
        'order' => 'DESC',
		'post_status'       => 'publish',
        'posts_per_page'   => 10000,
        'post_type' =>   'wpcargo_shipment',
        'suppress_filters' => false,
        'meta_query' => array(
            array(
                'key'     => 'registered_shipper',
                'value'   => $_GET['client'],
                'compare' => '=',
            )
        )
    );

	
	
	
    $start_date  = $_GET['from'];
    $end_date  = $_GET['to'];

    // if ($start_date && $end_date) {

    //     $query_args['date_query'] =
    //         array(
    //             'relation'   => 'OR',
    //             array(
    //                 array(
    //                     'column' => 'post_date',
    //                     'after' => $start_date . ' 00:00:00',
    //                     'before' => $end_date . ' 23:59:59',
    //                 ),
    //                 array(
    //                     'column' => 'post_modified',
    //                     'after' => $start_date . ' 00:00:00',
    //                     'before' => $end_date . ' 23:59:59',
    //                 )
    //             )
    //         );
    // }


    $posts = get_posts($query_args);



    // echo  "<pre>";
    // // var_dump($posts);

    // var_dump(get_post_meta($posts[0]->ID));

    // die();
    // $header is an array containing column headers
    $header = [array(
        // "SHIPMENT ID",
        "REFERENCE NUMBER",
        "ASSIGNED CLIENT",
        "CONSIGNEE NAME",
        "COD AMOUNT",
        "DESTINATION",
		"STATUS",
      
        "DRIVER NAME",
        "LAST UPDATE DATE",
// 		  "LAST STATUS",
		'PICKUP DATE',
		'LAST REMARK',
    )];


    $users = get_users(array('fields' => array('ID', 'display_name ')));


    $user_id = array();

    foreach ($users  as $user) {
        $user_id[$user->ID] = $user->display_name;
    }


$countor = array();
	
    foreach ($posts as $post) {


        $meta =   (get_post_meta($post->ID));
        $data = (unserialize($meta['wpcargo_shipments_update'][0]));

        if (!is_array($data)) {
            $data = unserialize($data);
        }


        $last_update = array();
        $last_date = '';
        $last_time = '';
//         foreach ($data as $dts) {
//             if ($last_date == '') {
//                 $last_date = $dts['date'];
//                 $last_time = $dts['time'];
//                 $last_update = $dts;
//             }
//             if (strtotime($dts['date']) > strtotime($last_date)) {
//                 $last_date = $dts['date'];
//                 $last_time = $dts['time'];
//                 $last_update = $dts;
//             }
//             if (strtotime($dts['date']) ==  strtotime($last_date)) {

//                 if (strtotime($dts['time']) ==  strtotime($last_time)) {
//                     $last_date = $dts['date'];
//                     $last_time = $dts['time'];
//                     $last_update = $dts;
//                 }
//             }
//         }

 
		$last_date = @$meta['wpcargo_pickup_date_picker'][0];
		$last_date = $post->post_date ;
		$last_update = end($data);
// 		$last_date = $last_update['date'];
		
        if (strtotime($last_date)  > strtotime($end_date)) {
            continue;
        }


        if (strtotime($last_date)  < strtotime($start_date)) {
            continue;
        }
		
// 		echo $last_date . '<br>';
		

		

		$countor[$meta['wpcargo_status'][0]]['count'] = $countor[$meta['wpcargo_status'][0]]['count'] + 1;
// 		$countor[$meta['wpcargo_status'][0]]['id'][$post->ID] =  @$meta['reference_number'][0];
// 		$countor[$meta['wpcargo_status'][0]]['date'][$post->ID] =   @$meta['wpcargo_pickup_date_picker'][0];
// 		$countor[$meta['wpcargo_status'][0]]['date'][$post->ID] =  date('d-m-Y' , strtotime($last_date));
		
		
		
        $header[] = array(
            // $post->post_title,
            @$meta['reference_number'][0],
            @$user_id[@$meta['registered_shipper'][0]],
            @$meta['consignee_name'][0],
            @$meta['cod_amount'][0],
            @$meta['wpcargo_destination'][0],
			$meta['wpcargo_status'][0],
            @$user_id[@$meta['wpcargo_driver'][0]],
            $last_update['date'],
// 			$last_update['status'],
			@$meta['wpcargo_pickup_date_picker'][0],
			 $last_update['remarks'],
        );
    }

// 	echo "<pre>";

//     var_dump(  $countor);
//     die();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($header, NULL, 'A1');

    // redirect output to client browser
    header('Content-Disposition: attachment;filename="myfile.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
