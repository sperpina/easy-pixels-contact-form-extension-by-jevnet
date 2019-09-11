<?php
/*
Plugin Name: Easy Pixels - Contact Form Extension by JEVNET
Plugin URI: https://www.jevnet.es/contact-form-7-adwords-facebook-tracking-plugin
Description: Easy Pixels extension to track Contact Form 7
Version: 1.4
Author: JEVNET
Author URI: https://www.jevnet.es
License: GPLv2 or later
Text Domain: easy-pixels-contact-form-extension-by-jevnet
Domain Path:       /lang

Tracking: 
Analytics: Pageview
Facebook: Pageview
Bing: Pageview
Google Ads: Remarketing

*/


if ( !function_exists( 'add_action' ) ) {
	echo '¿Qué quieres hacer?';
	exit;
}

/* Translations */
add_action('plugins_loaded', 'jn_epcf7e_load_textdomain');
function jn_epcf7e_load_textdomain() {
	load_plugin_textdomain( 'easy-pixels-contact-form-extension-by-jevnet', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

define('JN_EasyPixelsCF7_PATH', dirname(__FILE__));
define('JN_EasyPixelsCF7_URL', plugins_url('', __FILE__));

include(JN_EasyPixelsCF7_PATH."/classes/CF7tracking.php");
if(is_admin())
{
	add_action('plugins_loaded', 'jn_easypixelsCF7_load_textdomain');
	add_action('easypixels_admintabs','jn_easypixels_admintabs_CF7',20);
	require_once(JN_EasyPixelsCF7_PATH . '/admin/easyPixelsCF7Admin.php');
	add_action('admin_init','save_jnEasyPixelsCF7Settings');
	add_action('admin_menu','jn_createEasyPixelsCF7MenuOption');
}
else
{
	if(defined('WPCF7_VERSION')&&(WPCF7_VERSION!==null))
	{
		add_action("wpcf7_contact_form","jn_setCF7_Listener",10);
	}
}


function jn_setCF7_Listener($i)
{
	$form_id=$i->id();
	$form_title=$i->title();
	add_action('wp_footer',function() use ($form_id,$form_title){jn_set_cf7_pixel3($form_id,$form_title);});
}


function jn_set_cf7_pixel3($theCF7id,$form_title)
{

	$jn_ADW_CF7_labels=get_option('jn_cf7_ADW_labels');
	$tracking="";

     if ( class_exists( 'jn_Analytics' ) ){$jnEPGA=new jn_Analytics();}
     if ( class_exists( 'jn_Facebook' ) ){$jnFB=new jn_Facebook();}
	$jn_gAds=new jn_easyGAds();
	$jn_bingAds=new jn_easyBingAds();
	$jn_TwitterAds=new jn_easypixels_Twitter();
	if(class_exists('jn_easyGTagManager')){$jn_GTMtracking=new jn_easyGTagManager();} 

	if(($jn_gAds->is_enabled())&&(isset($jn_ADW_CF7_labels['jn_GADW_CF7_label_'.$theCF7id]))&&($jn_ADW_CF7_labels['jn_GADW_CF7_label_'.$theCF7id]!='')&&($jn_gAds->getCode()!='')&&(get_option('jn_GADW_CF7_enable')!=''))
	{
		$label=explode("/", $jn_ADW_CF7_labels['jn_GADW_CF7_label_'.$theCF7id]);
		$label=(sizeof($label)==2)?$jn_ADW_CF7_labels['jn_GADW_CF7_label_'.$theCF7id]:$jn_gAds->getCode()."/".$label[0];
		$tracking.="console.log('tracking');gtag('event', 'conversion', {'send_to': '".$label."'});";
	}
	if(isset($jnEPGA)&&($jnEPGA->is_enabled())&&($jnEPGA->getCode()!=''))
	{
		$tracking.="gtag('event', 'generate_lead', {'event_label': '".$theCF7id."','event_category':'".$form_title."'});";
	}
	if(isset($jnFB)&&($jnFB->is_enabled())&&($jnFB->getCode()!=''))
	{
		$tracking.="fbq('track', 'Lead',{content_category: '".$form_title."'});";
	}
	if(($jn_bingAds->is_enabled())&&($jn_bingAds->getCode()!=''))
	{
		$tracking.="window.uetq = window.uetq || [];window.uetq.push({ 'ec':'form', 'ea':'send', 'el':'".$form_title."'}); ";
	}
	if(isset($jn_GTMtracking)&&($jn_GTMtracking->is_enabled())&&($jn_GTMtracking->getCode()!=''))
	{
		$tracking.="dataLayer.push({'event': 'formsent','formname':'".$form_title."'});";
	}

	
	if(($jn_TwitterAds->is_enabled())&&($jn_TwitterAds->getCode()!=''))
	{
		$tracking.="twq('track','Signup', {content_category:'contact form',content_name: '".$form_title."'});</script>';";
	}
	if($tracking!='')
	{
		echo "<script>document.addEventListener( 'wpcf7mailsent', function( event ) {".$tracking."},false );</script>";
	}

}

/* Translations */
function jn_easypixelsCF7_load_textdomain() {
	load_plugin_textdomain( 'jn-easyPixels', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}