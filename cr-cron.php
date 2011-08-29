<?php
set_time_limit(60*60);
require_once '../../../wp-load.php';
global $wpdb,$wpCommentReplace;
$bad_keys = get_option('wpcr_bad_keys');
$url_user = get_option('wpcr_url_user');
$gen_coms = get_option('wpcr_gen_coms');
//var_dump($bad_keys);
//var_dump($url_user);
//var_dump(get_option('wpcr_check_keys'));

$all_coms = $wpdb -> get_results("select comment_ID from {$wpdb->comments} where 1=1", 'ARRAY_N');

foreach( $all_coms as $com):
	if( !$wpCommentReplace -> exists_in_table( $com[0] ) ):
	$update= true;
	$comment = $wpdb->get_results("select * from $wpdb->comments where comment_ID='{$com[0]}'");
	//var_dump($comment[0] -> comment_content);
	//var_dump( stripos($comment[0] -> comment_content, $key) );
	//initiating common vars
	$comment_author_IP = $comment[0] -> comment_author_IP;
	$rand_usr_url = $url_user[rand(0,count($url_user)-1)];					
	$update_author = $wpCommentReplace -> generate_author( $rand_usr_url );					
	$update_author_url = $wpCommentReplace -> generate_author_url ( $rand_usr_url) ;
	$update_comment = $wpCommentReplace -> generate_from_spin( $gen_coms[rand(0,count($gen_coms)-1)]);
		if($bad_keys = get_option('wpcr_bad_keys'))
			foreach($bad_keys as $key):
				$decide = false;

				if(stripos($comment[0] -> comment_content, $key) !== false){
					$decide = true;
					
				}
				if( get_option('wpcr_check_keys') == 'off' ) $decide = true;
				if($decide){
						$update_array = array(
						'comment_ID' => $com[0],
						'comment_author' => $update_author,
						'comment_author_url' => $update_author_url,
						'comment_content' => $update_comment,
						'comment_author_IP' => $comment_author_IP					
					);
					
					$res = wp_update_comment($update_array);
					//var_dump($res);
					if($res){
					
						$wpdb->query("insert into wp_replaced_comments (replaced_id) values('{$com[0]}')");
						$update = false;						
						break;
					}
					
				}
			
				
			endforeach;
			if($update){
			$update_array = array(
			'comment_ID' => $com[0],
			'comment_author' => $update_author,
			'comment_author_url' => $update_author_url,
			'comment_author_IP' => $comment_author_IP					
			);
					$res = wp_update_comment($update_array);
					//var_dump($res);
					if($res){					
						$wpdb->query("insert into wp_replaced_comments (replaced_id) values('{$com[0]}')");			
					}
				
			}
			
	endif;

endforeach;
