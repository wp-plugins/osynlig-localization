<?php
/*
Plugin Name: Osynlig Localization
Version: 1.0.0
Plugin URI: https://github.com/osynligab/osynlig-wordpress-localization
Description: Makes it possible to choose locale for the front-end.
Author: Osynlig AB
Author URI: https://osynlig.se/
*/

load_plugin_textdomain( 'localization', false, dirname( plugin_basename( __FILE__ ) ) ); 

/**
 * Change locale when on front-end.
 *
 * @since 1.0.0
 *
 * @return string Locale
 */
function lz_locale( $locale ) {
	if ( ! defined( 'WP_ADMIN' ) || WP_ADMIN !== true ) {
    $lz_locale = esc_attr( get_option( 'lz_locale' ) );

		if ( ! empty( $lz_locale ) ) {
			$locale = $lz_locale;
		}
	}
	
	return $locale;
}

add_filter( 'locale', 'lz_locale' );

/**
 * Add sub menu page to the Settings menu and register settings.
 *
 * @since 1.0.0
 *
 * @return nil
 */
function lz_admin_menu() {
	// Add sub menu page to the Settings menu.
	add_options_page( __( 'Localization', 'localization' ), __( 'Localization', 'localization' ), 8, 'localization', 'lz_options_page' );

	// Call register settings function
	add_action( 'admin_init', 'lz_register_settings' );
}

add_action( 'admin_menu', 'lz_admin_menu' );

/**
 * Register options page settings.
 *
 * @since 1.0.0
 *
 * @return nil
 */
function lz_register_settings() {
	register_setting( 'lz_settings', 'lz_locale' );
}

/**
 * Options page
 *
 * @since 1.0.0
 *
 * @return nil
 */
function lz_options_page() {
	$translations = lz_get_availiable_translations();
	$lz_locale = esc_attr( get_option( 'lz_locale' ) );
	?>
	<div class="wrap">
		<h2><?php _e( 'Localization', 'localization' ); ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'lz_settings' ); ?>
    	<?php do_settings_sections( 'lz_settings' ); ?>
			
			<?php if ( $translations === -1 ) : ?>
				<p><?php printf( __( "The languages directory doesn't seem to exist. Create it in your theme folder (%s) and add your translation files.", 'localization' ), get_template() ); ?>
			<?php elseif( empty( $translations ) ) : ?>
				<p><?php _e( "Couldn't find any translations. Check that you have put the translations in the languages directory.", 'localization' ); ?>
			<?php else : ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="lz-locale"><?php _e('Choose the locale you want on your website.', 'localization'); ?></label></th>
							<td valign="middle">
								<select name="lz_locale" id="lz-locale">
									<option value=""><?php _e( 'Default', 'localization' ); ?></option>
									<?php foreach( $translations as $locale ) : ?>
										<option value="<?php echo $locale; ?>"<?php if( $locale == $lz_locale ) : ?> selected="selected"<?php endif; ?>><?php echo $locale; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Get availiable translations (current theme).
 *
 * @since 1.0.0
 *
 * @return mixed Array with translations or integer if the languages directory doesn't exist.
 */
function lz_get_availiable_translations() {
	$translations = array();
	$language_dir = get_theme_root() .'/'. get_template() .'/languages';

	if( ! file_exists( $language_dir ) ) {
		return -1;
	}

	if ( $handle = opendir( $language_dir ) ) {
		$skip = array( '.', '..' );
	
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if( ! in_array( $file, $skip ) && substr( $file, -2, 2 ) == 'mo') {
				preg_match( '/[a-z]{2}_[A-Z]{2}/', $file, $matches );
				
				if( ! empty( $matches ) ) {
					$translations[] = $matches[0];
				}				
			}
		}
	}

	return $translations;
}