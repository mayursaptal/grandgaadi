<?php


/**
 * Plugin Name: Shipmanet Status api 
 * Author Name: Mayur Saptal
 * Description: save and show shipment status based on refrence no
 * Version: 0.0.2
 * License: 2.0
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: shipment_status_api
 * Author: Mayur Saptal (mayursaptal@gmail.com)
 */




require "view/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//  ini_set('display_errors', 1);
//   ini_set('display_startup_errors', 1);
//   error_reporting(E_ALL);

add_action('rest_api_init', function () {
    register_rest_route('gg-shipment/v1', '/update-tracking/(?P<id>[^/]+)', array(
        'methods' => 'GET',
        'callback' => 'ssa_update_tracking',
    ));
});


function wpse_298888_posts_where($where, $query)
{
    global $wpdb;

    $starts_with = esc_sql($query->get('starts_with'));

    if ($starts_with) {
        $where .= " AND $wpdb->posts.post_title LIKE '$starts_with%'";
    }
    return $where;
}
// add_filter('posts_where', 'wpse_298888_posts_where', 10, 2);


function ssa_update_tracking(WP_REST_Request $request)
{
    $reference_number = $request->get_param('id');

    if ($reference_number != 'yes') {
        return;
    }
    add_filter('posts_where', 'wpse_298888_posts_where', 10, 2);

    $posts = get_posts(array(
        'posts_per_page'   => -1,
        'post_type' =>   'wpcargo_shipment',
        'starts_with' => 'GGC',
        'suppress_filters' => false,
    ));


    $trackers = array();

    foreach ($posts as $post) {

        $meta = get_post_meta($post->ID);

        if ($meta['reference_number'][0]) {
            $trackers[] = $meta['reference_number'][0];
            $data = array(
                'ID' =>  $post->ID,
                'post_title' => $meta['reference_number'][0],
            );

            wp_update_post($data);
        }
    }


    return rest_ensure_response($trackers);
}



add_action('rest_api_init', function () {
    register_rest_route('gg-shipment/v1', '/status/(?P<id>[^/]+)', array(
        'methods' => 'GET',
        'callback' => 'ssa_send_status',
    ));
});

function ssa_send_status(WP_REST_Request $request)
{

    $reference_number = $request->get_param('id');
    $result = get_posts(array(
        'meta_key'   => 'reference_number',
        'meta_value' =>  $reference_number,
        'post_type' =>   'wpcargo_shipment'
    ));

    if (!@$result[0]->ID) {
        return rest_ensure_response(array(
            "status" =>  "failure ",
            'data' =>
            array("list" => array())
        ));
    }

    $meta = get_post_meta($result[0]->ID);





    $status = (unserialize($meta['wpcargo_shipments_update'][0]));

    if (!is_array($status)) {
        $status = (unserialize($status));
    }

    $response = [];
    foreach ($status as $stat) {
        $response[] = array(
            "id" => "",
            "status" => $stat['status'],
            "description" => $stat['remarks'],
            "created_at" => array(
                "date" => $stat['date'] . " " . $stat['time'] . ".000000",
                "timezone_type" => 3,
                "timezone" => "Asia/Dubai"
            )
        );
    }


    $data = array(
        "status" =>  "success",
        'data' =>
        array("list" => $response)
    );


    return rest_ensure_response($data);
}


add_action('admin_menu', 'my_menu_pages');
function my_menu_pages()
{
    add_menu_page('Assign Driver', 'Assign Driver', 'manage_options', 'assign-driver', 'my_menu_output');
    // add_submenu_page('my-menu', 'Submenu Page Title', 'Whatever You Want', 'manage_options', 'my-menu' );
    // add_submenu_page('my-menu', 'Submenu Page Title2', 'Whatever You Want2', 'manage_options', 'my-menu2' );
}

function my_menu_output()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/import_driver.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}


add_shortcode('SSI_ASSIGN_DRIVER', 'my_menu_output');


include_once plugin_dir_path(__FILE__) . 'view/report_view.php';
function ssi_report()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/report_shortcode.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}


add_shortcode('SSI_REPORT', 'ssi_report');

function ssi_bulkreport()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/ssi_bulkreport.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}


add_shortcode('SSI_BULK_REPORT', 'ssi_bulkreport');


add_action('wp', 'bulk_report_function');


function bulk_report_function()
{
    if ($_POST['update_bulk']) {

        $ids = $_POST['ids'];

        $driver = $_POST['driver'];

        $status = $_POST['status'];

        $posts = array();
        foreach ($ids  as $id) {
            if ($driver) {
                update_post_meta($id, 'wpcargo_driver', $driver);
            }

            if ($status) {



                $meta =   (get_post_meta($id));



                $data = (unserialize($meta['wpcargo_shipments_update'][0]));

                if (!is_array($data)) {
                    $data = unserialize($data);
                }


                $data[] =  array(
                    'date' => date('Y-m-d'),
                    'time' => date('h:m'),
                    'status' => $status,
                    'updated-by' => wp_get_current_user()->display_name

                );


                update_post_meta($id, 'wpcargo_status', $status);


                update_post_meta($id, 'wpcargo_shipments_update', $data);
            }
        }


        echo "updated successfully ";
        exit();
    }

    if ($_POST['ids']) {
        $ids = $_POST['ids'];

        $posts = array();
        foreach ($ids  as $id) {
            $posts[] = get_post_meta($id);
        }

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
            'PICKUP DATE',
            'LAST REMARK'
        )];


        $users = get_users(array('fields' => array('ID', 'display_name ')));


        $user_id = array();

        foreach ($users  as $user) {
            $user_id[$user->ID] = $user->display_name;
        }



        foreach ($posts as $post) {


            $meta =   $post;



            $data = (unserialize($meta['wpcargo_shipments_update'][0]));

            if (!is_array($data)) {
                $data = unserialize($data);
            }


            $last_update = array();
            $last_date = '';
            $last_time = '';
            foreach ($data as $dts) {
                if ($last_date == '') {
                    $last_date = $dts['date'];
                    $last_time = $dts['time'];
                    $last_update = $dts;
                }
                if (strtotime($dts['date']) > strtotime($last_date)) {
                    $last_date = $dts['date'];
                    $last_time = $dts['time'];
                    $last_update = $dts;
                }
                if (strtotime($dts['date']) ==  strtotime($last_date)) {

                    if (strtotime($dts['time']) ==  strtotime($last_time)) {
                        $last_date = $dts['date'];
                        $last_time = $dts['time'];
                        $last_update = $dts;
                    }
                }
            }






            $header[] = array(
                // $post->post_title,
                //access by key
                @$meta['reference_number'][0],
                @$user_id[@$meta['registered_shipper'][0]],
                @$meta['consignee_name'][0],
                @$meta['cod_amount'][0],
                @$meta['wpcargo_destination'][0],
                $meta['wpcargo_status'][0],
                @$user_id[@$meta['wpcargo_driver'][0]],
                // user data
                $last_update['date'],
                @$meta['wpcargo_pickup_date_picker'][0],
                $last_update['remarks'],
            );
        }



        // create new spreadsheet//
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header, NULL, 'A1');

        // redirect output to client browser
        header('Content-Disposition: attachment;filename="myfile.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);

        $path = wp_upload_dir()['path'] . '/myfile.xlsx';
        $url = wp_upload_dir()['url'] . '/myfile.xlsx';
        $writer->save($path);

        echo  $url;
        exit();
    }
}
function client_import_output()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/client_import.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}
add_shortcode('CLIENT_IMPORT', 'client_import_output');

function driver_import_output()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/driver_import.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}
add_shortcode('DRIVER_REPORT', 'driver_import_output');


function dispatch_import_output()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/dispatch_report.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}
add_shortcode('DISPATCH_REPORT', 'dispatch_import_output');

function bulk_import_output()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'view/bulk_import.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}
add_shortcode('BULK_IMPORT', 'bulk_import_output');


if (@$_GET['getsample']) {



    $key_maping = array(
        'post_title' => "REFERENCE NUMBER",
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

    $header = [$key_maping];
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($header, NULL, 'A1');
    // redirect output to client browser
    header('Content-Disposition: attachment;filename="sample.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    die();
}

//////My work


if (@$_GET['clientData']) {

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
            ), 
            array(
                'key'     => 'wpcargo_status',
                'compare' => '=',
                'value'   => 'DELIVERED',
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

    // $header is an array containing column headers
      $header = [array(
        // "GRANDGAADI PACKAGE DELIVERY SERVICE LLC,
        // ASSIGNED CLIENT NAME&nbsp :
        //   DELIVERED DATE :
        //   SHIPMENT STATUS :",    
        "ID",
         'PICKUP DATE', 
         "REFERENCE NUMBER",
         "CONSIGNEE NAME",
         "LAST STATUS",
         "LAST UPDATE DATE",
        "COD AMOUNT",
        "REMARK",
  
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
        
           'reference_number' => @$meta['reference_number'][0],
            'wpcargo_pickup_date_picker'=>    @$meta['wpcargo_pickup_date_picker'][0],
            'reference_number' => @$meta['reference_number'][0],
           'consignee_name' =>@$meta['consignee_name'][0],
           'status' => @$meta['wpcargo_status'][0],
          'date'  =>$last_update['date'],  
          'cod_amount'=>  @$meta['cod_amount'][0],		

            'remarks' =>$last_update['remarks'],
        );
    }
    

// 	echo "<pre>";

//     var_dump(  $countor);
//     die(); 
// $data=["GRANDGAADI PACKAGE DELIVERY SERVICE LLC,
//      ASSIGNED CLIENT NAME&nbsp :
//        DELIVERED DATE :
//        SHIPMENT STATUS :" ]
if($_GET['download'])
{
    $spreadsheet = new Spreadsheet();
   
    $sheet = $spreadsheet->getActiveSheet();
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('C1:H1');
    $sheet->setCellValue('C1', 'GRANDGAADI PACKAGE DELIVERY SERVICE LLC');
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A2:C2');
    $sheet->setCellValue('A2', 'ASSIGNED CLIENT NAME :');
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D2:H2');
    $sheet->setCellValue('D2', 'ASSIGNED CLIENT NAME');


    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A3:C3');
    $sheet->setCellValue('A3', 'DELIVERED DATE :');
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D3:H3');
    $sheet->setCellValue('D3',$_GET['from']  .' - ' .  $_GET['to']);

    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A4:C4');
    $sheet->setCellValue('A4', 'SHIPMENT STATUS :');
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D4:H4');
    $sheet->setCellValue('D4', 'SHIPMENT STATUS');
    

    $sheet->fromArray($header, NULL, 'A6');

    // redirect output to client browser
    header('Content-Disposition: attachment;filename="myfile.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    exit();
}

}



if (@$_GET['driverData']) {


    $total_cod = 0;
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
                'value'   => $_GET['driver'],
                'compare' => '=',
            ), 
         
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

    // $header=[array(
        // GRANDGAADI PACKAGE DELIVERY SERVICE LLC (DRS SHEET)
    // " DRIVER NAME:",
    //  "DELIVERED DATE:",
    //  "TOTAL COD AMOUNT :",
    // )]
    // $header is an array containing column headers
    $header = [array(
        "ID",
        "REFERENCE NUMBER",
        "CONSIGNEE NAME",
        "CONSIGNEE CONTACT",
        "STATUS",
        "COD AMOUNT",
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
        

        $total_cod =$total_cod + @$meta['cod_amount'][0];
         $header[] = array(
            'reference_number'=>@$meta['reference_number'][0],
            'reference_number'=>@$meta['reference_number'][0],
            'consignee_name'=>@$meta['consignee_name'][0],
            'consignee_contact'=>@$meta['consignee_contact'][0],
            'comment_status'=>@$meta['comment_status'][0],
            'cod_amount'=>@$meta['cod_amount'][0],
            'remarks'=>$last_update['remarks'],

 
        );
    }
    

// 	echo "<pre>";

//     var_dump(  $countor);
//     die();

if($_GET['download'])
{
   $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('C1:H1');
    $sheet->setCellValue('C1', 'GRANDGAADI PACKAGE DELIVERY SERVICE LLC (DRS SHEET)');
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A2:C2');
    $sheet->setCellValue('A2',  "DRIVER NAME :");
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D2:H2');
    $sheet->setCellValue('D2',  $user_id[ $_GET['driver']] );


    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A3:C3');
    $sheet->setCellValue('A3', "DELIVERED DATE :");
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D3:H3');
    $sheet->setCellValue('D3', $_GET['from']  .' - ' .  $_GET['to']);


    $sheet= $spreadsheet->getActiveSheet()->mergeCells('A4:C4');
    $sheet->setCellValue('A4',"TOTAL COD AMOUNT :");
    $sheet= $spreadsheet->getActiveSheet()->mergeCells('D4:H4');
    $sheet->setCellValue('D4',$total_cod);
    $sheet->fromArray($header, NULL, 'A5');

    // redirect output to client browser
    header('Content-Disposition: attachment;filename="myfile.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    exit();
}
}



if (@$_GET['dispatchData']) {

    
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
                    'value'   => $_GET['drive'],
                    'compare' => '=',
                ), 
                // array(
                //     'key'     => 'wpcargo_status',
                //     'compare' => '=',
                //     'value'   => 'DELIVERED',
                // )
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
        // $header=[array(
            // GRANDGAADI PACKAGE DELIVERY SERVICE LLC (DRS SHEET)
        // " ASSIGNED CLIENT NAME:",
        // " DELIVERED DATE :",
        // " SERVICE STATUS :" ,
        // " UPDATED BY:",   
        // )]
        // $header is an array containing column headers
        $header = [array(
            "ID",
            "REFERENCE NUMBER",
             "CONSIGNEE NAME",
             "CONSIGNEE ADDRESS",
             "CONSIGNEE CONTACT",
            "DESCRIPTION",
            "COD AMOUNT",
            "RECEIVER SIGN",
      
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
            
            
    $packages = $meta['packages'];
   $packages[0]['wpc-pm-description'];

            $header[] = array(
              
                 'reference_number'   =>   @$meta['reference_number'][0],
                 'reference_number'   =>   @$meta['reference_number'][0],
                 'consignee_name'  =>   @$meta['consignee_name'][0],
                 'wpcargo_receiver_address'    =>  @$meta['wpcargo_receiver_address'][0],
                 'consignee_contact'    => @$meta['consignee_contact'][0],

                 'wpc-multiple-package'=>unserialize(unserialize(@$meta['wpc-multiple-package'][0] ))[0]['wpc-pm-description'],

           
                'cod_amount' =>  @$meta['cod_amount'][0],
                'remarks'   =>  $last_update['remarks'],		
            );
        }
        

  
if($_GET['download'])
{
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet= $spreadsheet->getActiveSheet()->mergeCells('C1:H1');
        $sheet->setCellValue('C1', 'GRANDGAADI PACKAGE DELIVERY SERVICE LLC (DRS SHEET)');

        $sheet= $spreadsheet->getActiveSheet()->mergeCells('A2:C2');
        $sheet->setCellValue('A2', "ASSIGNED CLIENT NAME : ");
        $sheet= $spreadsheet->getActiveSheet()->mergeCells('D2:H2');
        $sheet->setCellValue('D2', "ASSIGNED CLIENT NAME ");

        $sheet= $spreadsheet->getActiveSheet()->mergeCells('A3:C3');
        $sheet->setCellValue('A3',  " DELIVERED DATE : ");
        $sheet= $spreadsheet->getActiveSheet()->mergeCells('D3:H3');
        $sheet->setCellValue('D3',  $_GET['from']  .' - ' .  $_GET['to']);

        $sheet= $spreadsheet->getActiveSheet()->mergeCells('A4:C4');
        $sheet->setCellValue('A4'," SERVICE STATUS : ");
        $sheet= $spreadsheet->getActiveSheet()->mergeCells('D4:H4');
        $sheet->setCellValue('D4'," SERVICE STATUS  ");

        $sheet= $spreadsheet->getActiveSheet()->mergeCells('A5:C5');
        $sheet->setCellValue('A5', " UPDATED BY : " );
        $sheet= $spreadsheet->getActiveSheet()->mergeCells('D5:H5');
        $sheet->setCellValue('D5', " UPDATED BY  " );


        $sheet->fromArray($header, NULL, 'A7');
    
        // redirect output to client browser
        header('Content-Disposition: attachment;filename="myfile.xlsx"');
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit();

    }
}