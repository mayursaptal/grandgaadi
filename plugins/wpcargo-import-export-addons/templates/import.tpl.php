<form id="wpcie-import-form" method="POST" action="<?php echo get_the_permalink().'?type=import'; ?>" enctype="multipart/form-data" class="container-fluid" >
    <?php wp_nonce_field( 'wpc_import_ie_results_callback', 'wpc_ie_nonce' ); ?>
    <section class="row">
        <div class="col-md-6">
            <div class="input-group mb-3">
                <div class="form-group">
                    <label for="uploadedfile"><?php esc_html_e( 'Import CSV File', 'wpc-import-export' ); ?></label>
                    <input type="file" class="form-control-file" id="uploadedfile" name="uploadedfile" 
                    accept="
                    application/octet-stream,
                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, 
                    application/vnd.ms-excel,
                    text/comma-separated-values,
                    text/x-comma-separated-values,
                    text/tab-separated-values,
                    text/csv,
                    application/csv,
                    application/x-csv,
                    .csv
                    ">
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" name="post_type" value="wpcargo_shipment" />
    <input type="submit" class="btn btn-primary btn-sm" name="import_shipment" value="<?php esc_html_e( 'Import Shipment', 'wpc-import-export' ); ?>" />
    <div class="row mt-4">
        <div class="col-sm-12">
            <h4 class="description"><?php esc_html_e( 'Instructions on how to Import Shipment ', 'wpc-import-export' ); ?></h4>
            <ol id="import-instruction">
                <li><a href="#" id="download-csv-template" class="description"><?php esc_html_e('Download CSV template', 'wpc-import-export'); ?></a> <?php esc_html_e('as template for Importing data.', 'wpc-import-export' ); ?></li>
                <li><?php esc_html_e( 'Delete Column(s) that are not needed, make sure no empty header column this cause data mapping error.', 'wpc-import-export' ); ?></li>
                <li><?php esc_html_e( 'Add Data to each Cell.', 'wpc-import-export' ); ?></li>
                <li><?php esc_html_e( 'Import CSV template.', 'wpc-import-export' ); ?></li>
            </ol>
        </div>
    </div>
</form>
<script>
    jQuery(document).ready(function($) {
        $('#download-csv-template').on('click', function(e){
            e.preventDefault();
            window.location.href='<?php echo $file_url.$filename_unique; ?>';
        });
    });
</script>