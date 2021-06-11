<?php

global $current_user, $pmpro_requestnewrefund;

if ( $current_user->ID ) {
	$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

//get requestnewrefund from DB
if ( ! empty( $_REQUEST['requestnewrefund'] ) ) {
	$requestnewrefund_code = sanitize_text_field( $_REQUEST['requestnewrefund'] );
} else {
	$requestnewrefund_code = NULL;
}

// Redirect non-user to the login page; pass the requestnewrefund page as the redirect_to query arg.
if ( ! is_user_logged_in() ) {
	if ( ! empty( $requestnewrefund_code ) ) {
		$requestnewrefund_url = add_query_arg( 'requestnewrefund', $requestnewrefund_code, pmpro_url( 'requestnewrefund' ) );
	} else {
		$requestnewrefund_url = pmpro_url( 'requestnewrefund' );
	}
	wp_redirect( add_query_arg( 'redirect_to', urlencode( $requestnewrefund_url ), wp_login_url() ) );
	exit;
}
