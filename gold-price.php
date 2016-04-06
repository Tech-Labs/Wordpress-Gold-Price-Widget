<?php
/*
Plugin Name: Gold Price Widget
Description: Advanced Gold Price Widget
Author: Ibrahim Mohamed Abotaleb
Version: 1.0
Author URI: http://mrkindy.com/
Text Domain: gold-price
Domain Path: /languages
*/
// Creating the widget
class gold_price extends WP_Widget
{
    function __construct()
    {
        parent::__construct( // Base ID of your widget
            'gold_price', // Widget name will appear in UI
            __('Gold Price Widget', 'gold-price'), // Widget description
            array('description' => __('Advanced Gold Price Widget.','gold-price'))
            );
    }
    // Creating widget front-end
    // This is where the action happens
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if(! empty($title))
            echo str_replace('fa-bars','fa-bar-chart',$args['before_title']) . $title . $args['after_title'];
        // This is where you run the code and display the output
        $Currency = $instance['Currency'];
        $result = get_transient( 'gold_price' );
        if ( false == $result ) {
            $res = file_get_contents("http://download.finance.yahoo.com/d/quotes.csv?s=xau$Currency=X&f=sl1d1t1ba&e=.csv");
            $res = explode(',',$res);
            $gold_price = $res[1];
            $gold_price = ($gold_price/31.1)/24;
            $data = array('ounce'=>number_format($res[1],3),'24'=>number_format($gold_price*24,3),'22'=>number_format($gold_price*22,3),'21'=>number_format($gold_price*21,3),'18'=>number_format($gold_price*18,3),'14'=>number_format($gold_price*14,3),'pound'=>number_format($gold_price*21,3)*8,'Currency_title'=>$instance['Currency_title']);
            set_transient( 'gold_price', $data ,1800);
        }else{
            $data = $result;
        }
        require 'gold-price-view.php';
        echo $args['after_widget'];
    }
    // Widget Backend
    public function form($instance)
    {
        if(isset($instance['title']))
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __('Gold Price', 'gold-price');
        }
        if(isset($instance['Currency']))
        {
            $Currency = $instance['Currency'];
        }
        else
        {
            $Currency = 'usd';
        }
        if(isset($instance['Currency_title']))
        {
            $Currency_title = $instance['Currency_title'];
        }
        else
        {
            $Currency_title = '$';
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title :' , 'gold-price'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'Currency' ); ?>"><?php _e( 'Currency :' , 'gold-price'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'Currency' ); ?>" name="<?php echo $this->get_field_name( 'Currency' ); ?>" type="text" value="<?php echo esc_attr( $Currency ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'Currency_title' ); ?>"><?php _e( 'Currency Symbol :' , 'gold-price'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'Currency_title' ); ?>" name="<?php echo $this->get_field_name( 'Currency_title' ); ?>" type="text" value="<?php echo esc_attr( $Currency_title ); ?>" />
        </p>
        <?php 
    }
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        delete_transient( 'gold_price' );
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['Currency'] = (! empty($new_instance['Currency'])) ? strip_tags($new_instance['Currency']) : '';
        $instance['Currency_title'] = (! empty($new_instance['Currency_title'])) ? strip_tags($new_instance['Currency_title']) : '';
        return $instance;
    }
} // Class gold_price ends here
// Register and load the widget
function gold_price_load_widget()
{
    register_widget('gold_price');
}
add_action('widgets_init', 'gold_price_load_widget');

function gold_price_style() {
	wp_enqueue_style( 'gold_price-style', plugins_url( 'css/style.css', dirname(__FILE__) ));
}

add_action( 'wp_enqueue_scripts', 'gold_price_style' );
