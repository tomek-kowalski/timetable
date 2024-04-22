<?php

/**
 * @link              http://www.kowalski-consulting.com/
 * @since             1.00
 * @package           Time Table
 * 
 * @wordpress-plugin
 * Plugin Name:       Flight Time Table
 * Description:       configurable time table by API 
 * Version:           1.00
 * Author:            Tomasz Kowalski
 * Author URI:        https://kowalski-consulting.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       time_table
 * Date:    		  2024-04-22  
 */

class Flight {

    function __construct() {
        $this->define_constants();
        $this->add_files();
        add_action('init', [$this, 'add_rewrite_rule'],1);
        add_action('template_redirect', [$this, 'load_custom_template']);
        add_action('wp_enqueue_scripts', [$this, 'plugin_styles']);
        add_action( 'admin_enqueue_scripts', [$this,'admin_styles'] );  
        add_action('admin_menu', [$this,'flight_table_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
        register_setting('flight_table_options', 'flight_table_params');
    }

    public function flight_table_settings_page() {

        $params     = get_option('flight_table_params', []);
        $agent      = isset($params['agent']) ? $params['agent'] : '';
        $departures = isset($params['departures']) ? $params['departures'] : '';
        $destinations     = isset($params['destinations']) ? $params['destinations'] : '';
        $periodToDate      = isset($params['periodToDate']) ? $params['periodToDate'] : '';
        $price      = isset($params['price']) ? $params['price'] : '';
        $airlineCodes      = isset($params['airlineCodes']) ? $params['airlineCodes'] : '';


        ?>
        <div class="wrap">
            <h1>Flight Table API params</h1>
            <p>You can configure API output by passing params to the query.</p>
            <form method="post" action="options.php" class="admin-table__form">
                    <?php settings_fields('flight_table_options'); ?>
                    <div class="admin-table__row">
                        <label for="agent">Agent ID:</label>
                        <input type="text" name="flight_table_params[agent]" id="agent" value="<?php echo esc_attr($agent); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <label for="departures">Departures:</label>
                        <input type="text" name="flight_table_params[departures]" id="departures" value="<?php echo esc_attr($departures); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <label for="destinations">Destinations:</label>
                        <input type="text" name="flight_table_params[destinations]" id="destinations" value="<?php echo esc_attr($destinations); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <label for="periodToDate">Return Date:</label>
                        <input type="text" name="flight_table_params[periodToDate]" id="periodToDate" value="<?php echo esc_attr($periodToDate); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <label for="price">Price:</label>
                        <input type="text" name="flight_table_params[price]" id="price" value="<?php echo esc_attr($price); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <label for="airlineCodes">Airline Codes:</label>
                        <input type="text" name="flight_table_params[airlineCodes]" id="airlineCodes" value="<?php echo esc_attr($airlineCodes); ?>"><br>
                    </div>
                    <div class="admin-table__row">
                        <input type="submit" name="submit" value="Save" class="table-save">
                    </div>
            </form>
        </div>
        <?php
    }

    function flight_table_menu() {
        add_menu_page(
            'Flight Table Settings',    
            'Flight Table',             
            'manage_options',       
            'flight_table-settings',    
            [$this, 'flight_table_settings_page'],
            'dashicons-admin-generic',
            1
        );
    }

    public static function getFlightsdata() {

        $params            = get_option('flight_table_params', []);
        $agent             = isset($params['agent']) ? $params['agent'] : '';
        $departures        = isset($params['departures']) ? $params['departures'] : '';
        $destinations      = isset($params['destinations']) ? $params['destinations'] : '';
        $periodToDate      = isset($params['periodToDate']) ? $params['periodToDate'] : '';
        $price             = isset($params['price']) ? $params['price'] : '';
        $airlineCodes      = isset($params['airlineCodes']) ? $params['airlineCodes'] : '';

        $url = "https://api/fares/{$agent}/{$departures}/{$destinations}/{$price}/{$periodToDate}?airlineCodes={$airlineCodes}";
    
        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'param' => json_encode([
                "agentId"      => $agent,
                "departure"    => $departures,
                "destination"  => $destinations,
                "returnDate"   => $periodToDate,
                "price"        => $price,
                "airlineCodes" => $airlineCodes
            ])
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-pass: xxxxx',
            'api-key: xxxxx',
        ]);
    
        $api_response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            curl_close($ch);

            $flights_json_path = FLIGHT_PATH . '/api/flights.json';
            if (file_exists($flights_json_path)) {
                $flights_json = file_get_contents($flights_json_path);
                $data = json_decode($flights_json, true);
                return $data;
            }
        }
    
        curl_close($ch);
    
        $data = json_decode($api_response, true);
    
        return $data;
    }
    
    
    

    public static function activate(){
		update_option( 'rewrite_rules', '' );
	}

    public function add_files() {
        require_once FLIGHT_PATH . '/time-table/rendering.php'; 
    }

    public function admin_styles() {
        wp_enqueue_style( 'flight-admin', FLIGHT_URL . 'assets/css/admin.css',array(),'all' );
    }

    public function plugin_styles() {
        wp_enqueue_style('front-style', FLIGHT_URL . '/assets/css/plugin-styles.css', array(), 'all');
    }

    public function define_constants()
	{
        if (!defined('FLIGHT_PATH')) {
        define( 'FLIGHT_PATH', plugin_dir_path( __FILE__ ) );
        }
        if (!defined('FLIGHT_URL')) {
		define( 'FLIGHT_URL', plugin_dir_url( __FILE__ ) );
        }
        if (!defined('FLIGHT_VERSION')) {
		define( 'FLIGHT_VERSION', '1.0.0' );
        }
	}

    public function add_rewrite_rule() {
        add_rewrite_rule('^timetable/?', 'index.php?timetable_template=1', 'top');
        flush_rewrite_rules();
    }
    

    public function load_custom_template() {
        $base = preg_replace('#^https?://#i', '', site_url());
        $requested_url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($requested_url, '/'));
        $last_part = end($parts);
    
        $modified_url = $base . '/' . $last_part . '/';
        $expected_url = $base . '/timetable/';
        
        if ($modified_url === $expected_url) {
            $custom_template = FLIGHT_PATH . 'templates/template-timetable.php';
            if (file_exists($custom_template)) {
                include $custom_template;
                exit;
            }
        } 
    }
}

if( class_exists( 'Flight' ) ){
    register_activation_hook( __FILE__, array( 'Flight', 'activate' ) );
    $flight = new Flight();
}
