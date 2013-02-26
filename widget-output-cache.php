<?php
/*
	Plugin Name: Widget Output Cache
	Description: Caches widget output in WordPress object cache
	Version: 0.2
	Author: Kaspars Dambis
	Author URI: http://konstruktors.com
*/

add_filter( 'widget_display_callback', 'maybe_cache_widget_output', 10, 3 );

function maybe_cache_widget_output( $instance, $widget_object, $args ) {
	$timer_start = microtime(true);
	$cache_key = 'widget-' . md5( serialize( array( $instance, $args ) ) );

	$cached_widget = get_transient( $cache_key );

	if ( $cached_widget == false ) {
		ob_start();
			$widget_object->widget( $args, $instance );
			$cached_widget = ob_get_contents();
		ob_end_clean();

		set_transient( $cache_key, $cached_widget, 60 * 5 ); // cache it for 5 minutes
	}

	echo $cached_widget;
	printf( '<!-- from widget cache in %s seconds. -->', number_format( microtime(true) - $timer_start, 5 ) );

	// We already echoed the widget here, so return false
	return false;
}