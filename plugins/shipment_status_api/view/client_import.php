
<?php


if (@$_GET['clientData']) {

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
                'value'   => $_GET['client'],
                'compare' => '=',
            ), 
            array(
                'key'     => 'wpcargo_status',
                'compare' => '=',
                'value'   => 'DELIVERED',
            )
        )
    );

    
    
    
    $start_date  = $_GET['from'];
    $end_date  = $_GET['to'];

    // if ($start_date && $end_date) {

    //     $query_args['date_query'] =
    //         array(
    //             'relation'   => 'OR',
    //             array(
    //                 array(
    //                     'column' => 'post_date',
    //                     'after' => $start_date . ' 00:00:00',
    //                     'before' => $end_date . ' 23:59:59',
    //                 ),
    //                 array(
    //                     'column' => 'post_modified',
    //                     'after' => $start_date . ' 00:00:00',
    //                     'before' => $end_date . ' 23:59:59',
    //                 )
    //             )
    //         );
    // }


    $posts = get_posts($query_args);



//      <div class=" col-4 ml-2">
//     <p>ASSIGNED CLIENT NAME&nbsp :---</p>
//     <p> DELIVERED DATE :---</p>
//     <p> SHIPMENT STATUS :- DELIVERED</p>
// </div>
    // $header is an array containing column headers
    //   $header = [array(
    //      "REFERENCE NUMBER",
    //      'PICKUP DATE', 
    //      "CONSIGNEE NAME",
    //      "LAST STATUS",
    //      "LAST UPDATE DATE",
    //     "COD AMOUNT",
    //     "REMARK",
  
    //  )]; 


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
        
           'reference_number' => @$meta['reference_number'][0],
            'wpcargo_pickup_date_picker'=>    @$meta['wpcargo_pickup_date_picker'][0],
           'consignee_name' =>@$meta['consignee_name'][0],
           'status' => @$meta['wpcargo_status'][0],
          'date'  =>$last_update['date'],  
          'cod_amount'=>  @$meta['cod_amount'][0],		

            'remarks' =>$last_update['remarks'],
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
    'role'    => 'wpcargo_client',
);
$users = get_users($args);



?> 
<form class="formargin"  id=
"formargin" method="get" enctype="multipart/form-data">
    <h2> Client Report </h2>

    <div class="row">
        <div class="col-6 ">
            <label>Client </label><br>
            <select style="display: block !important;" class="form-control" name="client" required placeholder="Client">
                <option>
                    --select--
                </option>
                <?php
                    foreach ($users as $user) {
                ?>
                <option value="<?php echo  $user->ID ?>"><?php echo  $user->display_name ?></option>
                <?php } ?>

            </select>
        </div>
   
    </div>

    <div class=" p-0 col-6">
        <label>Data Range</label>
        <div class="d-flex flex-row bd-highlight ">

            <input class="form-control wpccf-datepicker picker__input col-6  " placeholder="From" type="date"
                name="from">


            <input class="form-control wpccf-datepicker picker__input col-6 ml-2 " placeholder="To" type="date"
                name="to">
            <input type="hidden" name="clientData" value="true">
        </div>

        <input type="reset" class="btn btn-primary btn-sm mt-20 ml-0" value="Reset">
        <input type="submit" class="btn btn-primary btn-sm ml-2 " value="search">
        <button onclick="clientFunction()" class="btn btn-primary btn-sm ml-2" >Export</button>
    </div>

</form>

<div>
    <div class="tablediv">

        <table id="bulk-list" class="table table-hover table-sm">
            <thead>
                <tr>
                    <th class="form-check">
                        ID
                    </th>
                    <th class="table-header">Pick Up Date</th>
                    <th class="table-header">Referance Number</th>

                    <th class="table-header">Consignee Name</th>
                    <th class="table-header">Last Update Status</th>
                    <th class="table-header">Last Update Date</th>
                    <th class="table-header">Cod Amount</th>
                    <th class="table-header">Remark</th>

                </tr>
            </thead>
            <tbody>
          
     <?php
      foreach ($header as  $value)
       {
        $totalCOD =$totalCOD+$value['cod_amount'];
?>
                <tr>
                    <td >
                    <?php echo  $value['reference_number']?>
                    </td>
                    <td><?php echo  $value['wpcargo_pickup_date_picker']?></td>   
                    <td>
                        <?php echo  $value['reference_number']?>
                    </td>  
                    <td><?php echo  $value['consignee_name']?></td>
                    <td><?php echo  $value['status']?></td>
                    <td> <?php echo  $value['date']?></td>
                    <td> <?php echo  $value['cod_amount']?></td>
                    <td> <?php echo  $value['remarks']?></td>

                </tr>
           
                <?php } ?>
                      <tr>
                    <td colspan="5" style="text-align:left;">TOTAL SHIPMENT DELIVERED</td>
                    <td><?php echo count($header)?>
                    <td>

                </tr>
                <tr>
                    <td colspan="5" style="text-align:left">TOTAL COD AMOUNT </td>
                    <td></td>
                    <td><?php   echo $totalCOD?></td>

                </tr> 
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

    p {
        margin-bottom: 5px;
    }

    .flex-row {
        margin-bottom: 15px;
        padding-left: 0px;
    }

    @media screen and (min-width: 992px) {

        .tablediv {
            overflow: hidden;
        }
    }
</style>

<script>
     function clientFunction() {
       console.log("client");
       var elements = document.getElementById("formargin").elements;
     
       var obj ={};
        
       for(var i = 0 ; i < elements.length ; i++){
           var item = elements.item(i);
            obj[item.name] = item.value; 
         
       }
   
      var url="/client-report/?client="+obj['client']+"&from="+obj['from']+"&to="+obj['to']+"&clientData=true&download=true";
      window.open(url,'_blank');
   }
    </script>