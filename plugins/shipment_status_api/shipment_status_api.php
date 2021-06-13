<?php


/**
 * Plugin Name: Shipmanet Status api 
 * Author Name: Mayur Saptal
 * Description: save and show shipment status based on refrence no
 * Version: 0.0.1
 * License: 0.0.1
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: shipment_status_api
 * Author: Mayur Saptal (mayursaptal@gmail.com)
 */




require "view/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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


function bulk_report_function(){
    if($_POST['update_bulk']){

        $ids = $_POST['ids'];

        $driver = $_POST['driver'];

        $status = $_POST['status'];

        $posts = array();
        foreach( $ids  as $id){
            if( $driver){
                update_post_meta($id , 'wpcargo_driver' , $driver);
            }
        
            if($status){



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


                update_post_meta($id , 'wpcargo_status' , $status);


                update_post_meta($id , 'wpcargo_shipments_update' , $data);

              
            }
          
        }


        echo "updated successfully ";
        exit();

    }

    if($_POST['ids']){
        $ids = $_POST['ids'];

        $posts = array();
        foreach( $ids  as $id){
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
            $last_update['status'],
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

    $path = wp_upload_dir()['path'].'/myfile.xlsx';
    $url = wp_upload_dir()['url'].'/myfile.xlsx';
    $writer->save($path);

    echo  $url;
    exit();


      
    }
    
    
}

