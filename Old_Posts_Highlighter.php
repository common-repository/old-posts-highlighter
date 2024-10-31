<?php
/*
	Plugin Name: Old Posts Highlighter
	Plugin URI: http://www.mkh.fr/old-posts-highlighter/	
	Description: This module will randomly choose in your Wordpress database an old post and reset his publication date. It will highlight older posts by moving them back to front page and in the RSS feed. <strong>This plugin shouldn't be used with permalink structures using dates</strong>. <a href="options-general.php?page=OPH_admin.php">Configuration.</a>
	Version: 1.0.3	
	Author: MKH	
	Author email: contact@mkh.fr	
	Author URI: http://www.mkh.fr/	
	Donate: //	
	License: GNU GPL	
	Domain Path: /languages/
	Text Domain: Old_Posts_Highlighter
*/
/*  
	Old Posts Highlighter by MKH : http://www.mkh.fr/old-posts-highlighter/
	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once('OPH_admin.php');
require_once('OPH_core.php');

function ap_action_init()  
{  
   load_plugin_textdomain('Old_Posts_Highlighter', false, dirname(plugin_basename( __FILE__ ) ) . '/languages/' ); 
}  

define ('OPH_1_MINUTE', 60); 
define ('OPH_15_MINUTES', 15*OPH_1_MINUTE); 
define ('OPH_30_MINUTES', 30*OPH_1_MINUTE); 
define ('OPH_1_HOUR', 60*OPH_1_MINUTE); 
define ('OPH_2_HOURS', 2*OPH_1_HOUR); 
define ('OPH_4_HOURS', 4*OPH_1_HOUR); 
define ('OPH_6_HOURS', 6*OPH_1_HOUR); 
define ('OPH_12_HOURS', 12*OPH_1_HOUR); 
define ('OPH_24_HOURS', 24*OPH_1_HOUR); 
define ('OPH_48_HOURS', 48*OPH_1_HOUR); 
define ('OPH_72_HOURS', 72*OPH_1_HOUR); 
define ('OPH_168_HOURS', 168*OPH_1_HOUR); 
define ('OPH_INTERVAL', OPH_12_HOURS); 
define ('OPH_INTERVAL_aleatoire', OPH_4_HOURS); 
define ('OPH_AGE_LIMIT', 120);
define ('OPH_OMIT_CATS', ""); 
define ('OPH_FORCED_POSTS', "");

register_activation_hook(__FILE__, 'OPH_activate');
register_deactivation_hook(__FILE__, 'OPH_deactivate');
add_action('init', 'OPH_Old_Posts_Highlighter'); 
add_action('init', 'ap_action_init');  
add_action('admin_menu', 'OPH_options_setup');
add_action('admin_head', 'OPH_head_admin');
add_filter('the_content', 'OPH_the_content'); 
add_filter('plugin_action_links', 'OPH_plugin_action_links', 10, 2);

function OPH_plugin_action_links($links, $file) 
{
	$plugin_file = basename(__FILE__);
	if (basename($file) == $plugin_file) 
	{
		$settings_link = '<a href="options-general.php?page=OPH_admin.php">'.__('Options', 'OPH Admin').'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

function OPH_deactivate() 
{
	delete_option('OPH_give_credit');
}

function OPH_activate() 
{
	add_option('OPH_interval',OPH_INTERVAL);
	add_option('OPH_interval_aleatoire',OPH_INTERVAL_aleatoire);
	add_option('OPH_age_limit',OPH_AGE_LIMIT);
	add_option('OPH_omit_cats',OPH_OMIT_CATS);
	add_option('OPH_FORCED_POSTS',OPH_FORCED_POSTS);
	add_option('OPH_show_original_pubdate',1);	
	add_option('OPH_pos',0);	
	add_option('OPH_give_credit',0);	
	add_option('OPH_at_top',0);	
}
?>