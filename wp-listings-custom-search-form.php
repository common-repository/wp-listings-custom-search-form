<?php
/*
	Plugin Name: IMPress Listings Custom Search Widget
	Description: It is an add-on of IMPress Listings plugin which allow to create custom search widget for real estate listing management system. Designed to work with any theme using built-in templates. There is also a IMPress default search form shortcode with this plugin.
	Author: Soham Web Solution
	Author URI: https://sohamsolution.com/
	Version: 1.5.2
	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
	Text Domain: wp-listings-custom-search-form
*/

include_once 'wp-listings-search-form-shortcode.php';

class WP_Listings_Custom_Search_Form_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'listings-search wp-listings-custom-search', 'description' => __( 'Display listings search dropdown', 'wp-listings-custom-search-form' ) );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'listings-custom-search-form' );
		parent::__construct( 'listings-custom-search-form', __( 'IMPress Listings - Custom Search Form', 'wp-listings-custom-search-form' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		$instance = wp_parse_args( (array) $instance, array(
			'title'			=> '',
			'button_text'	=> __( 'Search', 'wp-listings-custom-search-form' )
		) );

		global $_wp_listings_taxonomies;

		$listings_taxonomies = $_wp_listings_taxonomies->get_taxonomies();

		extract( $args );

		printf(__('%1$s', 'wp-listings-custom-search-form'), $before_widget);

		if ( $instance['title'] ) echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

		$inlineWrapper = ( isset( $instance['inline_widget']) && $instance['inline_widget'] == 1 ? 'wlcsw_inline_wrapper' : '' );
		$inlineProperty = ( isset( $instance['inline_widget']) && $instance['inline_widget'] == 1 ? 'wlcsw_inline_property' : '' );
		$inlineOptions = ( isset( $instance['inline_widget']) && $instance['inline_widget'] == 1 ? 'wlcsw_inline_options' : '' );
		$inlineSubmit = ( isset( $instance['inline_widget']) && $instance['inline_widget'] == 1 ? 'wlcsw_inline_btn' : '' );

		printf(__('<div class="inner-80 %s">', 'wp-listings-custom-search-form'), $inlineWrapper);

		printf(__('<form role="search" method="get" id="customsearchform" action="%1$s" ><input type="hidden" value="" name="s" /><input type="hidden" value="listing" name="post_type" />', 'wp-listings-custom-search-form'), home_url( '/' ));

		foreach ( $listings_taxonomies as $tax => $data ) {
			if ( ! isset( $instance[$tax] ) || ! $instance[$tax] )
				continue;

			$terms = get_terms( $tax, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 100, 'hierarchical' => false ) );
			if ( empty( $terms ) )
				continue;

			$current = ! empty( $wp_query->query_vars[$tax] ) ? $wp_query->query_vars[$tax] : '';

			/* CUSTOM */

			/*echo "<select name='$tax' id='$tax' class='wp-listings-taxonomy'>\n\t";
			echo '<option value="" ' . selected( $current == '', true, false ) . ">{$data['labels']['name']}</option>\n";
			foreach ( (array) $terms as $term )
				echo "\t<option value='{$term->slug}' " . selected( $current, $term->slug, false ) . ">{$term->name}</option>\n";

			echo '</select>';*/

			printf( __( '<div class="Property-Types %2$s"><span class="Heading"> %1$s </span></div>', 'wp-listings-custom-search-form' ), $data['labels']['name'], $inlineProperty );

			printf( __( '<div class="general-link %1$s">', 'wp-listings-custom-search-form' ), $inlineOptions );

			foreach ( (array) $terms as $term ){
				printf( __( '<label><input name="%1$s" id="%1$s" type="radio" value="%2$s" %3$s>%2$s</label>', 'wp-listings-custom-search-form' ), $tax, $term->name, selected( $current, $term->slug, false ));
			}

			_e('</div>', 'wp-listings-custom-search-form');

			/* CUSTOM */

		}

		printf(__('<div class="Counties %2$s"><button type="submit" class="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i><span class="button-text">%1$s</span></button></div>', 'wp-listings-custom-search-form'), esc_attr( $instance['button_text'] ), $inlineSubmit);
		_e('<div class="clear"></div></form></div>', 'wp-listings-custom-search-form');

		printf(__('%1$s', 'wp-listings-custom-search-form'), $after_widget);

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array(
			'title'			=> '',
			'button_text'	=> __( 'Search Listings', 'wp-listings-custom-search-form' )
		) );

		global $_wp_listings_taxonomies;

		$listings_taxonomies = $_wp_listings_taxonomies->get_taxonomies();
		$new_widget = empty( $instance );

		printf( __('<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', 'wp-listings-custom-search-form'), $this->get_field_id( 'title' ), __( 'Title:', 'wp-listings-custom-search-form' ), $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $instance['title'] ), 'width: 95%;' );
		?>
		<h5><?php _e( 'Include these taxonomies in the search widget', 'wp-listings-custom-search-form' ); ?></h5>
		<?php
		foreach ( (array) $listings_taxonomies as $tax => $data ) {
			$terms = get_terms( $tax );
			if ( empty( $terms ) )
				continue;
			
			$checked = isset( $instance[ $tax ] ) && $instance[ $tax ];

			printf( __('<p><label><input id="%s" type="checkbox" name="%s" value="1" %s />%s</label></p>', 'wp-listings-custom-search-form'), $this->get_field_id( 'tax' ), $this->get_field_name( $tax ), checked( 1, $checked, 0 ), esc_html( $data['labels']['name'] ) );

		}

		printf( __('<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', 'wp-listings-custom-search-form'), $this->get_field_id( 'button_text' ), __( 'Button Text:', 'wp-listings-custom-search-form' ), $this->get_field_id( 'button_text' ), $this->get_field_name( 'button_text' ), esc_attr( $instance['button_text'] ), 'width: 95%;' );
	}
}

add_action( 'widgets_init', 'wp_listings_custom_search_register_widgets' );
add_action( 'wp_enqueue_scripts', 'wp_listings_custom_search_scripts', 10 );

/**
 * Register Widgets that will be used in the IMPress Listings plugin
 *
 * @since 0.1.0
 */
function wp_listings_custom_search_register_widgets() {

	if ( class_exists( 'WP_Listings' ) ) {

		$widgets = array( 'WP_Listings_Featured_Listings_Widget', 'WP_Listings_Custom_Search_Form_Widget' );

		foreach ( (array) $widgets as $widget ) {
			register_widget( $widget );
		}

	}

}

function wp_listings_custom_search_scripts() {
	wp_register_style( 'wlcsf-main-style', plugins_url( '/css/wlcsf-style.css' , __FILE__ ), array(), '', 'all' );
	wp_enqueue_style('wlcsf-main-style');
}

register_activation_hook( __FILE__, 'wlcsw_plugin_activate' );
function wlcsw_plugin_activate(){

    // Require parent plugin
    if ( ! is_plugin_active( 'wp-listings/plugin.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        //wp_die('Sorry, but this plugin requires the <a href="//wordpress.org/plugins/wp-listings">IMPress Listings (IMPress Listings)</a> Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        add_action( 'admin_notices', 'wlcsw_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

add_action( 'admin_init', 'wlcsw_has_wp_listings_plugin' );
function wlcsw_has_wp_listings_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !class_exists( 'WP_Listings' ) ) {
        add_action( 'admin_notices', 'wlcsw_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function wlcsw_plugin_notice(){
    _e('<div class="error"><p>Sorry, but IMPress Listings Custom Search Widget Plugin requires the IMPress Listings (WP Listings) to be installed and active.</p></div>', 'wp-listings-custom-search-form');
}

add_action('plugins_loaded', 'wlcsw_load_textdomain');
function wlcsw_load_textdomain() {
    load_plugin_textdomain( 'wp-listings-custom-search-form', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/* Displaying a Custom Widget Option */
function wlcsw_add_inline_widget_option( $widget, $return, $instance ) {
 
    // Are we dealing with a IMPress Listings custom search form widget?
    if ( 'listings-custom-search-form' == $widget->id_base ) {
         // Display the inline widget option.
        $inline_widget = isset( $instance['inline_widget'] ) ? $instance['inline_widget'] : '';
        printf(__('<p><input class="checkbox" type="checkbox" id="%1$s" name="%2$s" %3$s /><label for="%2$s">%4$s</label></p>', 'wp-listings-custom-search-form'), $widget->get_field_id('inline_widget'), $widget->get_field_name('inline_widget'), checked( 1 , $inline_widget, 0 ), 'Display widget inline.');
    }
}
add_filter('in_widget_form', 'wlcsw_add_inline_widget_option', 10, 3 );

/* Saving a Custom Widget Option */
function wlcsw_save_inline_widget_option( $instance, $new_instance ) {
 
    // Is the instance a nav menu and are descriptions enabled?
    if ( isset( $new_instance['inline_widget'] ) && !empty( $new_instance['inline_widget'] ) ) {
        $new_instance['inline_widget'] = 1;
    }
 
    return $new_instance;
}
add_filter( 'widget_update_callback', 'wlcsw_save_inline_widget_option', 10, 2 );

?>
