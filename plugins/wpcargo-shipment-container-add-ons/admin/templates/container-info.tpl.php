<div id="container-info">
	<p id="container-header" class="section-header"><?php esc_html_e( 'Shipment Container Information', 'wpcargo-shipment-container' ); ?></p>
    <div id="container-details">
    	<div id="flight-info" class="one-third first">
        	<h1><?php esc_html_e( 'Container Information', 'wpcargo-shipment-container' ); ?></h1>
            <p class="label"><?php esc_html_e( 'Flight/Container No.', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'container_no', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Agent name', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'container_agent', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Telephone', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'container_tel', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Passport', 'wpcargo-shipment-container' ) ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'passport', true ); ?></p>
        </div><!-- #flight-info -->
        <div id="trip-info" class="one-third">
            <h1><?php esc_html_e( 'Trip Information', 'wpcargo-shipment-container' ); ?></h1>
            <p class="label"><?php esc_html_e( 'Origin port', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'origin', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Destination port', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'destination', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Delivery Agent', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'delivery_agent', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Telephone', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'delivery_tel', true ); ?></p>
        </div><!-- #container-info -->
        <div id="time-info" class="one-third">
            <h1><?php esc_html_e( 'Time Information', 'wpcargo-shipment-container' ); ?></h1>
            <p class="label"><?php esc_html_e( 'Date', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'date', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Time', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'time', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Expected Date', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'expected_date', true ); ?></p>
            <p class="label"><?php esc_html_e( 'Travel Mode', 'wpcargo-shipment-container' ); ?></p>
            <p class="label-info"><?php echo get_post_meta( $container_id, 'travel_mode', true ); ?></p>
        </div><!-- #time-info -->
    </div><!-- #container-details -->
</div><!-- #container-info -->