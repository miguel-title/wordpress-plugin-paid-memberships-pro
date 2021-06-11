<?php

global $current_user, $pmpro_refund;

if ( $current_user->ID ) {
	$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

//get refund from DB
if ( ! empty( $_REQUEST['refund'] ) ) {
	$refund_code = sanitize_text_field( $_REQUEST['refund'] );
} else {
	$refund_code = NULL;
}

// Redirect non-user to the login page; pass the Refund page as the redirect_to query arg.
if ( ! is_user_logged_in() ) {
	if ( ! empty( $refund_code ) ) {
		$refund_url = add_query_arg( 'refund', $refund_code, pmpro_url( 'refund' ) );
	} else {
		$refund_url = pmpro_url( 'refund' );
	}
	wp_redirect( add_query_arg( 'redirect_to', urlencode( $refund_url ), wp_login_url() ) );
	exit;
}
