<?php   

/*
Plugin Name: WP-Comment-Replace
Plugin URI: http://sabirul-mostofa.blogspot.com
Description: Replace Comments with User defined ones
Version: 1.0
Author: Sabirul Mostofa
Author URI: http://sabirul-mostofa.blogspot.com
*/


$wpCommentReplace = new wpCommentReplace();
if(isset($wpCommentReplace)) {
	add_action('admin_menu', array($wpCommentReplace,'CreateMenu'),50);	
}
   
class wpCommentReplace{
	
	function __construct(){

		add_action('admin_enqueue_scripts' , array($this,'admin_scripts'));
		//add_action('wp_enqueue_scripts' , array($this,'front_scripts'));	
		add_action( 'wp_ajax_myajax-submit', array($this,'ajax_handle' ));

		
		
		register_activation_hook(__FILE__, array($this, 'create_table'));
		
		}
		
	function admin_scripts(){
		if(preg_match('/wpCommentReplace/',$_SERVER['REQUEST_URI'])){					
			wp_enqueue_script('jquery');
			wp_enqueue_script('kt_admin_script',plugins_url('/' , __FILE__).'js/script_admin.js');
			wp_register_style('kt_admin_css', plugins_url('/' , __FILE__).'css/style_admin.css', false, '1.0.0');
			wp_enqueue_style('kt_admin_css');
	
		}	
	}
	
	
			
		

	function CreateMenu(){
		add_submenu_page('edit-comments.php','Replace Comments','Replace Comments','activate_plugins','wpCommentReplace',array($this,'OptionsPage'));
	
	}
	function process_badkeys($data){
		$data = array_map( create_function('$a','return trim($a);'), explode("\n", stripslashes( $data) ) );
		return $this ->del_empty($data);
		}
		
	function process_url_users($data){
		$data =  array_map( create_function('$a','return trim($a);'), explode("\n", stripslashes( $data)  ) );
		return $this ->del_empty($data);
		// will use later
		$to_return = array(  'url' =>array(), 'username' => array() );
		foreach( $data as $single ){
			$d = explode( '##', $single );
			if(count($d) == 2){
				$to_return['url'][] = $d[0];
				$to_return['username'][] = $d[1];
			}
				
		}
			return $to_return;
	}
	
	function process_gen_comments($data){
	$data = array_map( create_function('$a','return trim($a,"\n\r ");'), explode('##', stripslashes( $data) ) );
	return $this ->del_empty($data);
	}
	
	function del_empty($data){
		foreach($data as $key => $value )
		if( !preg_match('/\S/', $value) )
			unset($data[$key]);
		return $data;
		
	}
	
	function check_comments($data){
		$error = 0;
		foreach( $data as $key=> $value ){
			if( preg_match_all('/\{/',$value, $match) == preg_match_all('/\}/',$value, $match) )
				continue;
			else
				$error++;

			
			}
			return array('data' => $data, 'error' => $error);
		
		}

	
				

		
		
	function create_table(){	
		$sql = "CREATE TABLE IF NOT EXISTS `wp_replaced_comments` (
		`id` int unsigned NOT NULL AUTO_INCREMENT, 
		`replaced_id` int unsigned  NOT NULL,
		PRIMARY KEY (`id`),
		key `repalced`(`replaced_id`)	
		)";
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);


	}	
		
		
/*
 * Options Page
 * 
 * */		
	function OptionsPage( ){
		require_once 'cr-options.php';
	}//endof options page
	
	function generate_from_spin($comment){
		if(stripos($comment,'{') === false)return $comment;
		return preg_replace_callback('/\{.*?\}/', create_function('$match','$a = explode("|",trim($match[0],"{}"));return $a[rand(0,count($a)-1)];'),$comment);	
	}
	function generate_author($data){
		$data = explode('##', $data);
		return $this-> generate_from_spin( $data[1]);		
		}
		
	function generate_author_url($data){
		$data = explode('##', $data);
		return $data[0];		
		}
       
     
   
   
   
   //Crude functions
	function exists_in_table($id){
		global $wpdb;
		//$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		$result = $wpdb->get_results( "SELECT id FROM wp_replaced_comments where  replaced_id='$id'" );
		if(empty($result))
			return false;

		return true;
	}
	
		function exists_in_table_double($member,$key){
		global $wpdb;
		//$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		$result = $wpdb->get_results( "SELECT id FROM wp_kt_members where  member_id='$member' and key_id='$key'" );
		if(empty($result))
			return false;
		else 
			return true;
	}
	
	


}


?>
