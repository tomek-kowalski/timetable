<?php 

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Time_Table_Template extends Flight {
    function __construct() {
        add_action('wp_footer', array($this, 'custom_footer'));
        add_action('wp_head', array($this, 'custom_header'));
    }

    public static function custom_footer() {?>
        <footer> 
        <?php     
        $html = '';
        $html .=  '<h1 class="title">Flight Timetable</h1>';
        echo $html;
        ?>
        <?php wp_footer(); ?>
        </footer>
        <?php
    }

    public static function custom_header() {
        ?>
         <?php wp_head();
    }
}

if (!class_exists('Time_Table_Template')) {
    $time_table_template = new Time_Table_Template();
}






