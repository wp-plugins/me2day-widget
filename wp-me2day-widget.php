<?php
/*
Plugin Name: me2day widget
Plugin URI: http://ssamture.net/projects/
Description: me2day widget to your own can be registered.
Author: MinHyeong Lim(ssamture)
Author URI: http://ssamture.net
Version: 1.1
*/
class Ssamture_Me2dayWidget extends WP_Widget{
	function Ssamture_Me2dayWidget(){
		parent::__construct( false, '미투데이');
	}
	function widget( $args, $instance ){
		extract($args);
      	echo $before_widget;
		$title =  "<a href='http://me2day.net/{$instance['me2dayID']}'><img src='http://static1.me2day.com/images/new_sub/img_me2_logo_btn_7.gif?1326950715' style='float:left;margin-right:5px;'/>{$instance['me2dayID']}</a>";
		echo $before_title . $title . $after_title;
		$me2dayRSS = "http://me2day.net/{$instance['me2dayID']}/rss";
		$body = wp_remote_retrieve_body(wp_remote_get($me2dayRSS));
		
		$xmldata = self::get_me2day($body);
		
		$me2daycount =  sizeof($xmldata->channel->item);
		$maxcount = $instance['me2dayCount'];
		if($me2daycount > $maxcount)
			$me2daycount = $maxcount;
		echo '<ul>';
		for($i=0; $i<$me2daycount; $i++){
			$regdate = date('Y/m/d',strtotime($xmldata->channel->item[$i]->pubDate)); 
			echo "<li><img src='http://static1.me2day.com/images/new_sub/img_me2_logo_btn_19.gif?1326950715' style='float:left;margin-right:5px;'/>{$xmldata->channel->item[$i]->title} <br/>-$regdate</li>"; 
		}
		echo '</ul>';
		echo $after_widget;
		
	}
	function get_me2day($body = null){
		if(empty($body)){
			return false;
		}else{
			$response = simplexml_load_string($body);
			return $response;
		}
	}
	
	function update($new_instance, $old_instance){
		$old_instance['me2dayID'] = strip_tags($new_instance['me2dayID']);
      	$old_instance['me2dayCount'] = strip_tags($new_instance['me2dayCount']);
      	return $old_instance;
    }
	function form($instance) {
		$field_id = $this->get_field_id('me2dayID');
		$field_name = $this->get_field_name('me2dayID');

		$widget_types = array('pageviews-sparkline' => 'Pageviews - Sparkline',
                            'pageviews-text' => 'Pageviews - Text');

?>
      <p>
        <label for="<?php echo $field_id; ?>">
          미투데이 ID: 
          <input type="text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $instance['me2dayID'];?>"/>
          </select>
        </label>
      </p>
<?php
		$field_id = $this->get_field_id('me2dayCount');
		$field_name = $this->get_field_name('me2dayCount');
?>
      <p>
        <label for="<?php echo $field_id; ?>">
          표시할 갯수: 
          <input type="text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $instance['me2dayCount'];?>"/>
          <br/>
          ※최대 20개까지
          </select>
        </label>
      </p>
<?php
    }
}
function ssamture_me2day_register_widgets(){
	register_widget( 'Ssamture_Me2dayWidget' );
}
add_action( 'widgets_init', 'ssamture_me2day_register_widgets');
