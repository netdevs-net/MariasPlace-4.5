<?php
	$my_postid = $_POST['teamID'];
    $post   = get_post( $my_postid );
    $post_thumbnail = get_the_post_thumbnail($my_postid);
    $post_title = get_the_title($my_postid);
    echo $post_content = $post->post_content;
?>