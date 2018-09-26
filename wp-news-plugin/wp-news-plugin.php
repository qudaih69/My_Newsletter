<?php

/**
* @package newsplugin
*/

/*
Plugin Name: My-plugin
Plugin URI: http://google.com
Description: newsletter plugin
Version: 0.1.0
Author: anas kuzanagi
Author URI: http://google.com
*/

// Exist if accessed directly
defined('ABSPATH') or die("hey you can't access this file you sillu human !");

class my_news_plugin {
    
    public function __construct() {
        require_once (plugin_dir_path( __FILE__ ).'/my_news.php');
        $my_news = new my_news();
        register_activation_hook(__FILE__, array('my_news', 'install'));
        register_uninstall_hook(__FILE__, array('my_news', 'uninstall'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function add_admin_menu()
    {
        add_menu_page('News plugin', 'News plugin', 'manage_options', 'My-plugin', array($this, 'menu_html'));
    }
    
    public function menu_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
    }
    
    function register() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }

    function enqueue() {
        wp_enqueue_style('mypluginstyle', plugins_url('/assets/css/admin.css', __FILE__));
        wp_enqueue_script('mypluginscript', plugins_url('/assets/js/admin.js', __FILE__));
    }
}

$my_news_plugin = new my_news_plugin();
$my_news_plugin->register();


                                
