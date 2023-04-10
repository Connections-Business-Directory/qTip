<?php
/**
 * Experimental qTip templates and shortcodes for the Connections Business Directory plugin.
 *
 * @package   Connections Business Directory Extension - qTip
 * @category  Template
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      https://connections-pro.com
 * @copyright 2023 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Business Directory Extension - qTip
 * Plugin URI:        https://connections-pro.com
 * Description:       Experimental qTip templates and shortcodes for the Connections Business Directory plugin.
 * Version:           1.0
 * Requires at least: 5.6
 * Requires PHP:      7.0
 * Author:            Steven A. Zahm
 * Author URI:        https://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cnt_members
 * Domain Path:       /languages
 */

namespace Connections_Directory\Extension;

use cnShortcode_Connections;
use cnTemplateFactory;
use Connections_Directory\Utility\_url;

final class qTip {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var string The absolute path this file.
	 *
	 * @since 1.0
	 */
	private $file = '';

	/**
	 * @var string The URL to the plugin's folder.
	 *
	 * @since 1.0
	 */
	private $url = '';

	/**
	 * @var string The absolute path to this plugin's folder.
	 *
	 * @since 1.0
	 */
	private $path = '';

	/**
	 * @var string The basename of the plugin.
	 *
	 * @since 1.0
	 */
	private $basename = '';

	public static function instance() {

		if ( ! self::$instance instanceof self ) {

			$self = new self();

			$self->file     = __FILE__;
			$self->url      = plugin_dir_url( $self->file );
			$self->path     = plugin_dir_path( $self->file );
			$self->basename = plugin_basename( $self->file );

			$self->includeDependencies();
			$self->hooks();

			self::$instance = $self;
		}

		return self::$instance;
	}

	/**
	 * Get plugin base URL.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function getBaseURL() {

		return $this->url;
	}

	private function includeDependencies() {

		include_once 'templates/qtip-card/qTipCard.php';
		include_once 'templates/qtip-vcard/qTipvCard.php';
	}

	private function hooks() {

		add_action( 'init', array( __CLASS__, 'registerScripts' ) );
		add_action( 'cn_register_template', array( __CLASS__, 'registerTemplates' ) );

		add_shortcode( 'connections_vcard', array( __CLASS__, 'shortcode_vCard' ) );
		add_shortcode( 'connections_qtip', array( __CLASS__, 'shortcode_qTip' ) );
	}

	public static function registerScripts() {

		// If SCRIPT_DEBUG is set and TRUE load the non-minified CSS files, otherwise, load the minified files.
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$url = _url::makeProtocolRelative( qTip::instance()->getBaseURL());

		wp_register_script( 'jquery-qtip', "{$url}assets/vendor/jquery-qtip/jquery.qtip{$min}.js", array( 'jquery' ), '3.0.3', true );
		wp_register_style( 'cn-qtip', "{$url}assets/vendor/jquery-qtip/jquery.qtip{$min}.css", array(), '3.0.3' );
	}

	public static function registerTemplates() {

		cnTemplateFactory::register(
			array(
				'class'       => 'qTipCard',
				'name'        => 'qTip Card',
				'type'        => 'qtip',
				'slug'        => 'qtip-card',
				'version'     => '1.0',
				'author'      => 'Steven A. Zahm',
				'authorURL'   => 'https://connections-pro.com',
				'description' => 'Used to display the contact tooltips utilizing the qTip jQuery Plugin.',
				'path'        => plugin_dir_path( __FILE__ ) . 'templates/qtip-card',
				'url'         => plugin_dir_url( __FILE__ ) . 'templates/qtip-card',
			)
		);

		cnTemplateFactory::register(
			array(
				'class'       => 'qTipvCard',
				'name'        => 'qTip vCard',
				'type'        => 'qtip',
				'slug'        => 'qtip-vcard',
				'version'     => '1.0',
				'author'      => 'Steven A. Zahm',
				'authorURL'   => 'https://connections-pro.com',
				'description' => 'Used to display the vCard download tooltips utilizing the qTip jQuery Plugin.',
				'path'        => plugin_dir_path( __FILE__ ) . 'templates/qtip-vcard',
				'url'         => plugin_dir_url( __FILE__ ) . 'templates/qtip-vcard',
			)
		);
	}

	public static function shortcode_vCard( $atts, $content = null, $tag = 'connections_vcard' ) {

		$atts = shortcode_atts(
			array(
				'id' => null,
			),
			$atts,
			$tag
		);

		if ( empty( $atts['id'] ) || ! is_numeric( $atts['id'] ) || empty( $content ) ) {
			return '';
		}

		$qTipContent = '<span class="cn-qtip-content-vcard" style="display: none">' . cnShortcode_Connections::shortcode(
				array(
					'id'       => $atts['id'],
					'template' => 'qtip-vcard'
				)
			) . '</span>';

		return '<span class="cn-qtip-vcard">' . $content . $qTipContent . '</span>';
	}

	public static function shortcode_qTip( $atts, $content = null, $tag = 'connections_qtip' ) {
		$atts = shortcode_atts(
			array(
				'id' => null,
			),
			$atts,
			$tag
		);

		if ( empty( $atts['id'] ) || ! is_numeric( $atts['id'] ) || empty( $content ) ) {
			return '';
		}

		$qTipContent = '<span class="cn-qtip-content-card" style="display: none">' . cnShortcode_Connections::shortcode(
				array(
					'id'       => $atts['id'],
					'template' => 'qtip-card'
				)
			) . '</span>';

		return '<span class="cn-qtip-card">' . $content . $qTipContent . '</span>';
	}
}

add_action(
	'Connections_Directory/Loaded',
	static function() {
		qTip::instance();
	}
);
