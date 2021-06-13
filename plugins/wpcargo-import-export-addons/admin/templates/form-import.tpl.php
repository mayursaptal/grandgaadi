<div id="wpc-ie-loader" ></div>
<div class="wpc-import-wrap">
    <form id="wpc-ie-form" enctype="multipart/form-data" method="POST" action="<?php echo admin_url(); ?>edit.php?post_type=wpcargo_shipment&page=wpcie-import" >
        <?php wp_nonce_field( 'wpc_import_ie_results_callback', 'wpc_ie_nonce' ); ?>
        <?php 
        $args = array(
            'post_type' => 'wpcargo_shipment',
        );
        $cpts = new WP_Query($args);
        if($cpts->have_posts()) : 
            while($cpts->have_posts() ) : $cpts->the_post();
                $meta_values[] = get_post_meta($post->ID);
            endwhile;
        endif;
        ?>
        <div style="clear:both;"></div>
        <table class="form-table">
            <tr>
                <th><?php esc_html_e('Choose a file from your computer:', 'wpc-import-export' ); ?></th>
                <td><input name="uploadedfile" type="file" /></td>
            </tr>
        </table>
        <input type="hidden" name="post_type" value="wpcargo_shipment" />
        <input type="hidden" name="page" value="<?php echo $page; ?>" />
        <p><input style="margin-top: 24px;" class="button button-primary button-large" type="submit" name="submit" value="<?php esc_html_e('Import CSV', 'wpc-import-export' ); ?>" /></p>
    </form>
    <h2 class="description"><?php esc_html_e( 'Instructions on how to Import Shipment ', 'wpc-import-export' ); ?></h2>
    <ol id="import-instruction">
        <li><a href="https://grandgaadi.in/wp-content/uploads/2021/05/shipment-export-1622050075-1.csv" id="download-csv-template-2" class="description"><?php esc_html_e('Download CSV template', 'wpc-import-export'); ?></a> <?php esc_html_e('as template for Importing data.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Delete Column(s) that are not needed.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Add Data to each Cell.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Import CSV template.', 'wpc-import-export' ); ?></li>
    </ol>
    <script>
        jQuery(document).ready(function($) {
            $('#download-csv-template').on('click', function(e){
                e.preventDefault();
                window.location='<?php echo $filename_unique; ?>';
            });
        });
    </script>
</div>