<?php

/*
Plugin Name: RSS Featured Images
Plugin URI: http://www.phoenixroberts.com
Description: Inserts featured images from your posts into your site's RSS feed. Allows for featured image size, positioning and, custom format options.
Author: Phoenix Roberts
Version: 1.3.0
Author URI: http://www.phoenixroberts.com
*/

$gblPluginPath = plugin_dir_path( __FILE__ );
include($gblPluginPath . "mainpage.php");

if (!function_exists('write_log')) {
	function write_log ( $log )  {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}


abstract class PxrPluginBase {
	abstract public function DisplayMainConfigPage();
	abstract public function InitMenu();
	abstract public function InitResources($hook);
	abstract public function InitSettings();
	abstract public function InitMeta($links, $file);
	abstract public function InitActionLinks($links);
}


class PxrUtils extends PxrPluginBase {
	private $m_sPluginName = "PXR_Utils";
	private $m_MainPage = null;
	private $m_MainPageSlug = "rss-feed-featured-images-main";

	private $m_sAlignmentOptionName = "rss_fi_image_alignment";
	private $m_sSizeOptionName = "rss_fi_image_size";
	private $m_OptionsGroup = "rss_fi_group";

	private $m_sBeginWrapperOptionName = "rss_fi_begin_wrapper";
	private $m_sEndWrapperOptionName = "rss_fi_end_wrapper";
	private $m_sEnableAlignmentOptionName = "rss_fi_enable_image_alignment";

	private static $m_Instance = null;

	public static function Instance() {
		if(self::$m_Instance == null) {
			self::$m_Instance = new PxrUtils();
			self::$m_Instance->initPlugin();
		}
		return self::$m_Instance;
	}

	function __construct() {}

	private function initPlugin() {
		$this->m_MainPage = new MainPage($this->m_OptionsGroup, $this->m_sAlignmentOptionName,
			$this->m_sSizeOptionName, $this->m_sBeginWrapperOptionName,
			$this->m_sEndWrapperOptionName, $this->m_sEnableAlignmentOptionName);
		add_action( 'admin_enqueue_scripts', array($this,'InitResources'));
		add_action('admin_menu', array($this,'InitMenu'));
		add_filter('the_excerpt_rss', array($this,'AddFeaturedImageToRSS'));
		add_filter('the_content_feed', array($this,'AddFeaturedImageToRSS'));
		add_action('admin_init', array($this,'InitSettings'));
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this,'InitActionLinks') );
		//add_filter( 'plugin_row_meta', array($this,'InitMeta'), 10, 2 );

	}
	public function InitMeta($links, $file) {
		//None at this time
	}
	public function InitActionLinks($links){
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=rss-feed-featured-images-main">Settings</a>'
			),
			$links
		);
	}
	public function GetOptionValue($optionName) {
		$retVal = null;
		$bProcessRequest = true;
		$defaultValue = null;
		switch($optionName) {
			case $this->m_sBeginWrapperOptionName:
				$defaultValue = "";
				break;
			case $this->m_sEndWrapperOptionName:
				$defaultValue = "";
				break;
			case $this->m_sEnableAlignmentOptionName:
				$defaultValue = "true";
				break;
			case $this->m_sAlignmentOptionName:
				$defaultValue = 'left-above';
				break;
			case $this->m_sSizeOptionName:
				$defaultValue = 'thumbnail';
				break;
			default:
				$bProcessRequest = false;
		}
		if($bProcessRequest) {
			$retVal = get_option($optionName);
			if (empty($retVal)){
				update_option($optionName, $defaultValue);
				$retVal = get_option($optionName);
			}
		}
		return $retVal;
	}

	public function InitSettings() {
		$this->registerSettings();
	}
	private function registerSettings() {
		register_setting($this->m_OptionsGroup, $this->m_sAlignmentOptionName);
		register_setting($this->m_OptionsGroup, $this->m_sSizeOptionName);
		register_setting($this->m_OptionsGroup, $this->m_sEnableAlignmentOptionName);
		register_setting($this->m_OptionsGroup, $this->m_sBeginWrapperOptionName);
		register_setting($this->m_OptionsGroup, $this->m_sEndWrapperOptionName);
	}
	private function decodeImageAlignment($alignmentCode) {
		$retVal = 'display: block; padding-bottom: 10px; clear: both;';
		switch ($alignmentCode) {
			case "left":
				$retVal = 'display: block; padding-bottom: 10px; clear:both;';
				break;
			case "centered":
				$retVal = 'display: block; margin: auto; padding: 10px;';
				break;
			case "left-wrap":
				$retVal = 'float: left; padding: 15px 15px 15px 0px;';
				break;
			case "right-wrap":
				$retVal = 'float: right; padding: 15px 0px 15px 15px;';
				break;
			default:
				break;
		}
		return $retVal;
	}
	public function AddFeaturedImageToRSS($content)
	{
		write_log("RSS feed handler invoked");
		global $post;
		if ( has_post_thumbnail( $post->ID ) ){
			$imageSize = $this->GetOptionValue($this->m_sSizeOptionName);
			$alignmentEnabled = $this->GetOptionValue($this->m_sEnableAlignmentOptionName);
			if($alignmentEnabled == "true") {
				$alignmentStyle = $this->decodeImageAlignment($this->GetOptionValue($this->m_sAlignmentOptionName));
				$content = get_the_post_thumbnail($post->ID, $imageSize, array('style' => $alignmentStyle)) . $content;
				write_log("RSS feed preformatted alignment applied");
			}
			else {
				write_log("RSS feed custom alignment applied");
				$sBeginWrapperOptionValue = $this->GetOptionValue($this->m_sBeginWrapperOptionName);
				$sEndWrapperOptionValue = $this->GetOptionValue($this->m_sEndWrapperOptionName);
				$content = $sBeginWrapperOptionValue . get_the_post_thumbnail($post->ID, $imageSize) . $sEndWrapperOptionValue . $content;
			}
		}
		else {
			write_log("No Post Id Present");
		}
		return $content;
	}
	public function DisplayMainConfigPage() {
		$this->m_MainPage->DisplayPage();
	}
	public function InitMenu() {
		add_options_page('RSS Featured Images', 'RSS Featured Images',
			'manage_options',  $this->m_MainPageSlug, array($this,'DisplayMainConfigPage'));
	}
	public function InitResources($hook) {
		switch($hook) {
			default:
				//CSS
				wp_enqueue_style($this->m_sPluginName . "_bootstrap-css", get_template_directory_uri() . "/styles/bootstrap/bootstrap.min.css");
				wp_enqueue_style($this->m_sPluginName . "_fontawesome-css", get_template_directory_uri() . "/font-awesome/css/font-awesome.min.css");
				wp_enqueue_style($this->m_sPluginName . "_plugin_styles", plugins_url("styles/plugin.css", __FILE__));
				//Javascript
				wp_enqueue_script('jquery');
				wp_enqueue_script($this->m_sPluginName . "_bootstrap-scripts", plugins_url("/scripts/bootstrap/bootstrap.min.js", __FILE__));
				wp_enqueue_script($this->m_sPluginName . "_theme-scripts", plugins_url("/scripts/plugin.js", __FILE__),
					false, "2.4");
				break;
		}
	}
}

PxrUtils::Instance();

?>