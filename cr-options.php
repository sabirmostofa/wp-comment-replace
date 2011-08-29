<?php
global $wpdb,$wpCommentReplace;
if(isset($_POST['final_submit'])):
	
	extract($_POST);
	//var_dump( $wpCommentReplace -> process_badkeys( $bad_keys) );
	//var_dump( $wpCommentReplace -> process_url_users( $url_user) );
	//$wpCommentReplace -> process_gen_comments( $gen_coms) );
	 $bad_keys = $wpCommentReplace -> process_badkeys( $bad_keys) ;
	$url_user = $wpCommentReplace -> process_url_users( $url_user) ;
	 $gen_coms = $wpCommentReplace -> process_gen_comments( $gen_coms) ;
	 $check = $wpCommentReplace -> check_comments( $gen_coms) ;
	 
	 update_option('wpcr_bad_keys', $bad_keys);
	 update_option('wpcr_url_user', $url_user);
	 if( $check['error'] ==0 )
	 update_option('wpcr_gen_coms', $check['data']);
	 else
		$show_error= 1;
		
	if(isset($_POST['check_keys']) && $_POST['check_keys'] == 'on')
		update_option('wpcr_check_keys', $_POST['check_keys']);
	else 		
		update_option('wpcr_check_keys', 'off');
	
	//update_option();
	//var_dump(get_option('wpcr_check_keys'));
endif;
$bad_keys = get_option('wpcr_bad_keys');
$url_user = get_option('wpcr_url_user');
//var_dump(get_option('wpcr_url_user'));
$gen_coms = ( isset($show_error) )? $check['data']:get_option('wpcr_gen_coms');

if(get_option('wpcr_check_keys') === false ) update_option('wpcr_check_keys', 'on');
$to_check=(get_option('wpcr_check_keys') == 'on')? 'checked="checked"' : '';

if( isset($show_error) )echo '<div class="error">Data was not saved. Curlay Brackets doesn\'t match</div>';
?>
<div class='wrap'>
<form action='' method='post'>
Banned Keywords(one per line):
<a href='#' class='show-area'>Show/Hide</a>
<br/>
<textarea style="display:none;width:50%;height:250px;" name='bad_keys'>
<?php
if($bad_keys)
foreach($bad_keys as $val)
	echo stripslashes($val)."\n";
?>
</textarea>
<br/>
<br/>

Good Url##Good Username(one per line):
<a href='#' class='show-area'>Show/Hide</a>
<br/>
<textarea style="display:none;width:50%;height:250px;" name='url_user'>
<?php
if($url_user)
foreach($url_user as $val)
	echo stripslashes( $val )."\n";
?>
</textarea>
<br/>
<br/>

Generic Comments(separated by Double Hash):
<a href='#' class='show-area'>Show/Hide</a>
<br/>
<textarea style="display:none;width:50%;height:250px;" name='gen_coms'>
<?php
if($gen_coms)
foreach($gen_coms as $val)
	echo stripslashes($val)."\n##\n";
?>

</textarea>
<br/>
<br/>
<input type='checkbox' <?php echo $to_check; ?>" name='check_keys'/>
Check The bad keywords list
<input class='button-primary' type='submit' name='final_submit' value='Submit'/>
</form>

</div>

