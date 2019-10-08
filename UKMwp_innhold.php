<?php
/* 
Plugin Name: UKM WP Innhold
Plugin URI: http://www.ukm-norge.no
Description: Håndterer visningen av WP-innlegg og posts som en del av admin
Author: UKM Norge / M Mandal 
Version: 1.0
Author URI: http://mariusmandal.no
*/

use UKMNorge\Wordpress\Nyhet;

define('UKM_WP_INNHOLD_PATH', dirname(__FILE__) .'/');

require_once('UKM/Autoloader.php');
require_once('UKM/wp_modul.class.php');

class UKMwp_innhold extends UKMWPmodul
{
	public static $action = 'innhold';
	public static $path_plugin = null;

	/**
	 * Registrer alle hooks
	 *
	 * @return void
	 */
	public static function hook()
	{
		add_action(
			'wp_ajax_UKMwp_innhold_ajax', 
			['UKMwp_innhold', 'ajax']
		);

		wp_enqueue_script(
			'UKMwp_innhold_script',
			plugin_dir_url(__FILE__)  . 'js/wp_innhold.js'
		);

		wp_register_style(
			'UKMwp_innhold_style',
			plugin_dir_url(__FILE__) . 'UKMwp_innhold.css'
		);
	}

	/**
	 * Registrer menyelementer
	 * (som denne modulen ikke har)
	 *
	 * @return void
	 */
	public static function meny() {
		// VOID
	}

	/**
	 * Håndter alle ajax-forespørsler
	 *
	 * @return void
	 */
	public static function ajax()
	{
		$news = new Nyhet($_POST['blog_id'], $_POST['post_id']);

		global $current_user;

		if ($_POST['newsAction'] == 'like') {
			$res = $news->doLike($current_user->ID);
		} else {
			$res = $news->doDislike($current_user->ID);
		}

		$data = [
			'success' => $res,
			'newCount' => $news->getLikeCount(),
		];

		header('Content-Type: application/json');
		echo json_encode($data);
		die();
	}
}

UKMwp_innhold::init( __DIR__ );
UKMwp_innhold::hook();