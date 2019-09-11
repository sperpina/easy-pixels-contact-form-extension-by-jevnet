<?php



function jn_easypixels_admintabs_CF7()
{
	?>
	<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'CF7' ), admin_url( 'admin.php?page=CF7easytracking' ) ) ); ?>" class="nav-tab<?php if ('CF7easytracking' == $_GET['page'] ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Contact Form 7' ); ?></a> 
	<?php
}

function jn_createEasyPixelsCF7MenuOption()
{

	if(!class_exists( 'jn_Analytics' ) )
	{
		add_menu_page('Easy Pixels Settings','Easy Pixels','administrator','CF7easytracking','jn_initCF7TrackingOptions');
	}
	add_submenu_page('easypixels', 'Contact form tracking', 'CF7 tracking', 'administrator', 'CF7easytracking', 'jn_initCF7TrackingOptions' );
}


function jn_initCF7TrackingOptions()
{
	if ( class_exists( 'jn_Analytics' ) ){$jnEPGA=new jn_Analytics();}
	if ( class_exists( 'jn_Facebook' ) ){$jnFB=new jn_Facebook();}
	if ( class_exists( 'jn_easyBingAds' ) ){$jnFB=new jn_easyBingAds();}

	if (!class_exists( 'jn_Analytics' ) ){require(JN_EasyPixelsCF7_PATH . '/admin/page-basicNotInstalled.php');}
	else{require(JN_EasyPixelsCF7_PATH . '/admin/page-easyPixelsCF7Admin.php');}
}
;


add_action('easyPixelsContactForm','jn_cf7TrackingAdminSettings');
function jn_cf7TrackingAdminSettings()
{
	if ( class_exists( 'WPCF7_ContactForm' ) )
	{
		$jnEPGA=new jn_Analytics();
		$jn_gAds=new jn_easyGAds();
		$jn_bingAds=new jn_easyBingAds();
		$jnFB=new jn_Facebook();
		$jn_TwitterAds=new jn_easypixels_Twitter();
		$jn_ADW_CF7_labels=get_option('jn_cf7_ADW_labels');
		$jn_ADW_CF7_enabled=(get_option('jn_GADW_CF7_enable')=='on')?' checked="checked"':'';
	?>
	<h2 class="title"><br/>Contact form 7</h2>
	<table class="form-table">
	<?php
	if(($jn_gAds->is_enabled())&&($jn_gAds->getCode()!=''))
	{
	?>
		<tr>
			<th><?php echo __('Enable Google Ads tracking','easy-pixels-contact-form-extension-by-jevnet');?></th><td><input type="checkbox" id="jn_GADW_CF7_enable" name="jn_GADW_CF7_enable"<?php echo $jn_ADW_CF7_enabled; ?>><label for="jn_GADW_CF7_enable"><?php echo __('Enable','easy-pixels-contact-form-extension-by-jevnet');?></label></td><td></td>
		</tr>
	<?php 
	}
		$args = array('post_type'=> 'wpcf7_contact_form','post_status' => 'publish','nopaging' => true);
		$the_query = new WP_Query( $args );

		$idCollection=Array();
		if($the_query->have_posts() )
		{
			$postCounter=0;
			while($postCounter<sizeof($the_query->posts))
			{
				$CF7id=(int)strip_tags(apply_filters('the_content', $the_query->posts[$postCounter]->ID ));
				$CF7title=strip_tags(apply_filters('the_content', $the_query->posts[$postCounter]->post_title ));
				$CF7title=sanitize_text_field($CF7title);
				$GAD_label_id='jn_GADW_CF7_label_'.$CF7id;

				$value=(isset($jn_ADW_CF7_labels[$GAD_label_id]))?$jn_ADW_CF7_labels[$GAD_label_id]:"";

				$trackingCode='';
				if(($jnEPGA->is_enabled())&&($jnEPGA->getCode()!=''))
				{
					$trackingCode.='<img src="'.JN_EasyPixels_URL.'/img/google.png" alt="Analytics" width="15px">'."&nbsp;gtag('event', 'generate_lead', {'event_label': '".$CF7id."','event_category':'".$CF7title."'});";
				}
				if(($jn_bingAds->is_enabled())&&($jn_bingAds->getCode()!=''))
				{
					if($trackingCode!=''){$trackingCode.='<br/>';}
					$trackingCode.='<img src="'.JN_EasyPixels_URL.'/img/bing.png" alt="Bing" width="15px">'."&nbsp;window.uetq.push({ 'ec':'form', 'ea':'send', 'el':'".$CF7title."'});";
				}
				if(($jnFB->is_enabled())&&($jnFB->getCode()!=''))
				{
					if($trackingCode!=''){$trackingCode.='<br/>';}
					$trackingCode.='<img src="'.JN_EasyPixels_URL.'/img/fb.png" alt="Facebook" width="15px">'."&nbsp;fbq('track', 'Lead',{content_category: '".$CF7title."'});";
				}
				if(($jn_TwitterAds->is_enabled())&&($jn_TwitterAds->getCode()!=''))
				{
					$trackingCode.="twq('track','Signup', {content_category:'contact form',content_name: '".$form_title."'});</script>';";
				}
	$gAdsFields='';
	if(($jn_gAds->is_enabled())&&($jn_gAds->getCode()!=''))
	{
		$gAdsFields='<td>'.$jn_gAds->getCode().' / <input value="'.$value.'" type="text" id="'.$GAD_label_id.'" name="'.$GAD_label_id.'" placeholder="YYYYYYYYYYYYYYYYYYY"></td>';
	}
	?>

				<tr>
					<th><label for="jn_GADW_CF7_label">(<?php echo $CF7id.') - '.$CF7title; ?></label></th><?php echo  $gAdsFields; ?><td><?php echo $trackingCode; ?></td>
				</tr>

				<?php
				$postCounter++;
			}
		}
		?>
		</table>
<?php }
}

function save_jnEasyPixelsCF7Settings()
{
	if ( false == get_option( 'jnAnalyticsCF7Settings-group' ) ) {add_option( 'jnAnalyticsCF7Settings-group' );}
	register_setting('jnAnalyticsCF7Settings-group','jn_GADW_CF7_enable');
	$CF7_ADW_labels=Array();
	foreach ($_POST as $key => $value) 
	{
		if(strpos($key, 'jn_GADW_CF7_label_')===0)
		{
			$CF7_ADW_labels[$key]=jnEasyPixelsCF7_sanitizeLabel($value);
		}
	}
	if(sizeof($CF7_ADW_labels)>0){update_option('jn_cf7_ADW_labels', $CF7_ADW_labels);}
}

function jnEasyPixelsCF7_sanitizeLabel($theLabel)
{
	$theLabel=sanitize_text_field($theLabel);
	if((strpos($theLabel,'/')>0)&&(strpos(strtoupper($theLabel),'AW-')==0)){$theLabel=substr($theLabel, strpos($theLabel,'/')+1);}
	$theLabel = preg_replace('/[^\w]/', '', $theLabel);
	return $theLabel;
}
?>