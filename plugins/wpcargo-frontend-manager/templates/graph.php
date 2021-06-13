<?php
$color_pallete  = wpcfe_report_color_pallete();
$status_report  = wpcfe_report_status();
$records        = array();
$sel_month      = isset( $_GET['_month'] ) && (int)$_GET['_month'] ? (int)$_GET['_month'] : date("m") ;
$sel_month      = (int)$sel_month <= 9 ? '0'.(int)$sel_month : (int)$sel_month ; 
$sel_year       = isset( $_GET['_year'] ) && (int)$_GET['_year'] ? (int)$_GET['_year'] : date("Y") ;

$sel_date       = $sel_year.'-'.$sel_month.'-01';
$date_start     = wpcfe_date_first_day( $sel_date );
$date_end       = wpcfe_date_last_day( $sel_date );
$dates          = wpcef_get_dates( $date_start, $date_end );
$total_shipments = wpcfe_get_all_shipment_count( $date_start, $date_end );
// Create status variables
if( !empty( $status_report  ) ){
    foreach ($status_report as $s_variable) {
        ${wpcfe_to_slug($s_variable)} = array();
    }
}
foreach ( $dates as $date ) {    
    if( !empty( $status_report  ) ){
        foreach ($status_report as $s_variable) {
            ${wpcfe_to_slug($s_variable)}[] = wpcfe_get_report_count( $date, $s_variable );
        }
    }
}
// Create data set object
$dataset = array();
if( !empty( $status_report  ) ){
    $counter = 0;
    foreach ($status_report as $s_variable) {
        $dataset[] = array(
            'label' => $s_variable,
            'backgroundColor' => '#'.$color_pallete[$counter],
            'borderColor'   => '#'.$color_pallete[$counter],
            'borderWidth'   => 1,
            'data'          => ${wpcfe_to_slug($s_variable)}
        );
        $counter++;
    }
}
?>
<?php do_action( 'wpcfe_before_dashboard_status_report' ); ?>
<form id="dashboard-form-filter" action="<?php echo $page_url; ?>" class="row mb-4 border-bottom">
    <input type="hidden" name="wpcfe" value="dashboard">
    <div id="wpcfe-filter-fields" class="col-lg-12 form-inline">
        <div class="form-group _dashboard-filter-group">
            <label class="sr-only" for="_month"><?php esc_html_e('Month', 'wpcargo-frontend-manager' ); ?></label>
            <select name="_month" class="form-control md-form wpcfe-select _dashboard-filter" id="_month" >
                <option value=""><?php esc_html_e('-- Select Month --','wpcargo-frontend-manager'); ?></option>
                <?php foreach( wpcfe_month_list() as $month_key => $month_value ): ?>
                    <?php $month_key = ($month_key + 1) <= 9 ? '0'.($month_key + 1) : ($month_key + 1) ;  ?>
                    <option value="<?php echo $month_key; ?>" <?php selected( $sel_month, $month_key ); ?>><?php echo $month_value; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group _dashboard-filter-group">
            <label class="sr-only" for="_year"><?php esc_html_e('Year', 'wpcargo-frontend-manager' ); ?></label>
            <select name="_year" class="form-control md-form wpcfe-select _dashboard-filter" id="_year" >
                <option value=""><?php esc_html_e('-- Select Year --','wpcargo-frontend-manager'); ?></option>
                <?php for( $year = date("Y") - 12 ; $year <= date("Y") + 2; $year++ ): ?>
                    <option value="<?php echo $year; ?>" <?php selected( $sel_year, $year ); ?>><?php echo $year; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group submit-filter p-0 mx-1">
            <button id="wpcfe-submit-filter" type="submit" class="btn btn-primary btn-fill btn-sm"><?php esc_html_e('Filter', 'wpcargo-frontend-manager' ); ?></button>
        </div>
    </div>
</form>
<div id="wpcfe-status-report" class="row mb-4">
    <?php
    if( !empty( $status_report ) ){
        $counter = 0;
        foreach ( $status_report as $status ) {
            $shipment_count      = wpcfe_get_shipment_status_count( $status, $date_start, $date_end );
            $shipment_percentage = 0;
            if( $total_shipments || $shipment_count ){
                $shipment_percentage = ( $shipment_count / $total_shipments ) * 100;
            }
            ?>   
            <div class="col-sm-6 col-md-3 col-lg-3 mb-4">
                <div class="card classic-admin-card">
                    <div class="card-body">
                        <div class="pull-right">
                        <i class="fa fa-line-chart" style="color:<?php echo '#'.$color_pallete[$counter] ?> !important;"></i>
                        </div>
                        <h6><a style="color:<?php echo '#'.$color_pallete[$counter] ?> !important;" href="<?php echo get_permalink( wpcfe_admin_page() ); ?>?status=<?php echo urlencode($status); ?>"><?php echo $status; ?></a></h6>
                        <h4 class="text-dark h1"><?php echo $shipment_count; ?></h4>
                        <p class="text-dark">
                        <?php
                        printf( __("%s of 100%s"), number_format($shipment_percentage, 2, '.', ''),'%' );
                        ?>
                        </p>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg info darken-3" role="progressbar" style="width: <?php echo $shipment_percentage; ?>%; background-color: <?php echo '#'.$color_pallete[$counter] ?> !important;" aria-valuenow="<?php echo $shipment_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <?php
            $counter++;
        }
    }
    ?>
</div>
<?php do_action( 'wpcfe_after_dashboard_status_report' ); ?>
<div id="wpcfe-graph-report" class="row my-4 py-4 border-top bg-white">
    <div class="col-lg-12">
        <h2 class="h5 text-center"><?php printf( esc_html__('Report for the Month of %s', 'wpcargo-frontend-manager'), date("F",strtotime( $date_end )) ); ?></h2>
        <p class="h6 text-center text-muted"><?php _e('Total Shipments', 'wpcargo-frontend-manager'); ?>: <?php echo $total_shipments; ?></p>
    </div>   
    <div class="col-lg-12 d-block d-sm-none">
        <div class="list-group list-group-flush">
            <?php foreach ($dates as $m_key => $m_date ): ?>
                <p class="h5 py-2 border-bottom" data-toggle="collapse" href="#mdata<?php echo $m_key; ?>" role="button" aria-expanded="false" aria-controls="mdata<?php echo $m_key; ?>"><?php echo $m_date; ?> <i class="fa fa-th-list float-right text-info" aria-hidden="true"></i></p>
                <section id="mdata<?php echo $m_key; ?>" class="<?php echo $m_key != 0 ? 'collapse' : '' ; ?>">
                <?php 
                    $mcounter = 0;
                    foreach ($status_report as $s_variable) {
                        ?>
                        <a class="list-group-item list-group-item-action"><?php echo $s_variable; ?>
                            <span class="badge badge-pill pull-right" style="background-color:<?php echo '#'.$color_pallete[$mcounter] ?> !important; font-size: 1em;"><?php echo ${wpcfe_to_slug($s_variable)}[$m_key]; ?>
                            </span>
                        </a>
                        <?php
                        $mcounter++;
                    }
                ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-12 d-none d-sm-block">
        <?php 
        $template = wpcfe_include_template( 'chart' );
        require_once( $template );
        ?>
    </div>
</div>
<?php do_action( 'wpcfe_after_dashboard_graph_report' ); ?>