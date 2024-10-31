<?php
/*
Plugin Name: Seraphinite Downloads Statistics (Base)
Plugin URI: http://wordpress.org/plugins/seraphinite-downloads-stats
Description: Measure direct downloads from your site.
Text Domain: seraphinite-downloads-stats
Domain Path: /languages
Version: 1.3.1
Author: Seraphinite Solutions
Author URI: https://www.s-sols.com
License: GPLv2 or later (if another license is not provided)
Requires PHP: 5.4
Requires at least: 4.5





 */




























if( defined( 'SERAPH_DLSTAT_VER' ) )
	return;

define( 'SERAPH_DLSTAT_VER', '1.3.1' );

include( __DIR__ . '/main.php' );

// #######################################################################

register_activation_hook( __FILE__, 'seraph_dlstat\\Plugin::OnActivate' );
register_deactivation_hook( __FILE__, 'seraph_dlstat\\Plugin::OnDeactivate' );
//register_uninstall_hook( __FILE__, 'seraph_dlstat\\Plugin::OnUninstall' );

// #######################################################################
// #######################################################################
