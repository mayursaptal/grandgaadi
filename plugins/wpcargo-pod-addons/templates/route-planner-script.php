
<script>
    function initPODRouteMap() {
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        const map = new google.maps.Map(document.getElementById("wpcpod-route-map"), {
            zoom: 6,
        });
        directionsRenderer.setMap(map);

		// AJAX 
		jQuery.ajax({
            type:"POST",
            data:{
                action  : 'wpcpod_generate_route_address',    
            },
            url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            beforeSend:function(){
            },
            success:function(response){
				$('#wpcpod-route-planner #wpcpod-route-map').css("height", "<?php echo apply_filters( 'wpcpod_route_map_height', 600 ); ?>px");
				$('#wpcpod-route-planner #wpcpod-route-loader').remove();
				if( response.status == 'success'){
					var wayPoints = [];
					jQuery.each( response.waypoints, function( index, value ){
						wayPoints.push( {
							location: value,
							stopover: true,
						} );
					} );

					calculateAndDisplayRoute(directionsService, directionsRenderer, response.origin, response.destination, wayPoints, response.shipments );
				}else{
                    $('#wpcpod-route-planner #wpcpod-route-map').remove();
                    $('#wpcpod-route-planner #route-planner-content').append('<div class="my-4 alert alert-info text-center">'+response.message+'</div>')
                }              
            }
        });

	}
	
    function calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination, wayPoints, shipments) {
        const waypts = wayPoints;
        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.WALKING,
            },
            (response, status) => {
            if (status === "OK") {

                directionsRenderer.setDirections(response);
                const route = response.routes[0];
                const summaryPanel = document.getElementById("directions-panel");
                summaryPanel.innerHTML = "";

                // For each route, display summary information.
                for (let i = 0; i < route.legs.length; i++) {
                
                    const routeSegment = i + 1;
                    summaryPanel.innerHTML +=
                        "<b><?php echo esc_html_e( 'Route Segment', 'wpcargo-pod' ); ?>: " + routeSegment + "</b><br>";
                    summaryPanel.innerHTML += route.legs[i].start_address + " <?php echo esc_html_e( 'to', 'wpcargo-pod' ); ?> ";
                    summaryPanel.innerHTML += route.legs[i].end_address + "<br>";
                    summaryPanel.innerHTML +=
                        "<?php echo esc_html_e( 'Shipment Number', 'wpcargo-pod' ); ?>: <b>" + shipments[i] +"</b><br>";
                    summaryPanel.innerHTML +=
                        route.legs[i].distance.text + "<br><br>";

                }
            } else {
                window.alert("<?php echo esc_html_e( 'Directions request failed due to', 'wpcargo-pod' ); ?> " + status);
            }
        });
	}
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('shmap_api'); ?>&callback=initPODRouteMap&libraries=&v=weekly"
    defer
></script>