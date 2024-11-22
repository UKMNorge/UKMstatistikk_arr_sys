<?php
/* 
Plugin Name: UKM Statistikk 2.0
Plugin URI: http://www.ukm.no
Description: UKM Norge statistikk på arrangørsystemet
Author: UKM Norge / Kushtrim Aliu
Version: 1.0
Author URI: http://www.ukm.no
*/

use UKMNorge\Nettverk\Administrator;

require_once('UKM/Autoloader.php');

class UKMstatistikk extends UKMNorge\Wordpress\Modul
{
    public static $action = 'statistikkTemplate';
    public static $path_plugin = null;

    /**
     * Initier Videresending-objektet
     *
     **/
    public static function init($plugin_path)
    {
        parent::init($plugin_path);
    }

    /**
     * Hooker modulen inn i Wordpress
     *
     * @return void
     */
    public static function hook()
    {
        add_action('user_admin_menu', ['UKMstatistikk', 'meny'], 101);
        add_action('wp_ajax_UKMstatistikk_ajax', ['UKMstatistikk', 'ajax']);
    }

    /**
     * Håndterer alle ajax-kall
     *
     * @return void
     */
    public static function ajax()
    {   
        $reques_method = $_SERVER['REQUEST_METHOD'];
        $subAction = $_REQUEST['controller'];

        try {
            require_once('ajax/' . $subAction . '.ajax.php');

        // Noe gikk galt
        } catch (Exception $e) {
            static::addResponseData('success', false);
            static::addResponseData('message', $e->getMessage());
            static::addResponseData('code', $e->getCode());
        }

        $data = json_encode(static::getResponseData());
        echo $data;
        die();
    }

    /**
     * Legg til alle scripts som videresendingen bruker
     * 
     * (og ja, det er en del!)
     *
     * @return void
     */
    public static function scripts_and_styles()
    {   
        wp_enqueue_script('WPbootstrap3_js');
        wp_enqueue_style('WPbootstrap3_css');

    	wp_enqueue_style('UKMStatistikkVueMIDIcons', 'https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');

        wp_enqueue_style('UKMstatistikkVueStyle', plugin_dir_url(__FILE__) . '/client/dist/assets/build.css');
        wp_enqueue_script('UKMstatistikkVueJs', plugin_dir_url(__FILE__) . '/client/dist/assets/build.js','','',true);
    }

    /**
     * Registrer menyer
     *
     **/
    public static function meny()
    {
        $page = add_menu_page(
            'UKM Norge Statistikk', 
            'Statistikk', 
            'subscriber', 
            'UKMstatistikk',
            ['UKMstatistikk','renderAdmin'], 
            'dashicons-editor-alignleft',
            402
        );
        add_action( 
            'admin_print_styles-' . $page, 
            ['UKMstatistikk', 'scripts_and_styles']
        );
        add_action( 
            'admin_print_styles-' . $page, 
            ['UKMstatistikk', 'add_klient_data']
        );
    }

    /**
     * Leg til data som brukes på klient on init
     *
     * @return void
     */
    public static function add_klient_data() {
        $type = str_replace(
            'UKMnettverket_',
            '',
            $_GET['page']
        );

        $current_admin = new Administrator(get_current_user_id());
        $omrader = $current_admin->getOmrader();

        // Legg til kommuner og fylker som brukeren har tilgang til og som skal brukes på klient side
        echo '<script>var ukm_statistikk_klient = [];';
        echo 'ukm_statistikk_klient["omrade"]=[];</script>';
        foreach($omrader as $omrade) {
            echo '<script>console.log("bcb"); ukm_statistikk_klient["omrade"].push({"type": "'. $omrade->getType() .'", "id" : "'. $omrade->getForeignId() .'", "name": "' . $omrade->getNavn() .'"});</script>';
        }
        echo '<script>ukm_statistikk_klient["is_superadmin"]='. is_super_admin() .';</script>';
    }
}


UKMstatistikk::init(__DIR__);
UKMstatistikk::hook();
