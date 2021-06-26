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

// var_dump($shipmentDetails);



$shipper_contact 	= str_replace(wpccf_search_metakey_code(), wpccf_replace_metakey_code($shipmentDetails['shipmentID']), '{shipper_contact}');






?>

<?php do_action('wpc_label_before_header_information', $shipmentDetails['shipmentID']); ?>
<div>
	<table class="shipment-header-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;padding:10px;font-size:14px !important">
		<tr>
			<td class="align-center" style="width: 70%;align-items: center;">
				<?php echo the_custom_logo() ?> <h1 style="font-weight: 800;font-size:18px !important">GRANDGAADI PACKAGE DELIVERY SERVICE LLC.</h1>
				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;text-align:left;margin:10px auto;padding:0;">
					<tr>
						<th style="padding-left: 8px;">ORDER DETAILS</th>
					</tr>
					<tr>


						<td>
							<?php echo  $header_cell2; ?><br>

							<?php
							$total_weight = 0;

							foreach ($shipmentDetails['packages'] as $val) {


								$total_weight +=	@($val["wpc-pm-weight"]);
							} ?>
							WEIGHT : <?php echo $total_weight ?> <br>
							<?php echo  $content_cell8; ?><br>
						</td>
					</tr>

				</table>
				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:10px auto;padding:0;text-align:left">

					<tr>
						<th style="padding-left: 8px;">SENDER DETAILS</th>
					</tr>
					<tr>
						<td>
							<?php
							$SHIPPER_CONTACT = 0;
							// $SHIPPER_CONTACT= $shipmentDetails['shipper_contact'];



							// var_dump($shipmentDetails);

							foreach ($shipmentDetails as $key => $val) {
								// var_dump("Key",$kay=>"Value",$val);
								// $SHIPPER_NAME= 
								// var_dump($val["shipper_name"]);
								// $SHIPPER_CONTACT=
								//  var_dump($val[" shipper_contact"]);

							}
							?>
							Name : <?php echo  $content_cell1; ?><br>
							<!-- add sender  phone number  -->
							PHONE:<?php echo $shipper_contact ?><br>
							<?php echo  $content_cell4; ?><br>

						</td>
					</tr>


				</table>
				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:10px auto;padding:0;text-align:left">

					<tr>
						<th style="padding-left: 8px;">RECEIVER DETAILS </th>
					</tr>
					<tr>
						<td style=" display: flex; justify-content: left;">
							NAME:<?php echo  $content_cell2; ?><br>
							<?php echo $header_cell9 ?><br>
							<?php echo  $content_cell5; ?><br>
						</td>
					</tr>
				</table>
				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:10px auto;padding:0;text-align:left">

					<tr>
						<th style="padding-left: 8px;">TERMS & CONDITIONS</th>
					</tr>
					<tr>
						<td style=" display: flex; justify-content: left;">
							GrandGaadi Package Delivery Service LLC is not liable for any exchange or refund, For any complaints, kindly contact the shipper details
						</td>
					</tr>
				</table>
			</td>
			<td rowspan="2" class="align-center" style="width: 30%; text-align: center;">
				<img style="float: none !important; margin: 5px !important; width: auto;height: 40px;" src="<?php echo $shipmentDetails['barcode']; ?>" alt="<?php echo get_the_title($shipmentDetails['shipmentID']); ?>" />
				<p style="margin:10px;padding:0;font-weight: bold;"><?php echo $header_cell6; ?><br></p>

				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:10px auto;padding:10px;text-align:left">

					<tr>
						<th style="padding-left: 8px;">PRODUCT DESCRIPTION</th>
					</tr>
					<tr>

						<td>
							<?php echo  $content_cell7; ?><br>
						</td>
					</tr>

				</table>
				<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000; width:100%;margin:10px auto;padding:10px;">
					<tr rowspan="3">
					<tr>
					<tr class="align-center">
						<td style="font-weight: 600; font-size: 16px;"><?php echo $header_cell3; ?><br></td>
					</tr>
					<tr class="align-center">
						<td style="font-weight: 600; font-size: 14px;">
							<?php echo $header_cell5; ?>
						</td>
					</tr>
					<tr class="align-center">
						<td>
							<?php echo $header_cell4; ?>
						</td>
					</tr>

					<tr class="align-center">
						<td>

							<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:10px auto;padding:10px;text-align:left">

								<tr>
									<th style="padding-left: 8px;">PROOF OF DELIVERY</th>
								</tr>
								<tr>

									<td>
									Receiver Name:
									</td>
								</tr>
								<tr>

									<td>
									Emirates ID /No.:
									</td>
								</tr>
								<tr>

									<td>
									Receiver Sign::
									</td>
								</tr>

							</table>

						</td>
					</tr>


				</table>

			</td>
		</tr>
	</table>
	<?php
	/*$mp_settings = $shipmentDetails['packageSettings'];
	if ($mp_settings) {
		//** Checked in multiple setting has value
		if (array_key_exists('wpc_mp_enable_admin', $mp_settings)) {
			//** Check if the multiple package is Enable
			$packages = $shipmentDetails['packages'];
			if (!empty($packages)) {
				//** Check if package array is not empty
				if (count($packages) == 1) {
					//** Check if package array has value and not empty
					$package = array_filter($packages[0]);
					if (!empty($package)) {
	?>
						<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
							<thead>
								<tr>
									<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<?php endif; ?>
									<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($packages as $package) {
								?>
									<tr>
										<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
										<td><?php echo $package['wpc-pm-qty']; ?></td>
										<td><?php echo $package['wpc-pm-piece-type']; ?></td>
										<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
											<td><?php echo $package['wpc-pm-length']; ?></td>
											<td><?php echo $package['wpc-pm-width']; ?></td>
											<td><?php echo $package['wpc-pm-height']; ?></td>
										<?php endif; ?>
										<td><?php echo $package['wpc-pm-weight']; ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					<?php
					}
				} else {
					?>
					<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
						<thead>
							<tr>
								<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
								<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
									<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
								<?php endif; ?>
								<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($packages as $package) {
							?>
								<tr>
									<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
									<td><?php echo $package['wpc-pm-qty']; ?></td>
									<td><?php echo $package['wpc-pm-piece-type']; ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php echo $package['wpc-pm-length']; ?></td>
										<td><?php echo $package['wpc-pm-width']; ?></td>
										<td><?php echo $package['wpc-pm-height']; ?></td>
									<?php endif; ?>
									<td><?php echo $package['wpc-pm-weight']; ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
	<?php
				}
			}
		}
	}
	?>
</div><!-- account copy -->
<div id="consignee-copy" class="copy-section">
	<table class="shipment-header-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:0;padding:0;">
		<tr>
			<td rowspan="3" class="align-center">
				<?php echo $shipmentDetails['logo']; ?>
			</td>
			<td rowspan="3" class="align-center">
				<img style="float: none !important; margin: 0 !important; width: 180px;height: 50px;" src="<?php echo $shipmentDetails['barcode']; ?>" alt="<?php echo get_the_title($shipmentDetails['shipmentID']); ?>" />
				<p style="margin:0;padding:0;font-weight: bold;"><?php echo get_the_title($shipmentDetails['shipmentID']); ?></p>
				<?php do_action('wpc_label_header_barcode_information', $shipmentDetails['shipmentID']); ?>
				<span class="copy-label"><?php esc_html_e('Consignee Copy', 'wpcargo-custom-field'); ?></span>
			</td>
			<td><?php echo $header_cell1; ?></td>
			<td><?php echo $header_cell2; ?></td>
			<td><?php echo $header_cell3; ?></td>
		</tr>
		<tr>
			<td><?php echo $header_cell4; ?></td>
			<td><?php echo $header_cell5; ?></td>
			<td><?php echo $header_cell6; ?></td>
		</tr>
		<tr>
			<td><?php echo $header_cell7; ?></td>
			<td><?php echo $header_cell8; ?></td>
			<td><?php echo $header_cell9; ?></td>
		</tr>
		<tr>
		</tr>
	</table>
	<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:0;padding:0;">
		<tr>
			<td><?php esc_html_e('Shipper', 'wpcargo-custom-field'); ?></td>
			<td><?php echo $content_cell1; ?></td>
			<td><?php esc_html_e('Consignee', 'wpcargo-custom-field'); ?></td>
			<td><?php echo $content_cell2; ?></td>
			<td colspan="2"><?php echo $content_cell3; ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $content_cell4; ?></td>
			<td colspan="2"><?php echo $content_cell5; ?></td>
			<td colspan="2" rowspan="3" style="vertical-align: baseline;"><?php echo $content_cell6; ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $content_cell7; ?></td>
			<td colspan="2"><?php echo $content_cell8; ?></td>
		</tr>
	</table>
	<?php
	$mp_settings = $shipmentDetails['packageSettings'];
	if ($mp_settings) {
		//** Checked in multiple setting has value
		if (array_key_exists('wpc_mp_enable_admin', $mp_settings)) {
			//** Check if the multiple package is Enable
			$packages = $shipmentDetails['packages'];
			if (!empty($packages)) {
				//** Check if package array is not empty
				if (count($packages) == 1) {
					//** Check if package array has value and not empty
					$package = array_filter($packages[0]);
					if (!empty($package)) {
	?>
						<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
							<thead>
								<tr>
									<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<?php endif; ?>
									<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($packages as $package) {
								?>
									<tr>
										<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
										<td><?php echo $package['wpc-pm-qty']; ?></td>
										<td><?php echo $package['wpc-pm-piece-type']; ?></td>
										<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
											<td><?php echo $package['wpc-pm-length']; ?></td>
											<td><?php echo $package['wpc-pm-width']; ?></td>
											<td><?php echo $package['wpc-pm-height']; ?></td>
										<?php endif; ?>
										<td><?php echo $package['wpc-pm-weight']; ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					<?php
					}
				} else {
					?>
					<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
						<thead>
							<tr>
								<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
								<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
									<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
								<?php endif; ?>
								<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($packages as $package) {
							?>
								<tr>
									<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
									<td><?php echo $package['wpc-pm-qty']; ?></td>
									<td><?php echo $package['wpc-pm-piece-type']; ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php echo $package['wpc-pm-length']; ?></td>
										<td><?php echo $package['wpc-pm-width']; ?></td>
										<td><?php echo $package['wpc-pm-height']; ?></td>
									<?php endif; ?>
									<td><?php echo $package['wpc-pm-weight']; ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
	<?php
				}
			}
		}
	}
	?>
</div><!-- Consignee copy -->
<div id="shippers-copy" class="copy-section">
	<table class="shipment-header-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:0;padding:0;">
		<tr>
			<td rowspan="3" class="align-center">
				<?php echo $shipmentDetails['logo']; ?>
			</td>
			<td rowspan="3" class="align-center">
				<img style="float: none !important; margin: 0 !important; width: 180px;height: 50px;" src="<?php echo $shipmentDetails['barcode']; ?>" alt="<?php echo get_the_title($shipmentDetails['shipmentID']); ?>" />
				<p style="margin:0;padding:0;font-weight: bold;"><?php echo get_the_title($shipmentDetails['shipmentID']); ?></p>
				<?php do_action('wpc_label_header_barcode_information', $shipmentDetails['shipmentID']); ?>
				<span class="copy-label"><?php esc_html_e('Shippers Copy', 'wpcargo-custom-field'); ?></span>
			</td>
			<td><?php echo $header_cell1; ?></td>
			<td><?php echo $header_cell2; ?></td>
			<td><?php echo $header_cell3; ?></td>
		</tr>
		<tr>
			<td><?php echo $header_cell4; ?></td>
			<td><?php echo $header_cell5; ?></td>
			<td><?php echo $header_cell6; ?></td>
		</tr>
		<tr>
			<td><?php echo $header_cell7; ?></td>
			<td><?php echo $header_cell8; ?></td>
			<td><?php echo $header_cell9; ?></td>
		</tr>
		<tr>
		</tr>
	</table>
	<table class="shipment-info-table" cellpadding="0" cellspacing="0" style="border: 1px solid #000;width: 100%;margin:0;padding:0;">
		<tr>
			<td><?php esc_html_e('Shipper', 'wpcargo-custom-field'); ?></td>
			<td><?php echo $content_cell1; ?></td>
			<td><?php esc_html_e('Consignee', 'wpcargo-custom-field'); ?></td>
			<td><?php echo $content_cell2; ?></td>
			<td colspan="2"><?php echo $content_cell3; ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $content_cell4; ?></td>
			<td colspan="2"><?php echo $content_cell5; ?></td>
			<td colspan="2" rowspan="3" style="vertical-align: baseline;"><?php echo $content_cell6; ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $content_cell7; ?></td>
			<td colspan="2"><?php echo $content_cell8; ?></td>
		</tr>
	</table>
	<?php
	$mp_settings = $shipmentDetails['packageSettings'];
	if ($mp_settings) {
		//** Checked in multiple setting has value
		if (array_key_exists('wpc_mp_enable_admin', $mp_settings)) {
			//** Check if the multiple package is Enable
			$packages = $shipmentDetails['packages'];
			if (!empty($packages)) {
				//** Check if package array is not empty
				if (count($packages) == 1) {
					//** Check if package array has value and not empty
					$package = array_filter($packages[0]);
					if (!empty($package)) {
	?>
						<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
							<thead>
								<tr>
									<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
									<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
										<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<?php endif; ?>
									<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($packages as $package) {
								?>
									<tr>
										<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
										<td><?php echo $package['wpc-pm-qty']; ?></td>
										<td><?php echo $package['wpc-pm-piece-type']; ?></td>
										<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
											<td><?php echo $package['wpc-pm-length']; ?></td>
											<td><?php echo $package['wpc-pm-width']; ?></td>
											<td><?php echo $package['wpc-pm-height']; ?></td>
										<?php endif; ?>
										<td><?php echo $package['wpc-pm-weight']; ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					<?php
					}
				} else {
					?>
					<table id="shipment-packages" cellpadding="0" cellspacing="0" style="width: 100%;border: none;margin:0;padding:0;">
						<thead>
							<tr>
								<td class="package-description"><?php esc_html_e('Description', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Qty.', 'wpcargo-custom-field'); ?></td>
								<td><?php esc_html_e('Piece Type', 'wpcargo-custom-field'); ?></td>
								<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
									<td><?php esc_html_e('Length', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Width', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
									<td><?php esc_html_e('Height', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_dimension_unit']; ?>)</td>
								<?php endif; ?>
								<td><?php esc_html_e('Weight', 'wpcargo-custom-field'); ?> (<?php echo $mp_settings['wpc_mp_weight_unit']; ?>)</td>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($packages as $package) {
							?>
								<tr>
									<td class="package-description"><?php echo $package['wpc-pm-description']; ?></td>
									<td><?php echo $package['wpc-pm-qty']; ?></td>
									<td><?php echo $package['wpc-pm-piece-type']; ?></td>
									<?php if (array_key_exists('wpc_mp_enable_dimension_unit', $mp_settings)) : ?>
										<td><?php echo $package['wpc-pm-length']; ?></td>
										<td><?php echo $package['wpc-pm-width']; ?></td>
										<td><?php echo $package['wpc-pm-height']; ?></td>
									<?php endif; ?>
									<td><?php echo $package['wpc-pm-weight']; ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
	<?php
				}
			}
		}
	}
	?>
</div><!-- Shippers copy -->
<?php do_action('wpc_label_footer_information', $shipmentDetails['shipmentID']); ?>
</div>
<div style="text-align: center; margin:12px 0;">
	<a href="#" class="button button-secondary print" onclick="wpcargo_print('print-label')"><span class="ti-printer"></span> <?php esc_html_e('Print File', 'wpcargo-custom-field'); ?></a>
</div>*/
	?>
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
		}

		table {

			border-collapse: collapse;
		}

		@media screen,
		print {
			.page_break {
				page-break-before: auto !important;
			}

			table {page-break-inside: avoid;}

		}
	</style>

	<div style="height:50px">

	</div>