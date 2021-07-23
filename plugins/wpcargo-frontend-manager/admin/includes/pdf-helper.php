<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( WPCFE_PATH.'admin/includes/dompdf/lib/html5lib/Parser.php' );
require_once( WPCFE_PATH.'admin/includes/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php' );
require_once( WPCFE_PATH.'admin/includes/dompdf/src/Options.php' );
require_once( WPCFE_PATH.'admin/includes/dompdf/src/Autoloader.php' );

Dompdf\WPCFE_Autoloader::register();
use Dompdf\Dompdf;
use Dompdf\WPCFE_Options;
// Bulk Print AJAX handler
add_action( 'wp_ajax_wpcfe_bulkprint', 'wpcfe_bulkprint_ajax_callback' );
function wpcfe_bulkprint_ajax_callback(){
    global $wpdb, $WPCCF_Fields, $wpcargo;
    $directory    = WPCFE_PATH.'admin/includes/file-container/';
    // Clean directory before adding new file
    foreach( glob($directory.'*.pdf') as $pdf_file){
        unlink($pdf_file);
    }
    $wpcfe_pdf_dpi  = apply_filters( 'wpcfe_pdf_dpi', 160 );
    $shipment_ids   = $_POST['selectedShipment'];
    $print_type     = $_POST['printType'];
    $waybill_title 	= $print_type.'-'.time();	
    $print_paper    = wpcfe_print_paper()[$print_type];
	
	  $output=wpcfe_bulkprint_template_path( $shipment_ids, $waybill_title, $print_type );



    $output .= '<script type="text/javascript">
    window.onload = function() { window.print(); }
 </script>';
 
 
    if( file_put_contents( $directory.$waybill_title.'.html', $output) ){
     $data_info = array(
         'file_url' => WPCFE_URL.'admin/includes/file-container/'.$waybill_title.'.html',
         'file_name' => $waybill_title
     );  
 }
 echo json_encode( $data_info );
 wp_die();
 
 die();
	
    // instantiate and use the dompdf class
    $options 		= new WPCFE_Options();
    $options->setDpi( $wpcfe_pdf_dpi );
    $dompdf 		= new Dompdf( $options );
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->loadHtml( wpcfe_bulkprint_template_path( $shipment_ids, $waybill_title, $print_type ) );
    $dompdf->setPaper( $print_paper['size'], $print_paper['orient']);
    // Render the HTML as PDF
    $dompdf->render();
    wpcfe_pdf_pagination( $dompdf, $print_type );

    // Output the generated PDF to Browser
    $output = $dompdf->output();
    $data_info = array();
    if( file_put_contents( $directory.$waybill_title.'.pdf', $output) ){
        $data_info = array(
            'file_url' => WPCFE_URL.'admin/includes/file-container/'.$waybill_title.'.pdf',
            'file_name' => $waybill_title
        );  
    }
    echo json_encode( $data_info );
    wp_die();
}
function wpcfe_bulkprint_template_path( $shipment_ids, $waybill_title, $print_type ){
    ob_start();
    global $WPCCF_Fields, $wpcargo, $wpcargo_print_admin;
    if( wpcfe_enable_label_multiple_print() && $print_type == 'label' ){
        $print_type         = $print_type.'-packages';
    }
    $custom_template_path   = get_stylesheet_directory() .'/wpcargo/'. $print_type.'.tpl.php';
    $mp_settings            = get_option('wpc_mp_settings');
    $setting_options        = get_option('wpcargo_option_settings');
    $print_fonts 		    = wpcargo_print_fonts();
    $ffamily 			    = get_option('wpcargo_print_ffamily');
    $fsize 				    = get_option('wpcargo_print_fsize') ? get_option('wpcargo_print_fsize') : 12 ;
    $logo                   = '';
    if( !empty( $setting_options['settings_shipment_ship_logo'] ) ){
        $logo 		= '<img style="width: 180px;" id="logo" src="'.$setting_options['settings_shipment_ship_logo'].'">';
    }
    if( get_option('wpcargo_label_header') ){
        $siteInfo = get_option('wpcargo_label_header');
    }else{
        $siteInfo  = $logo;
        $siteInfo .= '<h2 style="margin:0;padding:0;">'.get_bloginfo('name').'</h2>';
        $siteInfo .= '<p style="margin:0;padding:0;font-size: 14px;">'.get_bloginfo('description').'</p>';
        $siteInfo .= '<p style="margin:0;padding:0;font-size: 8px;">'.get_bloginfo('wpurl').'</p>';
    }
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?> <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?>>
        <head>
        <title><?php echo $waybill_title; ?></title>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
            <?php if( wpcfe_customfont_enable() ): ?>
                @font-face {
                    font-family: 'Firefly Sung';
                    font-style: normal;
                    font-weight: 400;
                    src: url(http://eclecticgeek.com/dompdf/fonts/cjk/fireflysung.ttf) format('truetype');
                }
                *{
                    font-family: Firefly Sung, DejaVu Sans, sans-serif;
                }
            <?php else: ?>
                <?php if( $ffamily && array_key_exists( $ffamily, $print_fonts ) ): ?>
                    @import url('<?php echo $print_fonts[$ffamily]['url']; ?>');
                    *{
                        font-family: <?php echo $print_fonts[$ffamily]['fontfamily']; ?> !important;
                        font-size: <?php echo $fsize; ?>px;
                    } 
                <?php endif; ?>
            <?php endif; ?> 
            html, body{ margin:0px; padding:0px; }
            h1, h2, h3, h4, h5, h6{ font-weight: normal !important; }
            h6, h5{ font-size: 1.1rem!important; }
            h4, h3{ font-size: 1.2rem!important; }
            h2{ font-size: 1.4rem!important; }
            h1{ font-size: 1.6rem!important; }
            div.copy-section { border: 2px solid #000; margin-bottom: 18px; }
            .copy-section table { border-collapse: collapse; }
            .copy-section table td.align-center{ text-align: center; }
            .copy-section table td { border: 1px solid #000; }
            table tr td{ padding:6px; }
            .page_break { page-break-before: always; }
            @media screen, print{ .page_break { page-break-before: always; } }
        </style>
        </head>
        <body>
        <?php

        if( file_exists( $custom_template_path ) ){
            $template_path = $custom_template_path;
        }else{
            if( $print_type != 'waybill' ){
                $template_path  =  WPCFE_PATH.'templates/print/'.$print_type.'.php';
            }else{
                ?>
                <style type="text/css">
                    div.copy-section { border: 2px solid #000; margin-bottom: 18px; }
                    .copy-section table { border-collapse: collapse; }
                    .copy-section table td.align-center{ text-align: center; }
                    .copy-section table td { border: 1px solid #000; }
                    table tr td{ padding:6px; }
                </style>
                <?php
                $template_path =  apply_filters( 'label_template_url', $wpcargo_print_admin->print_label_template_callback(), $shipmentDetails );
            }
        }
        if( !empty( $shipment_ids ) ){
            $counter        = 1;
            $shipment_num   = count( $shipment_ids );
            foreach ( $shipment_ids as $shipment_id ) {
                $shipmentID             = $shipment_id;
                $packages               = maybe_unserialize( get_post_meta( $shipmentID,'wpc-multiple-package', TRUE) );
                $shipmentDetails 	= array(
                    'shipmentID'	=> $shipment_id,
                    'barcode'		=> $wpcargo->barcode( $shipment_id ),
                    'packageSettings'	=> $mp_settings,
                    'cargoSettings'	=> $setting_options,
                    'packages'		=> $packages,
                    'logo'			=> $logo,
                    'siteInfo'		=> $siteInfo
                );
                include( $template_path );
                do_action( 'wpcfe_after_bulkprint_template', $counter, $shipment_num, $print_type );
                $counter++;
            }   
        }
        ?>
        </body>
    </html>
    <?php
    $output = ob_get_clean();
	return $output;
}
// Print Shipment Functionality - Print Button with dropdown
add_action( 'wp_ajax_wpcfe_print_shipment', 'wpcfe_print_shipment_ajax_callback' );
function wpcfe_print_shipment_ajax_callback(){
    global $wpdb, $WPCCF_Fields, $wpcargo;
    // Variables
    $wpcfe_pdf_dpi  = apply_filters( 'wpcfe_pdf_dpi', 160 );
    $shipment_id    = $_POST['shipmentID'];
    $print_type     = $_POST['printType'];
    $print_paper    = wpcfe_print_paper()[$print_type];

    $directory      = WPCFE_PATH.'admin/includes/file-container/';
    // Clean directory before adding new file
    foreach( glob($directory.'*.pdf') as $pdf_file){
        unlink($pdf_file);
    }
    $waybill_title  = $print_type.'-'.preg_replace("/[^A-Za-z0-9 ]/", '', get_the_title($shipment_id) ).'-'.time();
	
	  $output=wpcfe_print_shipment_template_path( $shipment_id, $waybill_title, $print_type );



   $output .= '<script type="text/javascript">
   window.onload = function() { window.print(); }
</script>';


   if( file_put_contents( $directory.$waybill_title.'.html', $output) ){
    $data_info = array(
        'file_url' => WPCFE_URL.'admin/includes/file-container/'.$waybill_title.'.html',
        'file_name' => $waybill_title
    );  
}
echo json_encode( $data_info );
wp_die();

die();
	
    // instantiate and use the dompdf class
    $options 		= new WPCFE_Options();
    $options->setDpi( $wpcfe_pdf_dpi );
    $dompdf 		= new Dompdf( $options );
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->loadHtml( wpcfe_print_shipment_template_path( $shipment_id, $waybill_title, $print_type ) );
	
	
    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper( $print_paper['size'], $print_paper['orient']);
    // Render the HTML as PDF
    $dompdf->render();
    wpcfe_pdf_pagination( $dompdf, $print_type );
    // Output the generated PDF to Browser
    $output = $dompdf->output();
    $data_info = array();
    if( file_put_contents( $directory.$waybill_title.'.pdf', $output) ){
        $data_info = array(
            'file_url' => WPCFE_URL.'admin/includes/file-container/'.$waybill_title.'.pdf',
            'file_name' => $waybill_title
        );  
    }
    echo json_encode( $data_info );
    wp_die();
}
// Template Path
function wpcfe_print_shipment_template_path( $shipment_id, $waybill_title, $print_type ){
    ob_start();
	
	
    global $WPCCF_Fields, $wpcargo, $wpcargo_print_admin;
    $shipmentID             = $shipment_id;
    if( wpcfe_enable_label_multiple_print() && $print_type == 'label' ){
        $print_type         = $print_type.'-packages';
    }
    $custom_template_path   = get_stylesheet_directory() .'/wpcargo/'. $print_type.'.tpl.php';
	
    $mp_settings            = get_option('wpc_mp_settings');
    $setting_options        = get_option('wpcargo_option_settings');
    $packages               = maybe_unserialize( get_post_meta( $shipmentID,'wpc-multiple-package', TRUE) );
    $print_fonts 		    = wpcargo_print_fonts();
    $ffamily 			    = get_option('wpcargo_print_ffamily');
    $fsize 				    = get_option('wpcargo_print_fsize') ? get_option('wpcargo_print_fsize') : 12 ;
    $logo                   = '';
    if( !empty( $setting_options['settings_shipment_ship_logo'] ) ){
        $logo 		= '<img style="width: 180px;" id="logo" src="'.$setting_options['settings_shipment_ship_logo'].'">';
    }
    if( get_option('wpcargo_label_header') ){
        $siteInfo = get_option('wpcargo_label_header');
    }else{
        $siteInfo  = $logo;
        $siteInfo .= '<p style="margin:0;padding:0;font-size: 18px;">'.get_bloginfo('name').'</p>';
        $siteInfo .= '<p style="margin:0;padding:0;font-size: 14px;">'.get_bloginfo('description').'</p>';
        $siteInfo .= '<p style="margin:0;padding:0;font-size: 8px;">'.get_bloginfo('wpurl').'</p>';
    }
    $shipmentDetails 	= array(
        'shipmentID'	=> $shipment_id,
        'barcode'		=> $wpcargo->barcode( $shipment_id ),
        'packageSettings'	=> $mp_settings,
        'cargoSettings'	=> $setting_options,
        'packages'		=> $packages,
        'logo'			=> $logo,
        'siteInfo'		=> $siteInfo,
		'meta'			=> get_post_meta($shipment_id)
    );
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?> <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?>>
        <head>
        <title><?php echo $waybill_title; ?></title>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">         
            <?php if( wpcfe_customfont_enable() ): ?>
                @font-face {
                    font-family: 'Firefly Sung';
                    font-style: normal;
                    font-weight: 400;
                    src: url(http://eclecticgeek.com/dompdf/fonts/cjk/fireflysung.ttf) format('truetype');
                }
                *{
                    font-family: Firefly Sung, DejaVu Sans, sans-serif;
                }
            <?php else: ?>
                <?php if( $ffamily && array_key_exists( $ffamily, $print_fonts ) ): ?>
                    @import url('<?php echo $print_fonts[$ffamily]['url']; ?>');
                    *{
                        font-family: <?php echo $print_fonts[$ffamily]['fontfamily']; ?> !important;
                        font-size: <?php echo $fsize; ?>px;
                    } 
                <?php endif; ?>
            <?php endif; ?>
            html, body{ margin:0px; padding:0px; }
            h1, h2, h3, h4, h5, h6{ font-weight: normal !important; }
            h6, h5{ font-size: 1.1rem!important; }
            h4, h3{ font-size: 1.2rem!important; }
            h2{ font-size: 1.4rem!important; }
            h1{ font-size: 1.6rem!important; }
            .page_break { page-break-before: always; }
            @media screen, print{ .page_break { page-break-before: always; } }
        </style>
        </head>
        <body>
        <?php
        if( file_exists( $custom_template_path ) ){
            $template_path = $custom_template_path;
        }else{
            if( $print_type != 'waybill' ){
                $template_path  = include( WPCFE_PATH.'templates/print/'.$print_type.'.php' );
				
            }else{
                ?>
                <style type="text/css">
                    div.copy-section { border: 2px solid #000; margin-bottom: 18px; }
                    .copy-section table { border-collapse: collapse; }
                    .copy-section table td.align-center{ text-align: center; }
                    .copy-section table td { border: 1px solid #000; }
                    table tr td{ padding:6px; }
                </style>
                <?php
                $template_path =  apply_filters( 'label_template_url', $wpcargo_print_admin->print_label_template_callback(), $shipmentDetails );
            }
        }
        include_once( $template_path );
        ?>
        </body>
    </html>
    <?php
    $output = ob_get_clean();
	return $output;
}