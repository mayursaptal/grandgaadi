
<script>
    let map = null;
    function initPODRouteMap() {
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        map = new google.maps.Map(document.getElementById("wpcpod-route-map"), {
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
                if( response.status == 'success'){
                    var wayPoints = [];
                    jQuery.each( response.waypoints, function( index, value ){
                        wayPoints.push( {
                            location: value.address,
                            stopover: true,
                        } );
                    } );
                    calculateAndDisplayRoute(directionsService, directionsRenderer, response.origin, response.destination, wayPoints, response.shipments, response.poo );
                }else{
                    $('#wpcpod-route-planner #wpcpod-route-map').remove();
                    $('#wpcpod-route-planner #wpcpod-route-loader').remove();
                    $('#wpcpod-route-planner #route-planner-content').append('<div class="my-4 alert alert-info text-center">'+response.message+'</div>')
                }              
            }
        });
    }
    function renderMarker( data, content, end = true, warehouse = false ){
        const position  = end ? data.end_location : data.start_location ;
        const image     = warehouse ? 'origin.png' : 'parcel.png' ;
        const title     = end ? data.end_address : data.start_address ;
        const oMarker   = new google.maps.Marker({
            position: position,
            map,
            icon: "<?php echo WPCARGO_POD_URL.'assets/img/';  ?>"+image,
            title: title,
        });
        const infowindow = new google.maps.InfoWindow({
            content: content,
        });
        oMarker.addListener("click", () => {
            infowindow.open(map, oMarker);
        });
    }
    function calculateAndDisplayRoute(directionsService, directionsRenderer, origin, destination, wayPoints, shipments, poo) {
        const waypts = wayPoints; 
        const summaryPanel = document.getElementById("directions-panel");
        const singleParcel = shipments.length === 1 ? true : false ;
        directionsService.route({
            origin: origin.address,
            destination: destination.address,
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING,
            }, (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    const route = response.routes[0];
                    if( poo ){
                        var leg         = response.routes[ 0 ].legs[ 0 ];
                        let sContent = `
                            <b><?php _e( 'Point of Origin', 'wpcargo-pod' ); ?>:</b><br>
                            <b><?php _e( 'Distance', 'wpcargo-pod' ); ?>: ${leg.distance.text}</b><br>
                            ${leg.start_address} <b><?php _e( 'to', 'wpcargo-pod' ); ?></b> ${leg.end_address}
                        `;
                        renderMarker( leg, sContent, false, true );
                    }
                    // For each route, display summary information.
                    for (let i = 0; i < route.legs.length; i++) {   
                        const routeSegment = i + 1;
                        let content = `
                            <b><?php _e( 'Route Segment', 'wpcargo-pod' ); ?>: ${routeSegment}</b><br>
                            <b><?php _e( 'Distance', 'wpcargo-pod' ); ?>: ${route.legs[i].distance.text}</b><br>
                            ${route.legs[i].start_address} <b><?php _e( 'to', 'wpcargo-pod' ); ?></b> ${route.legs[i].end_address}<br>
                            <?php _e( 'Shipment Number', 'wpcargo-pod' ); ?>: <b>${shipments[i]['number']}</b><br>
                        `;
                        if ('info' in shipments[i] ) {
                            for (const [key, value] of Object.entries( shipments[i]['info'] )) {
                                if( value == ''){
                                    continue;
                                }
                                content += value + "<br>";
                            }
                        }
                        renderMarker( route.legs[i], content );
                        if( route.legs.length === i + 1 ){
                            renderMarker( route.legs[i], content );
                        }
                        summaryPanel.innerHTML += '<section class="border-bottom mb-4 pb-4" >'+content +'<section>';
                    }
                    $('#wpcpod-route-planner #wpcpod-route-map').removeClass('d-none');
                } else {
                    summaryPanel.innerHTML = "<?php _e( 'Directions request failed due to', 'wpcargo-pod' ); ?> " + status;
                }
                $('#wpcpod-route-planner #wpcpod-route-loader').remove();
            });
    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('shmap_api'); ?>&callback=initPODRouteMap&libraries=&v=weekly"
    defer
></script>