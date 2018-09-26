<?php

defined('ABSPATH') or die("hey you can't access this file you sillu human !");

include_once plugin_dir_path( __FILE__ ).'/my_news_widget.php';

class my_news {
    
    public function __construct() {
        add_action('widgets_init', function(){register_widget('my_news_widget');});
        add_action('wp_loaded', array($this, 'save_email'));
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public static function install()
    {
        global $wpdb;
        
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}my_news_email (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);");
    }
    
    public static function uninstall()
    {
        global $wpdb;
        
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}my_news_email;");
    }
    
    public function save_email()
    {
        if (isset($_POST['my_news_email']) && !empty($_POST['my_news_email'])) {
            global $wpdb;
            $email = $_POST['my_news_email'];
            
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}my_news_email WHERE email = '$email'");
            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}my_news_email", array('email' => $email));
            }
        }
    }
    
    public function add_admin_menu()
    {
        $hook_send = add_submenu_page('My-plugin', 'Newsletter', 'Newsletter', 'manage_options', 'my_news', array($this, 'menu_html'));
        add_action('load-'.$hook_send, array($this, 'process_action'));
        
        $hook_create = add_submenu_page('My-plugin', 'Create', 'Create', 'manage_options', 'my_creat', array($this, 'menu_html_creat'));
        add_action('load-'.$hook_create, array($this, 'process_action'));
        
        add_submenu_page('My-plugin', 'Read', 'Read', 'manage_options', 'my_read', array($this, 'menu_html_read'));

        $hook_update = add_submenu_page('My-plugin', 'Update', 'Update', 'manage_options', 'my_update', array($this, 'menu_html_update'));
        add_action('load-'.$hook_update, array($this, 'process_action'));

        $hook_delete = add_submenu_page('My-plugin', 'Delete', 'Delete', 'manage_options', 'my_delete', array($this, 'menu_html_delete'));
        add_action('load-'.$hook_delete, array($this, 'process_action'));
    }
    
    public function menu_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        ?>
        <form method="post" action="options.php">
        <?php settings_fields('my_news_settings') ?>
        <?php do_settings_sections('my_news_settings') ?>
        <?php submit_button(); ?>
        </form>
        <form method="post" action="">
        <input type="hidden" name="send_newsletter" value="1"/>
        <?php submit_button('Envoyer la newsletter') ?>
        </form>
        <?php
    }
    
    public function menu_html_creat()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<br>';
        echo 'Ajoutez nouveau email';
        echo '<br>';
        echo '<br>';
        ?>
        </form>
        <form method="post" action="">
        <input type="email" name="create_email" required/>
        <?php submit_button('Add new') ?>
        </form>
        <?php
    }
    
    public function menu_html_read()
    {
        global $wpdb;
        
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<br>';
        echo '<br>';
        
        $tab_emails = $wpdb->get_results("SELECT id, email FROM {$wpdb->prefix}my_news_email");
        
        foreach ($tab_emails as $email) {
            ?>
            <h4>id <?=$email->id?> : <?=$email->email?></h4>
            <form method="GET" action="admin.php?">
            <input type="hidden" name="page" value="my_update">
            <input type="hidden" name="email_id" value="<?=$email->id?>">
            <button type="submit">Edit</button>
            </form>
            <br>
            <form method="GET" action="admin.php?">
            <input type="hidden" name="page" value="my_delete">
            <input type="hidden" name="delete_email" value="<?=$email->email?>">
            <input type="hidden" name="email_id" value="<?=$email->id?>">
            <button type="submit">Delete</button>
            </form>
            <?php
        }
    }
    
    public function menu_html_update()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<br>';
        echo 'Update email';
        echo '<br>';
        echo '<br>';
        
        if(isset($_GET['email_id'])){
            ?>
            <form method="post" action="http://localhost/wordpress-4.9.8-fr_FR/wordpress/wp-admin/admin.php?page=my_update">
            <input type="email" name="update_email" required>
            <input type="hidden" name="email_id" value="<?= $_GET['email_id'] ?>"/>
            <?php submit_button('update') ?>
            </form>
            <?php
        } else {
            echo '<a href="http://localhost/wordpress-4.9.8-fr_FR/wordpress/wp-admin/admin.php?page=my_read">Go to the read page to select email</a>';
        }
    }
    
    public function menu_html_delete()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<br>';
        echo 'Delete email';
        echo '<br>';
        echo '<br>';
        
        if(isset($_GET['email_id'])){
            ?>
            <h4><?= $_GET['delete_email']?></h4>
            <form name="delete_form" method="post" action="http://localhost/wordpress-4.9.8-fr_FR/wordpress/wp-admin/admin.php?page=my_delete">
            <input type="hidden" name="delete_email">
            <input type="hidden" name="email_id" value="<?= $_GET['email_id'] ?>"/>
            <button id="delete_btn">Delete</button>
            </form>
            <?php
        } else {
            echo '<a href="http://localhost/wordpress-4.9.8-fr_FR/wordpress/wp-admin/admin.php?page=my_read">Go to the read page to select email</a>';
        }
    }
    
    public function register_settings()
    {
        register_setting('my_news_settings', 'my_news_sender');
        register_setting('my_news_settings', 'my_news_object');
        register_setting('my_news_settings', 'my_news_content');
        
        add_settings_section('my_news_section', 'Paramètres d\'envoi', array($this, 'section_html'), 'my_news_settings');
        
        add_settings_field('my_news_sender', 'Expéditeur', array($this, 'sender_html'), 'my_news_settings', 'my_news_section');
        add_settings_field('my_news_object', 'Objet', array($this, 'object_html'), 'my_news_settings', 'my_news_section');
        add_settings_field('my_news_content', 'Contenu', array($this, 'content_html'), 'my_news_settings', 'my_news_section');
    }
    
    public function section_html()
    {
        echo 'Renseignez les paramètres d\'envoi de la newsletter.';
    }
    
    public function sender_html()
    {?>
        <input type="text" name="my_news_sender" value="<?php echo get_option('my_news_sender')?>"/>
        <?php
    }
    
    public function object_html()
    {?>
        <input type="text" name="my_news_object" value="<?php echo get_option('my_news_object')?>"/>
        <?php
    }
    
    public function content_html()
    {?>
        <textarea name="my_news_content"><?php echo get_option('my_news_content')?></textarea>
        <?php
    }
    
    public function process_action()
    {
        if (isset($_POST['send_newsletter'])) {
            $this->send_newsletter();
        } elseif (isset($_POST['create_email'])) {
            $this->create_email();
        } elseif (isset($_POST['update_email'])) {
            $this->update_email();
        } elseif (isset($_POST['delete_email'])) {
            $this->delete_email();
        }
    }
    
    public function send_newsletter()
    {
        global $wpdb;
        $recipients = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}my_news_email");
        $object = get_option('my_news_object', 'Newsletter');
        $content = get_option('my_news_content', 'Mon contenu');
        $sender = get_option('my_news_sender', 'no-reply@example.com');
        $header = array('From: '.$sender);
        
        foreach ($recipients as $_recipient) {
            $result = wp_mail($_recipient->email, $object, $content, $header);
        }
    }
    
    public function create_email() {
        
        if (isset($_POST['create_email']) && !empty($_POST['create_email'])) {
            global $wpdb;
            $email = $_POST['create_email'];
            
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}my_news_email WHERE email = '$email'");
            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}my_news_email", array('email' => $email));
            }
        }
    }

    public function update_email() {
        
        if (isset($_POST['update_email']) && !empty($_POST['update_email'])) {
            global $wpdb;
            $email = $_POST['update_email'];
            $email_id = $_POST['email_id'];
            $wpdb->update("{$wpdb->prefix}my_news_email", array("email" => $email), array('id' => $email_id));
        }
    }

    public function delete_email() {
        
        if (isset($_POST['delete_email'])) {
            global $wpdb;
            $email_id = $_POST['email_id'];  
            $wpdb->delete("{$wpdb->prefix}my_news_email", array("id" => $email_id));
        }
    }
}