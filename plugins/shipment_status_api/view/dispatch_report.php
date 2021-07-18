<?php
if (@$_GET['dispatchData']) {


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
                'value'   => $_GET['drive'],
                'compare' => '=',
            ),
            array(
                'key'     => 'wpcargo_status',
                'compare' => '=',
                'value'   => 'OUT FOR DELIVERY',
            )
        )
    );




    $start_date  = $_GET['from'];
    $end_date  = $_GET['to'];



    $posts = get_posts($query_args);




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
        $last_date = $post->post_date;
        $last_update = end($data);


        if (strtotime($last_date)  > strtotime($end_date)) {
            continue;
        }


        if (strtotime($last_date)  < strtotime($start_date)) {
            continue;
        }

        $countor[$meta['wpcargo_status'][0]]['count'] = $countor[$meta['wpcargo_status'][0]]['count'] + 1;


        $packages = $meta['packages'];
        $packages[0]['wpc-pm-description'];

        $header[] = array(
            'reference_number'   =>   @$meta['reference_number'][0],
            'reference_number'   =>   @$meta['reference_number'][0],
            'consignee_name'  =>   @$meta['consignee_name'][0],
            'wpcargo_receiver_address'    =>  @$meta['wpcargo_receiver_address'][0],
            'consignee_contact'    => @$meta['consignee_contact'][0],
            'wpc-multiple-package' => unserialize(unserialize(@$meta['wpc-multiple-package'][0]))[0]['wpc-pm-description'],
            'cod_amount' =>  @$meta['cod_amount'][0],
        );
    }
    if ($_GET['download']) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header, NULL, 'A1');

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
$drivers = get_users($args);

?>


<form class="formargin mt-10" id="formargin" method="get" enctype="multipart/form-data">
    <h2> Dispatch Report </h2>

    <div class="row">
        <div class="col-6 ">
            <label>Driver </label><br>
            <select style="display: block !important;" class="form-control" name="driver" required placeholder="driver">
                <option>
                    Select Driver
                </option>
                <?php
                foreach ($drivers as $driver) {
                ?>
                    <option <?php echo $_GET['driver'] == $driver->ID  ? 'Selected' : '' ?> value="<?php echo  $driver->ID ?>">
                        <?php echo  $driver->display_name ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>
    <div class="col-6 p-0">
        <label>Data Range</label>
        <div class="d-flex flex-row bd-highlight mr-4 ">
            <input class="form-control wpccf-datepicker picker__input col-6  " placeholder="From" value="<?php echo $_GET['from'] ?>" type="date" name="from">
            <input class="form-control wpccf-datepicker picker__input col-6 ml-2 " placeholder="To" value="<?php echo $_GET['to'] ?>" type="date" name="to">
            <input type="hidden" name="dispatchData" value="true">

        </div>

        <input type="reset" class="btn btn-primary btn-sm mt-20 ml-0" value="Reset">
        <input type="submit" class="btn btn-primary btn-sm ml-2 " value="search">
        <button onclick="myFunction(event)" id="formdata" class="btn btn-primary btn-sm ml-2">Export</button>
    </div>



</form>


<div>
    <div class="tablediv">
        <table id="bulk-list" class="table table-hover table-sm">
            <thead>
                <tr class="">
                    <br>
                    <br>

                </tr>
                <tr>
                    <th class="form-check">
                        ID
                    </th>
                    <th class="table-header">Referance Number</th>

                    <th class="table-header">Consignee Name</th>
                    <th class="table-header">Consignee Address</th>
                    <th class="table-header">Consignee Contact</th>
                    <th class="table-header">Product Description</th>
                    <th class="table-header">Cod Amount</th>
                    <th class="table-header">Receiver Sign</th>

                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($header as $val) {
                ?>
                    <tr>
                        <td>
                            <?php echo  $val['reference_number'] ?>
                        </td>
                        <td>
                            <?php echo  $val['reference_number'] ?>
                        </td>
                        <td>
                            <?php echo  $val['consignee_name'] ?>
                        </td>
                        <td>
                            <?php echo $val['wpcargo_receiver_address'] ?>
                        </td>
                        <td>
                            <?php echo $val['consignee_contact'] ?>
                        </td>
                        <td>
                            <?php echo $val['wpc-multiple-package'] ?>
                        </td>
                        <td>
                            <?php echo  $val['cod_amount'] ?>
                        </td>
                        <td>
                            <?php echo  $val['remarks'] ?>
                        </td>
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
    .tablediv td {
        text-align: left;
    }
    .tablediv {
        overflow: scroll;
        overflow-y: hidden;
    }

    .flex-row {
        margin-bottom: 15px;
    }

    p {
        margin-bottom: 1px;
    }

    @media screen and (min-width: 992px) {

        .tablediv {
            overflow: hidden;
        }
    }
</style>

<script>
    function myFunction(event) {
        console.log("Dispatch");
        event.preventDefault();
        var elements = document.getElementById("formargin").elements;
        var obj = {};

        for (var i = 0; i < elements.length; i++) {
            var item = elements.item(i);
            obj[item.name] = item.value;
        }
        var url = "/dispatch_report/?driver=" + obj['driver'] + "&from=" + obj['from'] + "&to=" + obj['to'] + "&dispatchData=true&download=true";
        window.open(url, '_blank');

    }
</script>