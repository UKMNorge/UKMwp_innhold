<?php
/* 
Plugin Name: UKM WP Innhold
Plugin URI: http://www.ukm-norge.no
Description: Håndterer visningen av WP-innlegg og posts som en del av admin
Author: UKM Norge / M Mandal 
Version: 1.0
Author URI: http://mariusmandal.no
*/

use UKMNorge\Wordpress\Modul;
use UKMNorge\Wordpress\Nyhet;

define('UKM_WP_INNHOLD_PATH', dirname(__FILE__) .'/');

require_once('UKM/Autoloader.php');

class UKMwp_innhold extends Modul
{
	public static $action = 'innhold';
	public static $path_plugin = null;

	public static function registerFunctions() {
		static::require('functions/getPage.function.php');
		static::require('functions/getCategory.function.php');
	}

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
			static::getPluginUrl()  . 'js/wp_innhold.js'
		);

		static::hookInnholdCss();
        
        wp_enqueue_style('WPgallery_css');
	}

	/**
	 * Pass på at vi laster inn css for innhold
	 * 
	 * returnerer navn på style, slik at du kan kjøre
	 * wp_enqueue_style(
	 *		UKMwp_innhold::hookInnholdCss()
	 *	);
	 *
	 * @return string wp_enqueue_style(hookname)
	 */
	public static function hookInnholdCss() {
		wp_register_style(
			'UKMwp_innhold_style',
			static::getPluginUrl() . 'UKMwp_innhold.css'
		);
		return 'UKMwp_innhold_style';
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