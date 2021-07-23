<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php echo is_rtl() ? 'dir="rtl"' : '' ; ?>>
	<head>
		<title><?php bloginfo( 'name' ); ?> | <?php _e('Shipping Manifest', 'wpcargo-shipment-container'); ?></title>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
			*{ margin:0; padding:0; }
			<?php if( function_exists('wpcfe_customfont_enable') && wpcfe_customfont_enable() ): ?>
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
			body{ margin: 18px 0; }
            h1, h2, h3, h4, h5, h6{ font-weight: normal !important; }
            h6, h5{ font-size: 1.1rem!important; }
            h4, h3{ font-size: 1.2rem!important; }
            h2{ font-size: 1.4rem!important; }
            h1{ font-size: 1.6rem!important; }
			table{ width:100%; border-collapse:collapse;}
			table tr td{ padding:6px; }
			#wpcsc-manifest-header{ margin-bottom:36px; padding-bottom:18px; display: block; border-bottom: 2px solid #000; }
			#delivery-manifest{ padding:18px; }
			table{ width:100%; margin-bottom: 18px; }
			.container-info td{ width: 50% !important; }
			#wpcsc-manifest-footer, #wpcsc-manifest-header, table thead td, table td{ font-size: 12px !important; }
			table thead td{	 font-weight: bold; text-align: center; }
			table td{ vertical-align: baseline; }
			#container-shipments table{ border-collapse: collapse; }
			#container-shipments table thead tr{ background-color: #000; }
			#container-shipments table thead tr td{ color: #fff; }
			#container-shipments table td{ border: 1px solid #000; padding: 4px; }
			.acknowledgement{ margin: 18px 0;}
            .page_break { page-break-before: always; }
			
		</style>
	</head>
	<body>
		<?php include_once( wpcsc_include_template( 'delivery-manifest.tpl' ) ); ?>
	</body>
</html>