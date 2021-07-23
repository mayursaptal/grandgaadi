<?php


$args = array(
    'role'    => 'wpcargo_client',

);
$users = get_users($args);



?>
<form method="get" enctype="multipart/form-data">
    <h2> Report </h2>

    <!-- <input required type="text" name="client" placeholder="Client"> -->

    <label>Client </label><br>
    <select style="display: block !important;" class="form-control " name="client" required placeholder="Client">
        <?php
        foreach ($users as $user) {
        ?>
            <option value="<?php echo  $user->ID ?>"><?php echo  $user->display_name ?></option>
        <?php } ?>
    </select>
    <br>

    <label>From </label><br>
    <input class="form-control wpccf-datepicker picker__input " placeholder="From" type="date" name="from">
    <br>
    <label>Top </label><br>
    <input class="form-control wpccf-datepicker picker__input " placeholder="To" type="date" name="to">
    <br>
    <input type="hidden" name="ssi_download" value="true">
    <br>
    <input type="submit" class="btn btn-primary btn-sm m-0" value="Download">
</form>