<div class="table-top mb-4">
    <form id="shipment-sort" class="float-right mr-4" action="<?php echo $page_url; ?>" method="get">
        <select name="wpcsc_page" class="mdb-select" style="width: 38px; display: inline-block;">
            <option ><?php echo __('Show entries', 'wpcargo-shipment-container' ); ?></option>
            <?php foreach( $wpcsc_list as $list ): ?>
            <option value="<?php echo $list ?>" <?php echo $list == $wpcsc_page ? 'selected' : '' ;?>><?php echo $list ?> <?php echo __('entries', 'wpcargo-shipment-container' ); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <a href="<?php echo $page_url; ?>?wpcsc=add" class="addShipmentContainer btn btn-primary btn-sm"><i class="fa fa-file-pdf text-white"></i> <?php echo wpc_scpt_add_new_item_label(); ?></a>
</div>
<div class="shipment-container-wrapper table-responsive">
    <form id="wpcfe-search" class="float-md-none float-lg-right" action="<?php echo $page_url; ?>" method="get">
        <input type="hidden" name="wpcsc" value="s">
        <div class="form-sm">
            <label for="search-shipment" class="sr-only"><?php esc_html_e('Shipment Number', 'wpcargo-shipment-container' ); ?></label>
            <input type="text" class="form-control form-control-sm" name="num" id="search-shipment" placeholder="<?php esc_html_e('Container Number', 'wpcargo-shipment-container' ); ?>" value="<?php echo $searched; ?>">
            <button type="submit" class="btn btn-primary btn-sm mx-md-0 ml-2"><?php esc_html_e('Search', 'wpcargo-shipment-container' ); ?></button>
        </div>
    </form>
    <table id="container-list" class="table table-hover table-sm">
        <thead>
            <tr>
                <th><?php echo apply_filters( 'wpcsc_container_number_label', __( 'Container Number', 'wpcargo-shipment-container' ) ); ?></th>
                <?php do_action( 'wpcsc_table_header_value' ); ?>	
                <th><?php echo apply_filters( 'wpcsc_container_action_label', __('Action', 'wpcargo-shipment-container' ) ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php					
            while ( $wpc_container->have_posts() ) {
                $wpc_container->the_post();
                ?>
                <tr id="container-<?php echo get_the_ID(); ?>">
                    <td> <a href="<?php echo $page_url.'?wpcsc=track&num='.get_the_title(); ?>" class="text-primary font-weight-bold" title="<?php esc_html_e('Track', 'wpcargo-shipment-container'); ?>"><?php echo get_the_title(); ?></a></td>	
                    <?php do_action( 'wpcsc_table_data_value', get_the_ID() ); ?>	
                    <td>
                        <a href="<?php echo $page_url.'?wpcsc=edit&id='.get_the_ID(); ?>" title="<?php esc_html_e('Update', 'wpcargo-shipment-container'); ?>"><i class="fa fa-edit text-info"></i></a>
                        <a href="#" class="wpcsc_container-delete" data-id="<?php echo get_the_ID(); ?>" title="<?php esc_html_e('Delete', 'wpcargo-shipment-container'); ?>"><i class="fa fa-trash text-danger"></i></a>
                    </td>			
                </tr>
                <?php
            } // end while
            ?>
        </tbody>
    </table>
</div>
<div class="row">
    <section class="col-md-5">
        <?php
            printf(
                '<p class="note note-primary">%s %s %s %s %s %s %s.</p>',
                __('Showing', 'wpcargo-shipment-container'),
                $record_start,
                __('to', 'wpcargo-shipment-container'),
                $record_end,
                __('of', 'wpcargo-shipment-container'),
                number_format($number_records),
                __('entries', 'wpcargo-shipment-container')
            );
        ?>
    </section>
    <?php if( function_exists( 'wpcfe_bootstrap_pagination' ) ): ?>
    <section class="col-md-7"><?php wpcfe_bootstrap_pagination( array( 'custom_query' => $wpc_container ) ); ?></section>
    <?php endif; ?>
</div>