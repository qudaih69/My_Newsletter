<?php

defined('ABSPATH') or die("hey you can't access this file you sillu human !");

class my_news_widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct('my_news', 'Newsletter', array('description' => 'Un formulaire d\'inscription à la newsletter.'));
    }
    
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'];
        echo apply_filters('widget_title', $instance['title']);
        echo $args['after_title'];
        ?>
        <form action="" method="post">
        <p>
        <label for="my_news_email">Votre email :</label>
        <input id="my_newsr_email" name="my_news_email" type="email"/>
        </p>
        <input type="submit"/>
        </form>
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
        </p>
        <?php
        // echo apply_filters('widget_title', $instance['title']);
    }
}