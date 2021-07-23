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
        <li><a href="#" id="download-csv-template" class="description"><?php esc_html_e('Download CSV template', 'wpc-import-export'); ?></a> <?php esc_html_e('as template for Importing data.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Delete Column(s) that are not needed.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Add Data to each Cell.', 'wpc-import-export' ); ?></li>
        <li><?php esc_html_e( 'Import CSV template.', 'wpc-import-export' ); ?></li>
    </ol>
    <script>
        jQuery(document).ready(function($) {
            function download_file(fileURL, fileName) {
                // for non-IE
                if (!window.ActiveXObject) {
                    var save = document.createElement('a');
                    save.href = fileURL;
                    save.target = '_blank';
                    var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
                    save.download = fileName || filename;
                    if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                            document.location = save.href; 
                        // window event not working here
                        }else{
                            var evt = new MouseEvent('click', {
                                'view': window,
                                'bubbles': true,
                                'cancelable': false
                            });
                            save.dispatchEvent(evt);
                            (window.URL || window.webkitURL).revokeObjectURL(save.href);
                        }   
                }
                // for IE < 11
                else if ( !! window.ActiveXObject && document.execCommand) {
                    var _window = window.open(fileURL, '_blank');
                    _window.document.close();
                    _window.document.execCommand('SaveAs', true, fileName || fileURL)
                    _window.close();
                }
            }
            $('#download-csv-template').on('click', function(e){
                e.preventDefault();
                var fileURL = "<?php echo $file_url.$filename_unique; ?>";
                var fileName = "<?php echo $filename_unique; ?>";
                download_file( fileURL, fileName );
            });
        });
    </script>
</div>