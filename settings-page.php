<?php
// create custom plugin settings menu
add_action('admin_menu', 'ddb_plugin_create_menu');

function ddb_plugin_create_menu() {

	//create new top-level menu
	add_menu_page('Delivery Date Settings', 'Delivery Box', 'administrator', __FILE__, 'ddb_plugin_settings_page' , plugins_url('/images/delivery-icon.svg', __FILE__) );

	//call register settings function
	add_action( 'admin_init', 'register_ddb_plugin_settings' );
}


function register_ddb_plugin_settings() {
	//register our settings
	register_setting( 'ddb-settings-group', 'data_spedizione' );
	register_setting( 'ddb-settings-group', 'data_consegna' );
}

function ddb_plugin_settings_page() {
?>
<div class="wrap">
<h1>Impostazioni plugin Delivery Date Box</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'ddb-settings-group' ); ?>
    <?php do_settings_sections( 'ddb-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Dopo quanti giorni spedisci l'articolo ?</th>
        <td><input type="number" name="data_spedizione" value="<?php echo esc_attr( get_option('data_spedizione') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Dopo quanti giorni è prevista la consegna ?</th>
        <td><input type="number" name="data_consegna" value="<?php echo esc_attr( get_option('data_consegna') ); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
<p>Inserisci il seguente shortcode: <code>[delivery]</code> per visualizzare il box con le date di consegna. </p>
</div>
<?php } ?>