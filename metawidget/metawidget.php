<?php
/**
 * @package MetaWidget
 */
/*
Plugin Name: Meta Widget
Plugin URI: https://vk.com/jqueryjs
Description: --
Version: 0.1
Author: jQueryJS
Author URI: https://vk.com/jqueryjs
License: GPLv2 or later
Text Domain: MetaWidget
*/

// Регистрация виджета консоли
add_action('wp_dashboard_setup', 'add_dashboard_widgets' );

// Выводит контент
function dashboard_widget_function( $post, $callback_args ) {
	global $wpdb;
	
	$wpdb->show_errors();
	
	$countProductQuery = "SELECT COUNT(*) FROM ".$wpdb->posts." WHERE `post_type` = 'product'";
	$countProduct = $wpdb->get_var($countProductQuery);
	
	$countProductOutStockQuery = "SELECT COUNT(*) FROM ".$wpdb->postmeta." WHERE `meta_value` = 'outofstock'";
	$countProductOutStock = $wpdb->get_var($countProductOutStockQuery);
	
	$countProductInStockQuery = "SELECT COUNT(*) FROM ".$wpdb->postmeta." WHERE `meta_value` = 'instock'";
	$countProductInStock = $wpdb->get_var($countProductInStockQuery);
	
	$countShopOrderQuery = "SELECT COUNT(*) FROM ".$wpdb->posts." WHERE `post_type` = 'shop_order'";
	$countShopOrder = $wpdb->get_var($countShopOrderQuery);
	
	echo '<div id="metaWC">';
	
	echo '</div>';
}

// Используется в хуке
function add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', 'WooCommerce информация', 'dashboard_widget_function');
}

add_action('admin_print_footer_scripts', 'get_info_product_javascript', 99);
function get_info_product_javascript() {
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var data = {
			action : 'get_info'
		};
		
		jQuery.post( ajaxurl, data,function(response) {
			jQuery("#metaWC").append(response);
		});
		
		setInterval(function(){
			jQuery("#metaWC").empty();
			jQuery.post( ajaxurl, data,function(response) {
			jQuery("#metaWC").append(response);
			});
		}, 5000);
	});
	</script>
	<?php
}

add_action('wp_ajax_get_info', 'get_info_callback');
function get_info_callback() {
	global $wpdb;
	
	$countProductQuery = "SELECT COUNT(*) FROM ".$wpdb->posts." WHERE `post_type` = 'product'";
	$countProduct = $wpdb->get_var($countProductQuery);
	
	$countProductOutStockQuery = "SELECT COUNT(*) FROM ".$wpdb->postmeta." WHERE `meta_value` = 'outofstock'";
	$countProductOutStock = $wpdb->get_var($countProductOutStockQuery);
	
	$countProductInStockQuery = "SELECT COUNT(*) FROM ".$wpdb->postmeta." WHERE `meta_value` = 'instock'";
	$countProductInStock = $wpdb->get_var($countProductInStockQuery);
	
	$countShopOrderQuery = "SELECT COUNT(*) FROM ".$wpdb->posts." WHERE `post_type` = 'shop_order'";
	$countShopOrder = $wpdb->get_var($countShopOrderQuery);
	
	echo 'Колличество товара(всего) - '.$countProduct.'<br>';
	echo 'Колличество товара в наличии - '.$countProductInStock.'<br>';
	echo 'Колличество товара не в наличии - '.$countProductOutStock.'<br>';
	echo 'Колличество заказов - '.$countShopOrder.'<br>';
	
	$wpdb->flush();
	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}