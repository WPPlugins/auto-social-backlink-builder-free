<?php

/**
 * Plugin Name: Auto Social Backlink Builder Free
 * Plugin URI:  http://wpextends.sinaapp.com/plugins/auto-social-backlink-builder.html
 * Description: Auto Social Backlink Builder is a easy-to-use, powerful plugin that can help you publish your blog posts with backlink to all your configured social networks automatically. It's can save you a tremendous amount of time. Also, increasing your traffic, leads and sales. 
 * Author:      WPExtends Team
 * Version:     1.2.8
 * Author URI:  http://wpextends.sinaapp.com
 */

if (!defined('WP_AUTO_SOCIAL_BACKLINK_BUILDER_FREE')) {

	require_once 'auto-social-backlink-config-free.php';
	require_once 'auto-social-backlink-admin-free.php';
	include_once 'http-client-free.php';
		
	function auto_social_backlink_free_publish($post_id) {	
		$is_revision = wp_is_post_revision($post_id);
		if ($is_revision) {
			return;
		}
		
		$already_posted = get_post_meta($post_id, 'wpsb_already_posted', true);
		if ($already_posted) {
			return;
		}
		
		add_post_meta($post_id, 'wpsb_already_posted', 'inprocess');	
		build_delicious_backlink_free($post_id);
		update_post_meta($post_id, 'wpsb_already_posted', 'complated');
	}
	
	function build_delicious_backlink_free($post_id) {
		@set_time_limit(0);
		@ignore_user_abort(true);
		
		$post = get_post($post_id);	
		if (!$post) {
			return false;
		}
				
		$post_notes = strip_tags($post->post_excerpt);
		if (! $post_notes) {
			$post_notes = strip_tags($post->post_title);
		}
		if (strlen($post_notes) > 252) {
			$post_notes = substr($post_notes, 0, 252) . '... ';
		}
			
		$post_url = get_permalink($post_id);
		if (! $post_url) {
			$post_url = get_option('siteurl') . '/?p=' . $post_id;
		}	
		
		$post_tags = array();
		foreach(wp_get_post_tags($post_id) as $tag) {
			$post_tags[] = trim($tag->name);
		}
			
		global $wpsb_options;
		if (! empty($wpsb_options)) {
			$delicious_username = $wpsb_options['delicious_username']; 
			$delicious_password = $wpsb_options['delicious_password'];
			if ($delicious_username && $delicious_password) {
				$url = 'https://api.del.icio.us/v1/posts/add';
				$auth = $delicious_username . ':' . $delicious_password;
				
				$data['url'] = $post_url;		
				$data['description'] = $post_notes; 
				$data['extended'] = $post_notes;
				$data['tags'] = implode(',', $post_tags);
				$data['shared'] = true;
				
				$request = new HttpClientFree();
				$request->get($url, $data, $auth);
				$request->close();			
			}	
		}
		return TRUE;
	}
	
	function auto_social_backlink_free_activate() {}
	
	function auto_social_backlink_free_deactivate() {}
	
	add_action('publish_post', 'auto_social_backlink_free_publish');
	add_action('publish_page', 'auto_social_backlink_free_publish');
	register_activation_hook(__FILE__, 'auto_social_backlink_free_activate');
	register_deactivation_hook(__FILE__, 'auto_social_backlink_free_deactivate');

}

?>