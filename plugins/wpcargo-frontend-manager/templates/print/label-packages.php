<?php $_totalCount  = count( $packages ); ?>
<?php $_counter     = 1; 
// var_dump($shipmentDetails);
?>
<?php

if(false){
foreach ($packages as $package ): ?> 
    <?php do_action( 'wpcfe_before_label_content', $shipmentDetails, $packages, $package, $_counter ); ?>
    <table style="width:100%;">
        <?php do_action( 'wpcfe_start_label_section', $shipmentDetails, $packages, $package, $_counter ); ?>
        <tr>
            <td style="width:50% !important; vertical-align: middle !important; text-align: center !important;">
                <?php do_action( 'wpcfe_label_site_info', $shipmentDetails, $packages, $package, $_counter ); ?>
            </td>
            <td style="width:50% !important; padding-right:18px;">
                <?php do_action( 'wpcfe_label_from_info', $shipmentDetails, $packages, $package, $_counter ); ?>
            </td>
        </tr>
        <?php do_action( 'wpcfe_middle_label_section', $shipmentDetails, $packages, $package, $_counter ); ?>
        <tr>
            <td colspan="2" style="padding-left:28px;">
                <?php do_action( 'wpcfe_label_to_info', $shipmentDetails, $packages, $package, $_counter ); ?>
            </td>
        </tr>
        <?php do_action( 'wpcfe_end_label_section', $shipmentDetails, $packages, $package, $_counter ); ?>
    </table>
    <?php do_action( 'wpcfe_after_label_content', $shipmentDetails, $packages, $package, $_counter ); ?>
    <?php if( $_totalCount == $_counter ){ continue; } ?>
    <div class="page_break"></div>
    <?php $_counter++; ?>
<?php endforeach; 
}
$val = wpccf_replace_metakey_code($shipmentDetails['shipmentID']);

?>


<?php
$print_label_settings = get_option('wpccf_print_label_settings');
$header_cell1 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell1']);
$header_cell2 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell2']);
$header_cell3 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell3']);
$header_cell4 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell4']);
$header_cell5 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell5']);
$header_cell6 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell6']);
$header_cell7 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell7']);
$header_cell8 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell8']);
$header_cell9 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['header_cell9']);
$content_cell1 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell1']);
$content_cell2 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell2']);
$content_cell3 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell3']);
$content_cell4 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell4']);
$content_cell5 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell5']);
$content_cell6 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell6']);
$content_cell7 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell7']);
$content_cell8 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), $print_label_settings['content_cell8']);
$content_cell9 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), " 12478");
$content_cell10 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), " 30");
$content_cell11 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), " 95524884545");


$shipper_contact 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), '{shipper_contact}');



//var_dump(wpccf_replace_metakey_code($shipmentDetails['shipmentID']));

//var_dump($meta);


?>

<table>

	
	<tr>
	<td colspan="2" style="    text-align: center;
">
	  <?php echo $shipmentDetails['logo']?>
	</td>	
		
<tr>
	<td colspan="2"  style="    text-align: center;
">
		<img src="<?php echo $shipmentDetails['barcode']?>"> <br>
		
		<?php echo @explode(':', $header_cell6)[1] ? explode(':', $header_cell6)[1] : $header_cell6?>
	</td>	
</tr>	
	
</tr>	
<tr>
	<td>
	REFERENCE NUMBER 
	</td>	
	<td>
		
		<?php echo @explode(':', $header_cell6)[1] ? explode(':', $header_cell6)[1] : $header_cell6?>
	</td>
</tr>	
	
	<tr>
	<td>
	SHIPPER NAME
	</td>	
	<td>
	
		<?php echo @explode(':', $content_cell1)[1] ? explode(':', $content_cell1)[1] : $content_cell1?>
	</td>
</tr>	

	
	<tr>
	<td>
	CONSIGNEE NAME
	</td>	
	<td>

		<?php echo @explode(':', $content_cell2)[1] ? explode(':', $content_cell2)[1] : $content_cell2?>
	</td>
</tr>	

		<tr>
	<td>
	CONSIGNEE ADRESS 
	</td>	
	<td>
	
		<?php echo @explode(':', $content_cell5)[1] ? explode(':', $content_cell5)[1] : $content_cell5?>
	</td>
</tr>	
	
	
		<tr>
	<td>
	CONSIGNEE CONTACT
	</td>	
	<td>
	
		
		<?php echo @explode(':', $header_cell9)[1] ? explode(':', $header_cell9)[1] : $header_cell9?>
	</td>
</tr>
	
		<tr>
	<td>
	DESTINATION
	</td>	
	<td>
	
		<?php echo @explode(':', $header_cell5)[1] ? explode(':', $header_cell5)[1] : $header_cell5?>
	</td>
</tr>
	
			<tr>
	<td>
	COD AMOUNT
	</td>	
	<td>
	
		<?php echo @explode(':', $header_cell3)[1] ? explode(':', $header_cell3)[1] : $header_cell3?>
	</td>
</tr>
				<tr>
	<td>
	PRODUCT DESCRIPTION
	</td>	
	<td>
	<?php echo @explode(':', $content_cell7)[1] ? explode(':', $content_cell7)[1] : $content_cell7?>
	</td>
</tr>
	
	
</table>

	<style>
		.notice {
			display: none;
		}

		.error {
			display: none;
		}

		table,
		td,
		th {
			border: 1px solid black;
			padding:10px;
		
		}

		table {
			width:100%;
			border-collapse: collapse;
		}

		@media screen,
		print {
			.page_break {
				page-break-before: auto !important;
			}

			table {page-break-inside: avoid!important;}

		}
	</style>

	<div style="height:50px">

	</div>
