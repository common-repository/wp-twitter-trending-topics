<?php
/*
Plugin name: Wordpress Twitter Trending Topics
Plugin URI: http://www.laliamos.com/wordpress-twitter-trending-topics-plugin/
Description: Un simple widget para mostrar los trending topics de la red social twitter, por regi&oacute;n.
Version: 1.3
Author: Marc C. G.
Author URI: http://www.laliamos.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=admin%40laliamos%2ecom&lc=ES&item_name=Gracias por donar!&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHostedGuest
*/

add_action( 'widgets_init', 'TwitterTrendsWidgetInit' );
function TwitterTrendsWidgetInit() {
  register_widget( 'TwitterTrendsWidget' );
}


class TwitterTrendsWidget extends WP_Widget {
  function TwitterTrendsWidget() {
    parent::WP_Widget( false, $region = 'Twitter Trending Topics' );
  }
    public function __construct() {
      parent::__construct(
        'twittertrendswidget', // Base ID
        'TwitterTrendsWidget', // Name
        array( 'description' => __( 'Twitter Trending Topics', 'text_domain' ), ) // Args
      );
    } // End constructor

  function widget( $args, $instance ) {
    extract( $args );    
    
    $region = apply_filters( 'widget_region', $instance['region'] ); // Selecciona la regi&oacute;n (Ej. Valencia )
    $expiration = apply_filters( 'widget_expiration', $instance['expiration'] ); // Catch time 
    $display = apply_filters( 'widget_display', $instance['display'] ); // No trends to disply 
    
    echo $before_widget;
    
 ?>
<?php
$vector = array(
1 => 'Twitter WordPress Plugin',
2 => 'Twitter Trending Topics',
);
$numero = rand(1,2);
 ?>
<?php
$vector2 = array(
1 => 'Twitter WordPress Plugin',
2 => 'Twitter Trending Topics',
);
$numero2 = rand(1,2);
?>
    <div class="my_textbox">
<div style="text-align:center;"><a href="http://www.laliamos.com/wordpress-twitter-trending-topics-plugin/" title="<?php echo $vector[$numero]; ?>" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/wp-twitter-trending-topics/img/twitter_bird.png" alt="<?php echo $vector2[$numero2]; ?>" /></a></div>
      <?php
      $trends = twitter_trends($region,$expiration);
      echo '<ol>';
      for ($i=0; $i < $display; $i++){ 
        echo '<li><a href='.$trends[0]['trends'][$i]['url'].' target="_blank">'.$trends[0]['trends'][$i]['name'].'</a></li>';
      }
      echo '</ol>';       
      ?>
    </div>

     <?php
       echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {

    $instance = array();

 
    $instance['region'] = strip_tags( $new_instance['region'] );
    $instance['expiration'] = strip_tags( $new_instance['expiration'] );
    $instance['display'] = strip_tags( $new_instance['display'] );
    
    delete_transient( 'twitter_trends' );
    return $instance;    
  }

  function form( $instance ) {

    $region = esc_attr( $instance['region'] );
    $expiration = esc_attr( $instance['expiration'] );
    $display = esc_attr( $instance['display'] );
    ?>

    
    <p>
      <label for="<?php echo $this->get_field_id( 'region' ); ?>"><?php _e( 'Seleciona Regi&oacute;n:' ); ?>
      <select class="widefat" name="<?php echo $this->get_field_name( 'region' ); ?>">
        <option value="23424950" <?=$region == '23424950' ? ' selected="selected"' : '';?>>Espa&ntilde;a</option>
        <option value="395272" <?=$region == '395272' ? ' selected="selected"' : '';?>>Valencia</option>
        <option value="753692" <?=$region == '753692' ? ' selected="selected"' : '';?>>Barcelona</option>
        <option value="766273" <?=$region == '766273' ? ' selected="selected"' : '';?>>Madrid</option>
      </select>  
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'expiration' ); ?>"><?php _e( 'Refrescar :' ); ?>
      <select class="widefat" name="<?php echo $this->get_field_name( 'expiration' ); ?>">
        <option value="1" <?=$expiration == '1' ? ' selected="selected"' : '';?>>Cada Hora</option>
        <option value="12" <?=$expiration == '12' ? ' selected="selected"' : '';?>>Dos veces al d&iacute;a</option>
        <option value="24" <?=$expiration == '24' ? ' selected="selected"' : '';?>>Cada d&iacute;a</option>
        <option value="168" <?=$expiration == '168' ? ' selected="selected"' : '';?>>Cada semana</option>
        <option value="720" <?=$expiration == '720' ? ' selected="selected"' : '';?>>Cada mes</option>
      </select>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display Number of Trends :' ); ?>
      <input class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" type="text" value="<?php echo $display; ?>" />
      </label>
    </p>
    <p>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="hosted_button_id" value="KBRBCP9YD9EEL" /> <input type="image" name="submit" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donate_SM.gif" alt="PayPal. La forma r&aacute;pida y segura de pagar en Internet." /> <img src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /><br /><a href="http://www.laliamos.com" title="seo valencia" target="_blank">Seo Valencia</a></form>
    </p>
    
    <?php
  }
} // class TwitterTrendsWidget

function twitter_trends($region,$expiration){
        
        $count = get_transient('twitter_trends');
    if ($count !== false) return $count;
         $count = 0;

         $url = 'https://api.twitter.com/1/trends/'.$region.'.json?count=50';
         $dataOrig = file_get_contents($url, true); //getting the file content
   if (is_wp_error($dataOrig)) {
         return 'Error while fetching data from Twitter API!';
   }else{
        $count = json_decode($dataOrig, true); //getting the file content as array
        $count = $count;         
        }

set_transient('twitter_trends', $count, 60*60*$expiration); // set cache
return $count;
}
?>
