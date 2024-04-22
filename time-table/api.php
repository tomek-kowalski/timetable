<?php 

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Time_Table_Template {
    function __construct() {
        add_action('wp_footer', array($this, 'custom_footer'));
        add_action('wp_head', array($this, 'custom_header'));
    }

    public static function custom_footer() {?>
        <footer>
        <?php wp_footer(); ?>
        </footer>
        <?php
    }

    public static function custom_header() {
        ?>
        <html <?php language_attributes(); ?>>
        <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width,user-scalable=no">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
        
         <?php wp_head();
    }
}

if (!class_exists('Time_Table_Template')) {
    $time_table_template = new Time_Table_Template();
}






