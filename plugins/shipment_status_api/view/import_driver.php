<?php
set_time_limit(0);
$str_CSV = file_get_contents($_FILES['csv_file_driver']['tmp_name']);

$lines = explode("\n", $str_CSV);
$head = str_getcsv(array_shift($lines));
$array = array();
foreach ($lines as $line) {
    $row[] = array_combine($head, str_getcsv($line));
}

$status = 'OUT FOR DELIVERY';
foreach ($row as $index => $data) {
    foreach ($data as $driver_header => $reference_number) {
        $driver = trim($driver_header, '"');
        $user = get_user_by('login', $driver);
        if ($user) {
            $driver_id = $user->ID;
            $post = get_posts(array(
                'meta_key'   => 'reference_number',
                'meta_value' =>  $reference_number,
                'post_type' =>   'wpcargo_shipment'
            ));
            $meta = get_post_meta($post[0]->ID);

            if ($meta &&  $post[0]->ID) {
                $assign_done[$driver][] = $meta['reference_number'][0];
                $data = array(
                    'ID' =>  $post[0]->ID,
                    'post_title' => $meta['reference_number'][0],
                );

                $history = (unserialize($meta['wpcargo_shipments_update_demo'][0]));

                if (!is_array($history)) {
                    $history  = (unserialize($history));
                }

                if (!$history) {
                    $history = array();
                }

                $history[] = array(
                    'date' => date('Y-m-d'),
                    'time' => date('H:i'),
                    'location' => '',
                    'status' => $status,
                    'remarks' => '',
                    'updated-name' => 'admin'
                );

                update_post_meta($post[0]->ID, 'wpcargo_shipments_update_demo', $history);

                $history = (unserialize($meta['wpcargo_shipments_update'][0]));
                if (!is_array($history)) {
                    $history  = (unserialize($history));
                }

                if (!$history) {
                    $history = array();
                }

                $history[] = array(
                    'date' => date('Y-m-d'),
                    'time' => date('H:i'),
                    'location' => '',
                    'status' => $status,
                    'remarks' => '',
                    'updated-name' => 'admin'
                );


                update_post_meta($post[0]->ID, 'wpcargo_shipments_update', $history);
                wp_update_post($data);
                update_post_meta($post[0]->ID, 'wpcargo_driver', $driver_id);
                update_post_meta($post[0]->ID, 'wpcargo_status', $status);
            }
        }
    }
}



?>

<form method="post" enctype="multipart/form-data">
    <h2> Assign Driver </h2>
    <label>Upload File </label><br> <br>
    <input type="file" name="csv_file_driver">
    <br> <br> <br>
    <input type="submit" class="btn btn-primary btn-sm m-0" value="Assign">
</form>