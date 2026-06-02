<?php
/**
 * Plugin Name: Elementor Mixed Masonry Gallery
 * Description: Custom Elementor widget for mixed image and video masonry gallery with responsive custom lightbox carousel.
 * Version: 1.0.5
 * Author: <a href="https://vivek.expert">Vivek Jayakrishnan</a>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EMG_Elementor_Mixed_Masonry_Gallery_V105' ) ) {

	final class EMG_Elementor_Mixed_Masonry_Gallery_V105 {

		const VERSION = '1.0.5';

		public function __construct() {
			add_action( 'plugins_loaded', [ $this, 'init' ] );
		}

		public function init() {
			if ( ! did_action( 'elementor/loaded' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
				return;
			}

			add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		}

		public function admin_notice_missing_elementor() {
			echo '<div class="notice notice-warning"><p><strong>Elementor Mixed Masonry Gallery</strong> requires Elementor to be installed and activated.</p></div>';
		}

		public function register_assets() {
			wp_register_style(
				'emg-masonry-gallery-v105',
				plugins_url( 'assets/css/emg-masonry-gallery.css', __FILE__ ),
				[],
				self::VERSION
			);

			wp_register_script(
				'emg-masonry-gallery-v105',
				plugins_url( 'assets/js/emg-masonry-gallery.js', __FILE__ ),
				[ 'jquery' ],
				self::VERSION,
				true
			);
		}

		public function register_widgets( $widgets_manager ) {
			require_once __DIR__ . '/widgets/class-emg-mixed-masonry-gallery.php';

			if ( class_exists( 'EMG_Mixed_Masonry_Gallery_Widget_V105' ) ) {
				$widgets_manager->register( new \EMG_Mixed_Masonry_Gallery_Widget_V105() );
			}
		}
	}

	new EMG_Elementor_Mixed_Masonry_Gallery_V105();
}
