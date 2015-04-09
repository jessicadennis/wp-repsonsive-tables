<?php
/**
 * @package WP_Responsive_tables
 * @version 0.1
 */
/*
Plugin Name: WP Responsive Tables
Description: Implements Chris Coyier's responsive data tables (https://css-tricks.com/responsive-data-tables/) with a convenient Wordpress shortcode.
Author: Jessica Dennis
Version: 0.1
Author URL: http://jessicadennis.me
*/

/* Support Functions */

/* Make the shortcode not generate those horrible empty <p></p>'s because HATE. Code adapted from https://gist.github.com/bitfade/4555047 */
add_filter("the_content", "wprt_filter_the_content");

function wprt_filter_the_content($content) {

	// opening tag
	$rep = preg_replace("/(<p>)?\[(responsive-table)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);

	// closing tag
	$rep = preg_replace("/(<p>)?\[\/(responsive-table)](<\/p>|<br \/>)?/","[/$2]",$rep);

	return $rep;

}

/* Variables */
$resp_table_css = ''; //we will return the computed css and add it to wp_head


function responsive_table_handler($atts,  $content = null) {

    /* Include Simple DOM Parster */
    require_once(plugin_dir_path( __FILE__ ) . 'simple_html_dom.php');
	
    /* Use Simple HTML DOM to process our content and pull the th tags */
    $html = str_get_html($content);
    $th = $html->find('th');
	
	echo '<pre>' . var_dump($th) . '</pre>';

    /* now let's set a randomish ID on the particular table we're working on */
    $table = $html->find('table');
    $tid = rand(1024,9999);
    $table->id = $tid;
    $table->class='responsive-table';

    /* now let's generate our CSS */
    for ($i = 1, $size = count($th); $i <= $size; $i++) {
        $resp_table_css .= '#' . $tid . '.responsive-table td:nth-of-type(' . $i . '):before { content: "' . $th[$i]->innertext . '"; }';
    /* echo the generated css inline because meh, why not */
    }
	echo '<style type="text/css">' . $resp_table_css . '</style>';
	echo $content;
}

add_shortcode ( 'responsive-table',  'responsive_table_handler');

function resp_table_css() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'wp-responsive-tables.css' , __FILE__ ) . '" />';
}
add_action('wp_head', 'resp_table_css');
?>