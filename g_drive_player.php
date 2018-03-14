<?php
/*
Plugin Name: G Drive Player
Plugin URI:  https://github.com/hklcf/G-Drive-Player-for-Wordpress
Description: Embed Google Drive video to WordPress
Version:     1.0
Author:      HKLCF
Author URI:  https://eservice-hk.net/
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: gdp
Domain Path: /languages
*/

function add_gdp_menu() {
	add_plugins_page('G Drive Player Setting', 'G Drive Player', 'administrator', 'gdp-setting', 'gdp_setting_function');
	$option_group = 'gdp_setting';
	$option_name = 'gdp_player_option';
	$setting_section = 'gdp_setting_section';
	register_setting( $option_group, $option_name );

	add_settings_section( $setting_section, 'Setting', 'gdp_setting_section_function', $option_group );
	function gdp_setting_section_function() {
		echo 'G Drive Player Setting';
	}

	add_settings_field( 'gdp_player_id', 'Player ID', 'gdp_player_id_function', $option_group, $setting_section );
	function gdp_player_id_function() {
		$gdp_player_option = get_option( 'gdp_player_option' );
		echo '<input class="regular-text"  name="gdp_player_option[id]" type="text" value="'.$gdp_player_option['id'].'" placeholder="player">';
	}

	add_settings_field( 'gdp_player_size', 'Player Size', 'gdp_player_size_function', $option_group, $setting_section );
	function gdp_player_size_function() {
		$gdp_player_option = get_option( 'gdp_player_option' );
		echo '<label>Height</label><input class="small-text"  name="gdp_player_option[height]" type="text" value="'.$gdp_player_option['height'].'" placeholder="100%">';
		echo '<label>Width</label><input class="small-text"  name="gdp_player_option[width]" type="text" value="'.$gdp_player_option['width'].'" placeholder="100%">';
	}
}
add_action( 'admin_menu', 'add_gdp_menu' );

function gdp_setting_function() {
	$option_group = 'gdp_setting';
	echo '<h1>G Drive Player</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( $option_group );
	do_settings_sections( $option_group );
	submit_button();
	echo '</form>';
}

function gdp_video_func( $atts, $link = '' ) {
	$gdp_player_option = get_option( 'gdp_player_option' );
  $links = explode(',', $link);
  $link_nodes = count($links);
  $link_node = rand(0, $link_nodes-1);
	$thumbnail = $gdp_player_option['thumbnail'] === '1' ? get_the_post_thumbnail_url('','full') : '';
	$atts = shortcode_atts(
		array(
			'id' => $gdp_player_option['id'],
		), $atts, 'video' );
	preg_match('/(https:\/\/drive\.google\.com\/file\/d\/)(.*)(\/view.*)/', $links[$link_node], $matches);
	$docid = $matches[2];
	$player_div = '<div id="'.esc_html($atts['id']).'"><iframe width="'.$gdp_player_option['width'].'" height="'.$gdp_player_option['height'].'" src="https://drive.google.com/file/d/'.$docid.'/preview" frameborder="0" allowfullscreen></iframe></div>';
	return $player_div;
}
add_shortcode( 'video', 'gdp_video_func' );

function gdp_video_quicktags() {
	if(wp_script_is('quicktags')) {
?>
		<script type="text/javascript">
			QTags.addButton( 'video', 'video', '[video]', '[/video]', '', 'G Drive Player', 201 );
		</script>
<?php
	}
}
add_action( 'admin_print_footer_scripts', 'gdp_video_quicktags' );
?>
