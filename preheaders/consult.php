<?php

global $current_user, $pmpro_consult;

if ( $current_user->ID ) {
	$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

//get consult from DB
if ( ! empty( $_REQUEST['consult'] ) ) {
	$consult_code = sanitize_text_field( $_REQUEST['consult'] );
} else {
	$consult_code = NULL;
}

// Redirect non-user to the login page; pass the Consult page as the redirect_to query arg.
if ( ! is_user_logged_in() ) {
	if ( ! empty( $consult_code ) ) {
		$consult_url = add_query_arg( 'consult', $consult_code, pmpro_url( 'consult' ) );
	} else {
		$consult_url = pmpro_url( 'consult' );
	}
	wp_redirect( add_query_arg( 'redirect_to', urlencode( $consult_url ), wp_login_url() ) );
	exit;
}
