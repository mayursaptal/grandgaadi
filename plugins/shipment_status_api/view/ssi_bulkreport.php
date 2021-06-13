<!-- code  -->
<div class="main-container"style="width: 100%;padding:10px";>
<div class="container">
    <form class="example" action=" " method="post" style="width: 100%;">
        <input type="text" placeholder="Search.." name="search">
        <button type="submit">search</button>
    </form>
    <?php



    $arr = (explode(",", get_option('wpcargo_option_settings')['settings_shipment_status']))
    ?>


    <div class="select-choice" style="display: flex;justify-content: start;">
        <div class="col-md-6 " style="padding:5px 0;">
		<div class="float-md-left float-lg-left" style="width: 100%;">
        <?php $users = get_users('wpcargo_driver') ?>
			<select  id="wpstatus_select1" name="wpcfesort" class="form-control md-form browser-default">
                <option value="">Status</option>
                
                <?php foreach ($arr as $val) {
            ?>

                <option value="<?php echo $val ?>"><?php echo $val ?> </option>

            <?php

            } ?>
							</select>
		</div>
	</div>





        <div class="col-md-6" style="padding:5px 10px;">
		<div class="float-md-right float-lg-right" style="width: 100%;">
        <?php $users = get_users(['role'=>'wpcargo_driver']) ?>
			<select id="wpdriver_select2" name="wpcfesort" class="form-control md-form browser-default">
                <option value="">Driver</option>
                
            <?php
            foreach ($users as $user) {
            ?>
                <option value="<?php echo  $user->ID ?>"><?php echo  $user->display_name ?></option>
            <?php } ?>
							</select> 
		</div>
	</div>
    </div>
  
    <input type="submit" id="wpstatus_select"  class="bulk-barcode-scan btn btn-secondary btn-sm waves-effect waves-light" style="width: 100px;padding: 5px;height: 40px;" value="Bulk Update">

           
    <input type="submit" id="wpdriver_select"  class="btn btn-primary btn-sm m-0" style="width: 100px;padding: 5px;height: 40px;" value="Export">
</div>

</div>

<!-- Table Start -->
<div>
    <div>
        <table id="bulk-list" class="table table-hover table-sm">
            <thead>
                <tr>
                    <th class="form-check">
                        <input class="form-check-input " id="wpcfe-select-all" type="checkbox" />
                        <label class="form-check-label" for="materialChecked2"></label>
                    </th>
                    <th class="table-header">Tracking Number</th>
                    <th class="table-header">Assigned Client</th>
                    <th class="table-header">Consignee Name</th>
                    <th class="table-header">Destiantion</th>
                    <th class="table-header">Pick Up Date</th>
                    <th class="table-header">Cod Amount</th>   
                    <th class="table-header">Status</th>
                    <th class="table-header">Driver</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php

                $post = array();
                $searchbar = $_POST['search'];

                $search_split = explode(" ", $searchbar);


                if ($search_split) {

                    foreach ($search_split as $x => $split) {
						
							if(empty($split)){
								continue;
							}
						
//                         $query_args = array(

//                             'posts_per_page'   => 1,
//                             'post_type' =>   'wpcargo_shipment',
//                             'title'      => $split,
// 							    'meta_key'         => 'reference_number',
//    								'meta_value'       => $split,

//                         );


//                         $posts[] = get_posts($query_args)[0];
//                         
                          $posts[] = get_page_by_title( $split , OBJECT , 'wpcargo_shipment');
                    }
                }


    $users = get_users(array('fields' => array('ID', 'display_name ')));


    $user_id = array();

    foreach ($users  as $user) {
        $user_id[$user->ID] = $user->display_name;
    }

                foreach ($posts as $post) {

                    $post_data = get_post_meta($post->ID);
					
					
// 					if(!in_array($post_data['reference_number'][0]  ,$search_split )){
// 						continue;
// 					}
					

                    // var_dump($post_data);
                ?>
                    <tr>

                        <td class="form-check">
                            <input class="wpcfe-shipments form-check-input" id="get" value="<?php echo $post->ID ?>" type="checkbox" />
                            <label class="form-check-label" for="materialChecked2"></label>
                        </td>
                        <td> <?php echo $post_data['reference_number'][0] ?></td>
                        <!-- <td>Assigned Cliend</td> -->
                        <td> <?php echo $post_data['shipper_name'][0] ?></td>
                        <td> <?php echo $post_data['consignee_name'][0] ?></td>
                        <td> <?php echo $post_data['wpcargo_destination'][0] ?></td>
                        <td><?php echo $post_data['wpcargo_pickup_date_picker'][0]?></td>
                        <td><?php echo $post_data['cod_amount'][0]?></td>
                        <td> <?php echo $post_data['wpcargo_status'][0] ?></td>
                        <!-- <td> <?php echo $post_data['wpcargo_type_of_shipment'][0] ?></td> -->
                        <!-- <td><?php echo $post_data['wpcargo_origin_field'][0] ?> </td> -->
                        
                        <td> <?php echo $user_id[$post_data['wpcargo_driver'][0] ]?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
                </div>
<!-- Table End-->


<style>
    a.dropdown-item {
        display: none;
    }

    a.dropdown-item:nth-last-child(2) {
        display: block;
    }
</style>

<script>
    jQuery(
        function($) {
            $('#wpdriver_select').click(function(e) {
                e.preventDefault();
                var val = [];
                var inputs = $('#bulk-list').find('input[type=checkbox]:checked');
                if (!$(this).is(':checked')) {
                    for (let index = 0; index < inputs.length; index++) {
                        const element = inputs[index];
                        val.push($(element).val());
                    }
                    $.post("", {
                        'ids': val
                    }, function(data, status) {
                        window.open(data);
                    });
                }

            });
        }
    );

    jQuery(
        function($) {
            $('#wpstatus_select').click(function(e) {
                e.preventDefault();
                var val = [];
                var inputs = $('#bulk-list').find('input[type=checkbox]:checked');
                if(!$(this).is(':checked'))
                { 
                    for (let index = 0; index < inputs.length; index++) {
                        const element =inputs[index];
                        val.push($(element).val());
                      
                    }
                   $.post("",{
                       'ids':val , 
                       'update_bulk' : true , 
                       'driver' : $('#wpdriver_select2').val(),
                       'status' : $('#wpstatus_select1').val(),
                   },function(data,status){
                       
                    alert(data);

                   })
                }
            });

        }

    );
</script>


<style>
    form.example input[type=text] {
        padding: 10px;
        font-size: 17px;
        border: 1px solid grey;
        float: left;
        width: 80%;
        background: #f1f1f1;
    }

    form.example button {
        float: left;
        width: 20%;
        padding: 10px;
        background: #2196F3;
        color: white;
        font-size: 17px;
        border: 1px solid grey;
        border-left: none;
        cursor: pointer;
    
    }
   span.waves-input-wrapper{
    margin:0;
    margin-right: 50px;
   }

    form.example button:hover {
        background: #0b7dda;
    }

    form.example::after {
        content: "";
        clear: both;
        display: table;
    }

    /* .waves-input-wrapper {
        margin: 0 50px;
    } */

    .container {
        width: 100%;
    }

    .menu-btn {
        display: flex;
        justify-content: start;
    }
</style>