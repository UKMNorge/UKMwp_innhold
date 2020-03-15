<?php

use UKMNorge\Wordpress\Nyhet;

require_once('UKM/Autoloader.php');
require_once('UKM/mail.class.php');

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if( isset( $_POST['comment'] ) ) {
        global $current_user;
		$current_user_name = empty( $current_user->data->user_nicename ) ? $current_user->data->user_login : $current_user->data->user_nicename;
    
		$news = new Nyhet( $_POST['blog_id'], $_POST['post_id'] );
		$res = $news->doComment( $current_user->ID, $current_user_name, $_POST['comment'] );

        $melding = $_POST['comment'] .
            "\r\n\r\n".
            '@'. $user['username'] .' har kommentert på '. $_POST['post_title'];
        $sendtoall = strpos( $_POST['comment'], '@channel' ) !== false;

		require_once('UKM/mail.class.php');
		$epost = new UKMmail();
		$epost->subject('Ny kommentar fra '. $current_user_name .' på '. $_POST['post_title'])
			->to('support@ukm.no')
            ->setFrom('arrangorsystemet@ukm.no', 'Arrangørsystemet')
            ->setReplyTo('support@ukm.no', 'UKM Norge support');
        
        $gotMentions = false;
        $mentionList = '';
        foreach( $news->getCommenters() as $user ) {
            if( $sendtoall || strpos( $_POST['comment'], '@'.$user['username'] ) !== false ) {
                $userdata = get_userdata( $user['id'] );
                $epost->addBlindcopy( $userdata->user_email, $user['name'] );
                $gotMentions = true;
                
                $mentionList = $userdata->user_nicename .', ';
            }
        }

        if( $gotMentions ) {
            $mentionList = rtrim( $mentionList, ', ' );
            $melding .= '<p>&nbsp;</p>'.
                '<hr />'.
                '<p>E-postvarsel er sendt til '. $mentionList .'</p>';
        }
        $epost->text( $melding );

        if( UKM_HOSTNAME != 'ukm.dev') {
            $epost->ok();
        }
    }
}

if( isset( $_GET['comment'] ) && isset( $_GET['action'] ) ) {
	global $current_user;
	$current_user_id = $current_user->ID;

	switch( $_GET['action'] ) {
		case 'delete':
			$news = new Nyhet( $_GET['blog_id'], $_GET['post'] );
			$res = $news->deleteComment( $_GET['comment'], $current_user_id );

			if( $res ) {
				echo '<div class="alert alert-success" style="margin-top: 2em;">Kommentaren er slettet</div>';
			} else {
				echo '<div class="alert alert-danger" style="margin-top: 2em;">Beklager, klarte ikke å slette kommentaren. <a href="mailto:support@ukm.no">Kontakt UKM Norge</a>.</div>';
			}
		break;
	}
}