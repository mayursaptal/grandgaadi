


<?php

if (@$_GET['driverData']) {



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

  


    $posts = get_posts($query_args);



    // $header is an array containing column headers
    $header = [array(
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

 
        $last_date = @$meta['wpcargo_pickup_date_picker'][0];
        $last_date = $post->post_date ;
        $last_update = end($data);
       

        
        if (strtotime($last_date)  > strtotime($end_date)) {
            continue;
        }


        if (strtotime($last_date)  < strtotime($start_date)) {
            continue;
        }
        



        $countor[$meta['wpcargo_status'][0]]['count'] = $countor[$meta['wpcargo_status'][0]]['count'] + 1;

        

  
         $header[] = array(
            
            'reference_number'=>@$meta['reference_number'][0],
            'consignee_name'=>@$meta['consignee_name'][0],
            'consignee_contact'=>@$meta['consignee_contact'][0],
            'comment_status'=>@$meta['comment_status'][0],
            'cod_amount'=>@$meta['cod_amount'][0],
            'remarks'=>$last_update['remarks'],

 
        );
    }
    
    if($_GET['download'])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header, NULL, 'A1');
    
        // redirect output to client browser
        header('Content-Disposition: attachment;filename="myfile.xlsx"');
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    
        exit();
    }
}
?>

<?php
$args = array(
    'role'    => "wpcargo_driver",

);
$drivers= get_users($args);

?>

<form class="formargin"  id="formargin" method="get" enctype="multipart/form-data">
    <h2> Driver Report </h2>

    <div class="row">
        <div class="col-6 ">
            <label>Driver </label><br>
            <select style="display: block !important;" class="form-control" name="client" required placeholder="Client">
                <option >
                    --select--
                </option>
                <?php
                    foreach ($drivers as $driver) {
                ?>
                <option value="<?php echo  $driver->ID ?>"><?php echo  $driver->display_name ?></option>
                <?php } ?>

            </select>
        </div>
    </div>

    <div class="col-6 p-0">
        <label>Data Range</label>
        <div class="d-flex flex-row bd-highlight mr-4">

            <input class="form-control wpccf-datepicker picker__input col-6  " placeholder="From" type="date"
                name="from">


            <input class="form-control wpccf-datepicker picker__input col-6 ml-2" placeholder="To" type="date"
                name="to">
            <input type="hidden" name="driverData" value="true">
          
        </div>

        <input type="reset" class="btn btn-primary btn-sm mt-20 ml-0" value="Reset">
        <input type="submit" class="btn btn-primary btn-sm ml-2 " value="search">
        <button  onclick="myFunction()" id="formdata" class="btn btn-primary btn-sm ml-2">Export</button>
    </div>


</form>



<div>
    <div class="tablediv">
        <table id="bulk-list" class="table table-hover table-sm">
            <thead>
                <tr>
                    <br>
                    <br>
                  
                </tr>
                <tr>
                    <th class="form-check">
                        ID
                    </th>
                    <th class="table-header">Referance Number</th>
                    <th class="table-header">Consignee Name</th>
                    <th class="table-header">Consignee Contact</th>
                    <th class="table-header">Shipment Status</th>
                    <th class="table-header">Cod Amount</th>
                    <th class="table-header">Remark</th>

                </tr>
            </thead>
            <tbody>
            <?php
                 foreach ($header as $value)
       {
?>

                <tr>
                    <td><?php echo  $value['reference_number']?></td>
                    <td>
                        <?php echo  $value['reference_number']?>
                    </td>
                    <td><?php echo  $value['consignee_name']?></td>
                    <td><?php echo $value['consignee_contact']?></td>
                    <td><?php echo  $value['comment_status']?></td>
                    <td><?php echo  $value['cod_amount']?></td>
                    <td><?php echo  $value['remarks']?></td>

                </tr>
<?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    h2 {
        margin-bottom: 30px;
    }

    .formargin {
        margin-top: 50px; 
        overflow: hidden;
        height: 100%;
    }

    .searchbar {
        padding: 10px;
        font-size: 17px;
        border: 1px solid grey;
        float: left;
        width: 80%;
        background: #f1f1f1;
    }

    .submitbtn {

        float: left;
        width: 20%;
        padding: 10px;
        background: #2196F3;
        color: white;
        font-size: 17px;
        border: 1px solid grey;
        border-left: none;
    }

    .tablediv {

        overflow: scroll;
        overflow-y: hidden;
    }

    @media only screen and (max-width: 600px) {}

    .flex-row {
        margin-bottom: 15px;
    }


    p {
        margin-bottom: 5px;
    }

    @media screen and (min-width: 992px) {

        .tablediv {
            overflow: hidden;
        }
    }
</style>
<script>
    function myFunction() {
        console.log("Driver");
       
        var elements = document.getElementById("formargin").elements;
      
        var obj ={};
         
        for(var i = 0 ; i < elements.length ; i++){
            var item = elements.item(i);
            obj[item.name] = item.value; 
        }
        var url="/driver_report/?client="+obj['client']+"&from="+obj['from']+"&to="+obj['to']+"&driverData=true&download=true";
        window.open(url,'_blank');
    
    }
</script>