<div class="postbox">
    <div id="wpcbranch-restriction" class="inside">
        <h3 class="hndle"><?php esc_html_e('Branch Restriction Settings', 'wpcargo-branches' ); ?></h3>
        <hr/>
        <p><input id="wpcbranch_restrict_all_employees" type="checkbox" class="wpcbranch_access" name="wpcbranch_restrict_all_employees" value="1" <?php checked( get_option('wpcbranch_restrict_all_employees'), 1 ); ?> > <label for="wpcbranch_restrict_all_employees"><?php esc_html_e('Restrict Branch Manager to access all Employees?', 'wpcargo-branches' ); ?></label></p>
        <p><input id="wpcbranch_restrict_all_agents" type="checkbox" class="wpcbranch_access" name="wpcbranch_restrict_all_agents" value="1" <?php checked( get_option('wpcbranch_restrict_all_agents'), 1 ); ?>> <label for="wpcbranch_restrict_all_agents"><?php esc_html_e('Restrict Branch Manager to access all Agents?', 'wpcargo-branches' ); ?></label></p>
        <p><input id="wpcbranch_restrict_all_clients" type="checkbox" class="wpcbranch_access" name="wpcbranch_restrict_all_clients" value="1" <?php checked( get_option('wpcbranch_restrict_all_clients'), 1 ); ?>> <label for="wpcbranch_restrict_all_clients"><?php esc_html_e('Restrict Branch Manager to access all Clients?', 'wpcargo-branches' ); ?></label></p>
        <p><input id="wpcbranch_restrict_all_drivers" type="checkbox" class="wpcbranch_access" name="wpcbranch_restrict_all_drivers" value="1" <?php checked( get_option('wpcbranch_restrict_all_drivers'), 1 ); ?>> <label for="wpcbranch_restrict_all_drivers"><?php esc_html_e('Restrict Branch Manager to access all Drivers?', 'wpcargo-branches' ); ?></label></p>
        <p class="description"><i><?php esc_html_e('Note: Restriction is enabled the Branch Manager can only have assignement options based on the Assigned Users on the table list', 'wpcargo-branches' ); ?></i></p>
    </div>
</div>