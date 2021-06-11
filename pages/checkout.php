<?php
	global $gateway, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $pmpro_levels, $tospage, $pmpro_show_discount_code, $pmpro_error_fields;
	global $discount_code, $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth,$ExpirationYear, $bcpf, $bpetname, $bpetbirthdate, $bpetsex, $bpetspecies, $bpetbreed;

	/**
	 * Filter to set if PMPro uses email or text as the type for email field inputs.
	 *
	 * @since 1.8.4.5
	 *
	 * @param bool $use_email_type, true to use email type, false to use text type
	 */
	$pmpro_email_field_type = apply_filters('pmpro_email_field_type', true);

	// Set the wrapping class for the checkout div based on the default gateway;
	$default_gateway = pmpro_getOption( 'gateway' );
	if ( empty( $default_gateway ) ) {
		$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-none';
	} else {
		$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-' . $default_gateway;
	}
?>

<?php do_action('pmpro_checkout_before_form'); ?>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.60/inputmask/jquery.inputmask.js"></script>

<style>
	.checkout_page.hidden{
		display:none;
	}

	#pmpro_add_planpet{
		margin-bottom:1em;
		float:left;
	}

	.table_data{
		border:none;
		font-size: 16px;
		text-align:center;
		width:100%;
	}
	
</style>

<?php if ($_REQUEST['submit-checkout']){?>
	<input type='hidden' id='issubmit' value='true'>
<?php }else{ ?>
	<input type='hidden' id='issubmit' value='false'>
<?php } ?>

<div id="pmpro_level-<?php echo $pmpro_level->id; ?>" class="<?php echo pmpro_get_element_class( $pmpro_checkout_gateway_class, 'pmpro_level-' . $pmpro_level->id ); ?>">
<form id="pmpro_form" class="<?php echo pmpro_get_element_class( 'pmpro_form' ); ?>" action="<?php if(!empty($_REQUEST['review'])) echo pmpro_url("checkout", "?level=" . $pmpro_level->id); ?>" method="post">

	<input type="hidden" id="level" name="level" value="<?php echo esc_attr($pmpro_level->id) ?>" />
	<input type="hidden" id="checkjavascript" name="checkjavascript" value="1" />
	<?php if ($discount_code && $pmpro_review) { ?>
		<input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="hidden" size="20" value="<?php echo esc_attr($discount_code) ?>" />
	<?php } ?>

	<div id="pmpro_message_error" class="pmpro_message pmpro_error" style='display:none;'>
		Por favor complete todos os campos obrigatórios.
	</div>

	<?php if($pmpro_msg) { ?>
		<div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>">
			<?php echo apply_filters( 'pmpro_checkout_message', $pmpro_msg, $pmpro_msgt ) ?>
		</div>
	<?php } else { ?>
		<div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></div>
	<?php } ?>

	<?php if($pmpro_review) { ?>
		<p><?php _e('Almost done. Review the membership information and pricing below then <strong>click the "Complete Payment" button</strong> to finish your order.', 'paid-memberships-pro' );?></p>
	<?php } ?>
	<input type="hidden" value="checkout_first_part" id='current_checkout_page'>
	<?php
		$include_pricing_fields = apply_filters( 'pmpro_include_pricing_fields', true );
		if ( $include_pricing_fields ) {
		?>
		<div id="pmpro_pricing_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_pricing_fields' ); ?> checkout_first_part checkout_page">
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Membership Level', 'paid-memberships-pro' );?></span>
				<?php if(count($pmpro_levels) > 1) { ?><span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><a href="<?php echo pmpro_url("levels"); ?>"><?php _e('change', 'paid-memberships-pro' );?></a></span><?php } ?>
			</h3>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<p>
					<?php printf(__('You have selected the <strong>%s</strong> membership level.', 'paid-memberships-pro' ), $pmpro_level->name);?>
				</p>

				<?php
					/**
					 * All devs to filter the level description at checkout.
					 * We also have a function in includes/filters.php that applies the the_content filters to this description.
					 * @param string $description The level description.
					 * @param object $pmpro_level The PMPro Level object.
					 */
					$level_description = apply_filters('pmpro_level_description', $pmpro_level->description, $pmpro_level);
					if(!empty($level_description))
						echo $level_description;
				?>

				<div id="pmpro_level_cost">
					<?php if($discount_code && pmpro_checkDiscountCode($discount_code)) { ?>
						<?php printf(__('<p class="' . pmpro_get_element_class( 'pmpro_level_discount_applied' ) . '">The <strong>%s</strong> code has been applied to your order.</p>', 'paid-memberships-pro' ), $discount_code);?>
					<?php } ?>
					<?php echo wpautop(pmpro_getLevelCost($pmpro_level)); ?>
					<?php echo wpautop(pmpro_getLevelExpiration($pmpro_level)); ?>
				</div>

				<?php do_action("pmpro_checkout_after_level_cost"); ?>

				<?php if($pmpro_show_discount_code) { ?>
					<?php if($discount_code && !$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><a id="other_discount_code_a" href="#discount_code"><?php _e('Click here to change your discount code.', 'paid-memberships-pro' );?></a></p>
					<?php } elseif(!$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><?php _e('Do you have a discount code?', 'paid-memberships-pro' );?> <a id="other_discount_code_a" href="#discount_code"><?php _e('Click here to enter your discount code', 'paid-memberships-pro' );?></a>.</p>
					<?php } elseif($pmpro_review && $discount_code) { ?>
						<p><strong><?php _e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo $discount_code?></p>
					<?php } ?>
				<?php } ?>

				<?php if($pmpro_show_discount_code) { ?>
				<div id="other_discount_code_tr" style="display: none;">
					<label for="other_discount_code"><?php _e('Discount Code', 'paid-memberships-pro' );?></label>
					<input id="other_discount_code" name="other_discount_code" type="text" class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'other_discount_code' ); ?>" size="20" value="<?php echo esc_attr($discount_code); ?>" />
					<input type="button" name="other_discount_code_button" id="other_discount_code_button" value="<?php _e('Apply', 'paid-memberships-pro' );?>" />
				</div>
				<?php } ?>
			</div> <!-- end pmpro_checkout-fields -->
		</div> <!-- end pmpro_pricing_fields -->
		<?php
		} // if ( $include_pricing_fields )
	?>

	<?php
		do_action('pmpro_checkout_after_pricing_fields');
	?>

	<?php if(!$skip_account_fields && !$pmpro_review) { ?>

	<?php 
		// Get discount code from URL parameter, so if the user logs in it will keep it applied.
		$discount_code_link = !empty( $discount_code) ? '&discount_code=' . $discount_code : ''; 
	?>
	<input type='hidden' id='isUserLogin' value='1'>
	<div id="pmpro_user_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_user_fields' );?> checkout_first_part checkout_page">
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>">Dados Pessoais</span>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php _e('Already have an account?', 'paid-memberships-pro' );?> <a href="<?php echo wp_login_url( apply_filters( 'pmpro_checkout_login_redirect', pmpro_url("checkout", "?level=" . $pmpro_level->id . $discount_code_link) ) ); ?>"><?php _e('Log in here', 'paid-memberships-pro' );?></a></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bfirstname', 'pmpro_checkout-field-bfirstname' ); ?>">
				<label for="bfirstname"><?php _e('First Name', 'paid-memberships-pro' );?></label>
				<input id="bfirstname" name="bfirstname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bfirstname' ); ?>" size="30" value="<?php echo esc_attr($bfirstname); ?>" />
			</div> <!-- end pmpro_checkout-field-bfirstname -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-blastname', 'pmpro_checkout-field-blastname' ); ?>">
				<label for="blastname"><?php _e('Last Name', 'paid-memberships-pro' );?></label>
				<input id="blastname" name="blastname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'blastname' ); ?>" size="30" value="<?php echo esc_attr($blastname); ?>" />
			</div> <!-- end pmpro_checkout-field-blastname -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-cpf', 'pmpro_checkout-field-cpf' ); ?>">
				<label for="bcpf">CPF</label>
				<input id="bcpf" name="bcpf" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bcpf' ); ?>" size="30" value="<?php echo esc_attr($bcpf); ?>" />
			</div>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bphone', 'pmpro_checkout-field-bphone' ); ?>">
				<label for="bphone"><?php _e('Phone', 'paid-memberships-pro' );?></label>
				<input id="bphone" name="bphone" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bphone' ); ?>" size="30" value="<?php echo esc_attr(formatPhone($bphone)); ?>" />
			</div>
			
			<?php
				do_action('pmpro_checkout_after_username');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password', 'pmpro_checkout-field-password' ); ?>">
				<label for="password"><?php _e('Password', 'paid-memberships-pro' );?></label>
				<input id="password" name="password" type="password" class="<?php echo pmpro_get_element_class( 'input', 'password' ); ?>" size="30" value="<?php echo esc_attr($password); ?>" placeholder="Insira 7 caracteres ou mais." minlength="7"/>
			</div> <!-- end pmpro_checkout-field-password -->

			<?php
				$pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);
				if($pmpro_checkout_confirm_password) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password2', 'pmpro_checkout-field-password2' ); ?>">
						<label for="password2"><?php _e('Confirm Password', 'paid-memberships-pro' );?></label>
						<input id="password2" name="password2" type="password" class="<?php echo pmpro_get_element_class( 'input', 'password2' ); ?>" size="30" value="<?php echo esc_attr($password2); ?>" placeholder="Insira 7 caracteres ou mais." minlength="7"/>
					</div> <!-- end pmpro_checkout-field-password2 -->
				<?php } else { ?>
					<input type="hidden" name="password2_copy" value="1" />
				<?php }
			?>

			<?php
				do_action('pmpro_checkout_after_password');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bemail', 'pmpro_checkout-field-bemail' ); ?>">
				<label for="bemail"><?php _e('Email Address', 'paid-memberships-pro' );?></label>
				<input id="bemail" name="bemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bemail' ); ?>" size="30" value="<?php echo esc_attr($bemail); ?>" />
			</div> <!-- end pmpro_checkout-field-bemail -->

			<?php
				$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);
				if($pmpro_checkout_confirm_email) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bconfirmemail', 'pmpro_checkout-field-bconfirmemail' ); ?>">
						<label for="bconfirmemail"><?php _e('Confirm Email Address', 'paid-memberships-pro' );?></label>
						<input id="bconfirmemail" name="bconfirmemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bconfirmemail' ); ?>" size="30" value="<?php echo esc_attr($bconfirmemail); ?>" />
					</div> <!-- end pmpro_checkout-field-bconfirmemail -->
				<?php } else { ?>
					<input type="hidden" name="bconfirmemail_copy" value="1" />
				<?php }
			?>

			<?php
				do_action('pmpro_checkout_after_email');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_hidden' ); ?>">
				<label for="fullname"><?php _e('Full Name', 'paid-memberships-pro' );?></label>
				<input id="fullname" name="fullname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'fullname' ); ?>" size="30" value="" autocomplete="off"/> <strong><?php _e('LEAVE THIS BLANK', 'paid-memberships-pro' );?></strong>
			</div> <!-- end pmpro_hidden -->

		</div>  <!-- end pmpro_checkout-fields -->
	</div> <!-- end pmpro_user_fields -->
	<?php } elseif($current_user->ID && !$pmpro_review) { ?>
		<div id="pmpro_account_loggedin" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_alert', 'pmpro_account_loggedin' ); ?> checkout_first_part checkout_page">
			<?php printf(__('You are logged in as <strong>%s</strong>. If you would like to use a different account for this membership, <a href="%s">log out now</a>.', 'paid-memberships-pro' ), $current_user->user_login, wp_logout_url($_SERVER['REQUEST_URI'])); ?>
		</div> <!-- end pmpro_account_loggedin -->
	<?php } ?>

	<?php
		$pmpro_include_billing_address_fields = apply_filters('pmpro_include_billing_address_fields', true);
		if($pmpro_include_billing_address_fields) { ?>
	<div id="pmpro_billing_pet_detail" class='checkout_third_part checkout_page'>
		<h3>
			<span>Adicione um Pet</span>
		</h3>
		<div class="pmpro_checkout-fields">
			<input type="hidden" id="petid" value="0">
			<div class="pmpro_checkout-field pmpro_checkout-field-petname">
				<label for="petname">Nome do Pet</label>
				<input id="_petname" name="_petname" type="text" class="input pmpro_required" size="30" value="">
				<input type="hidden" id="petname" name="petname" class="input pmpro_required" size="30" value="<?php echo $bpetname ?>">
				<span class="pmpro_asterisk"> 
				</span>
			</div>
			<div class="pmpro_checkout-field pmpro_checkout-field-birthdate">
				<label for="birthdate"><?php _e('Birth date', 'paid-memberships-pro') ?></label>
				<input type="text" id="_birthdate" class="input pmpro_required" size="30" name="_birthdate" placeholder="DD/MM/AAAA" title="Enter a date in this format DD/MM/YYYY" value="">
				<input type="hidden" id="birthdate" class="input pmpro_required" name="birthdate" value="<?php echo $bpetbirthdate ?>">
				<span class="pmpro_asterisk"> 
				</span>
			</div>
			<div class="pmpro_checkout-field pmpro_checkout-field-genre">
				<label for="genre"><?php _e('Genre', 'paid-memberships-pro') ?></label>
				<select name="_petgender" class="pmpro_required" id='_petgender'>
					<option value="Macho" <?php if($bpetsex == 'Macho'){echo 'selected';} ?>>Macho</option>
					<option value="Fêmea" <?php if($bpetsex == 'Fêmea'){echo 'selected';} ?>>Fêmea</option>
				</select>	
				<input type="hidden" name="genre" class="input pmpro_required" id="petgender" value="<?php echo $bpetsex ?>">
				<span class="pmpro_asterisk"> 
				</span>
			</div>
			<div class="pmpro_checkout-field pmpro_checkout-field-species">
				<label for="species"><?php _e('Species', 'paid-memberships-pro') ?></label>
				<select id = '_species' name="_species" class="input pmpro_required" >
					<option value='Canino' <?php if($bpetspecies == 'Canino'){echo 'selected';} ?>>Canino</option>
					<option value='Felino' <?php if($bpetspecies == 'Felino'){echo 'selected';} ?>>Felino</option>
					<option value='Aves' <?php if($bpetspecies == 'Aves'){echo 'selected';} ?>>Aves</option>
				</select>
				<input type="hidden" name="species" class="input pmpro_required" id="species" value="<?php echo $bpetspecies ?>">
				<span class="pmpro_asterisk"> 
				</span>
			</div>
			<div class="pmpro_checkout-field pmpro_checkout-field-breed">
				<label for="breed"><?php _e('Breed', 'paid-memberships-pro') ?></label>
				<input id="_breed" name="_breed" type="text" class="input pmpro_required" size="30" value="">
				<span class="pmpro_asterisk"> 
				</span>
			</div>
			<input type="hidden" name="breed" class="input pmpro_required" id="breed" value="<?php echo $bpetbreed ?>">
		</div>
		<input type="button" id="pmpro_add_planpet" value='+ Adicionar Pet' class='pmpro_btn'>
		<table id="pmpro_planpets_table" class="pmpro_table pmpro_planpets" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>
						Nome do Pet
					</th>
					<th>
						Data de Nascimento
					</th>
					<th>
						Gênero
					</th>
					<th>
						Espécie
					</th>
					<th>
						Raça
					</th>
					<th></th>
				</tr>
			</thead>
			<tbody>	
				<?php 
					if (empty(trim($bpetname))){
						$petdatalength = 0;	
					}else{
						$petdatalength = count(explode(',', $bpetname));
					}
					for ($petindex = 0; $petindex < $petdatalength; $petindex++){
						$petname = explode(',', $bpetname)[$petindex];
						$petbirthdate = explode(',', $bpetbirthdate)[$petindex];
						$petsex = explode(',', $bpetsex)[$petindex];
						$petspecies = explode(',', $bpetspecies)[$petindex];
						$petbreed = explode(',', $bpetbreed)[$petindex];
				?>
				<tr class="petdata" id="pet_<?php echo $petindex?>">
				<td>
					<input class="pmpro_checkout-field table_data petname" id="petname_<?php echo $petindex; ?>" name="petname_<?php echo $petindex; ?>" value="<?php echo $petname; ?>" readonly>
				</td>
				
				<td>
					<input class="pmpro_checkout-field table_data birthdate" id="birthdate_<?php echo $petindex; ?>" name="birthdate_<?php echo $petindex; ?>" value="<?php echo $petbirthdate; ?>" readonly>
				</td>
				
				<td>
					<input class="pmpro_checkout-field table_data petgender" id="petgender_<?php echo $petindex; ?>" name="petgender_<?php echo $petindex; ?>" value="<?php echo $petsex; ?>" readonly>
				</td>
				
				<td>
					<input class="pmpro_checkout-field table_data species" id="species_<?php echo $petindex; ?>" name="species_<?php echo $petindex; ?>" value="<?php echo $petspecies; ?>" readonly>
				</td>
				
				<td>
					<input class="pmpro_checkout-field table_data breed" id="breed_<?php echo $petindex; ?>" name="breed_<?php echo $petindex; ?>" value="<?php echo $petbreed; ?>" readonly>
				</td>

				<td>
					<a type="button" onclick="petEdit(this);" id="petupdate_<?php echo $petindex; ?>" style="cursor:pointer;" >Editar</a><a type="button" id="petdelete_<?php echo $petindex; ?>"  onclick="petDelete(this);" style="cursor:pointer;"> | Excluir</a>
				</td>
				</tr>
				<?php } ?>
				<tr id='noitem_tr'>
					<td valign="top" colspan="6" class="dataTables_empty">Adicione um Pet para visualizar.</td>
				</tr>			
			</tbody>
		</table>
		
	</div>
	<div id="pmpro_billing_address_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_billing_address_fields' ); ?> checkout_second_part checkout_page" <?php if(!$pmpro_requirebilling || apply_filters("pmpro_hide_billing_address_fields", false) ){ ?>style="display: none;"<?php } ?>>
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Billing Address', 'paid-memberships-pro' );?></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-baddress1', 'pmpro_checkout-field-baddress1' ); ?>">
				<label for="baddress1"><?php _e('Address 1', 'paid-memberships-pro' );?></label>
				<input id="baddress1" name="baddress1" type="text" class="<?php echo pmpro_get_element_class( 'input', 'baddress1' ); ?>" size="30" value="<?php echo esc_attr($baddress1); ?>" />
			</div> <!-- end pmpro_checkout-field-baddress1 -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-baddress2', 'pmpro_checkout-field-baddress2' ); ?>">
				<label for="baddress2"><?php _e('Address 2', 'paid-memberships-pro' );?></label>
				<input id="baddress2" name="baddress2" type="text" class="<?php echo pmpro_get_element_class( 'input', 'baddress2' ); ?>" size="30" value="<?php echo esc_attr($baddress2); ?>" />
			</div> <!-- end pmpro_checkout-field-baddress2 -->
			<?php
				$longform_address = apply_filters("pmpro_longform_address", true);
				if($longform_address) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcity', 'pmpro_checkout-field-bcity' ); ?>">
						<label for="bcity"><?php _e('City', 'paid-memberships-pro' );?></label>
						<input id="bcity" name="bcity" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bcity' ); ?>" size="30" value="<?php echo esc_attr($bcity); ?>" />
					</div> <!-- end pmpro_checkout-field-bcity -->
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bstate', 'pmpro_checkout-field-bstate' ); ?>">
						<label for="bstate"><?php _e('State', 'paid-memberships-pro' );?></label>
						<select id="bstate" name="bstate" class="<?php echo pmpro_get_element_class( '', 'bstate' ); ?>">
							<option value="Acre" <?php if($bstate == 'Acre'){echo 'selected';} ?>>Acre</option>
							<option value="Amapá" <?php if($bstate == 'Amapá'){echo 'selected';} ?>>Amapá</option>
							<option value="Amazonas" <?php if($bstate == 'Amazonas'){echo 'selected';} ?>>Amazonas</option>
							<option value="Alagoas" <?php if($bstate == 'Alagoas'){echo 'selected';} ?>>Alagoas</option>
							<option value="Bahia" <?php if($bstate == 'Bahia'){echo 'selected';} ?>>Bahia</option>
							<option value="Ceará" <?php if($bstate == 'Ceará'){echo 'selected';} ?>>Ceará</option>
							<option value="Distrito Federal" <?php if($bstate == 'Distrito Federal'){echo 'selected';} ?>>Distrito Federal</option>
							<option value="Espírito Santo" <?php if($bstate == 'Espírito Santo'){echo 'selected';} ?>>Espírito Santo</option>
							<option value="Goiás" <?php if($bstate == 'Goiás'){echo 'selected';} ?>>Goiás</option>
							<option value="Mato Grosso" <?php if($bstate == '"Mato Grosso'){echo 'selected';} ?>>Mato Grosso</option>
							<option value="Mato Grosso do Sul" <?php if($bstate == 'Mato Grosso do Sul'){echo 'selected';} ?>>Mato Grosso do Sul</option>
							<option value="Minas" <?php if($bstate == 'Minas'){echo 'selected';} ?>>Minas</option>
							<option value="Maranhão" <?php if($bstate == 'Maranhão'){echo 'selected';} ?>>Maranhão</option>
							<option value="Pará" <?php if($bstate == 'Pará'){echo 'selected';} ?>>Pará</option>
							<option value="Paraíba" <?php if($bstate == 'Paraíba'){echo 'selected';} ?>>Paraíba</option>
							<option value="Pernambuco" <?php if($bstate == 'Pernambuco'){echo 'selected';} ?>>Pernambuco</option>
							<option value="Piauí" <?php if($bstate == 'Piauí'){echo 'selected';} ?>>Piauí</option>
							<option value="Paraná" <?php if($bstate == 'Paraná'){echo 'selected';} ?>>Paraná</option>
							<option value="Rio Grande do Norte" <?php if($bstate == 'Rio Grande do Norte'){echo 'selected';} ?>>Rio Grande do Norte</option>
							<option value="Rio Grande do Sul" <?php if($bstate == 'Rio Grande do Sul'){echo 'selected';} ?>>Rio Grande do Sul</option>
							<option value="Rio de Janeiro" <?php if($bstate == 'Rio de Janeiro'){echo 'selected';} ?>>Rio de Janeiro</option>
							<option value="Rondônia" <?php if($bstate == 'Rondônia'){echo 'selected';} ?>>Rondônia</option>
							<option value="Roraima" <?php if($bstate == 'Roraima'){echo 'selected';} ?>>Roraima</option>
							<option value="São Paulo" <?php if($bstate == 'São Paulo'){echo 'selected';} ?>>São Paulo</option>
							<option value="Sergipe" <?php if($bstate == 'Sergipe'){echo 'selected';} ?>>Sergipe</option>
							<option value="Santa Catarina" <?php if($bstate == 'Santa Catarina'){echo 'selected';} ?>>Santa Catarina</option>
							<option value="Tocantins" <?php if($bstate == 'Tocantins'){echo 'selected';} ?>>Tocantins</option>
						</select>
					</div> <!-- end pmpro_checkout-field-bstate -->
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bzipcode', 'pmpro_checkout-field-bzipcode' ); ?>">
						<label for="bzipcode"><?php _e('Postal Code', 'paid-memberships-pro' );?></label>
						<input id="bzipcode" name="bzipcode" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bzipcode' ); ?>" size="30" value="<?php echo esc_attr($bzipcode); ?>"  placeholder="_____-___" />
					</div> <!-- end pmpro_checkout-field-bzipcode -->
				<?php } else { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcity_state_zip', 'pmpro_checkout-field-bcity_state_zip' ); ?>">
						<label for="bcity_state_zip' ); ?>"><?php _e('City, State Zip', 'paid-memberships-pro' );?></label>
						<input id="bcity" name="bcity" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bcity' ); ?>" size="14" value="<?php echo esc_attr($bcity); ?>" />,
						<?php
							$state_dropdowns = apply_filters("pmpro_state_dropdowns", false);
							if($state_dropdowns === true || $state_dropdowns == "names") {
								global $pmpro_states;
								?>
								<select name="bstate" class="<?php echo pmpro_get_element_class( '', 'bstate' ); ?>">
									<option value="">--</option>
									<?php
										foreach($pmpro_states as $ab => $st) { ?>
											<option value="<?php echo esc_attr($ab);?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?php echo $st;?></option>
									<?php } ?>
								</select>
							<?php } elseif($state_dropdowns == "abbreviations") {
								global $pmpro_states_abbreviations;
								?>
								<select name="bstate" class="<?php echo pmpro_get_element_class( '', 'bstate' ); ?>">
									<option value="">--</option>
									<?php
										foreach($pmpro_states_abbreviations as $ab)
										{
									?>
										<option value="<?php echo esc_attr($ab);?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?php echo $ab;?></option>
									<?php } ?>
								</select>
							<?php } else { ?>
								<input id="bstate" name="bstate" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bstate' ); ?>" size="2" value="<?php echo esc_attr($bstate); ?>" />
						<?php } ?>
						<input id="bzipcode" name="bzipcode" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bzipcode' ); ?>" size="5" value="<?php echo esc_attr($bzipcode); ?>" />
					</div> <!-- end pmpro_checkout-field-bcity_state_zip -->
			<?php } ?>

			<?php
				$show_country = apply_filters("pmpro_international_addresses", true);
				if($show_country) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcountry', 'pmpro_checkout-field-bcountry' ); ?>">
						<label for="bcountry"><?php _e('Country', 'paid-memberships-pro' );?></label>
						<select name="bcountry" id="bcountry" class="<?php echo pmpro_get_element_class( '', 'bcountry' ); ?>">
						<?php
							global $pmpro_countries, $pmpro_default_country;
							if(!$bcountry) {
								$bcountry = $pmpro_default_country;
							}
							foreach($pmpro_countries as $abbr => $country) { ?>
								<option value="<?php echo $abbr?>" <?php if($abbr == $bcountry) { ?>selected="selected"<?php } ?>><?php echo $country?></option>
							<?php } ?>
						</select>
					</div> <!-- end pmpro_checkout-field-bcountry -->
				<?php } else { ?>
					<input type="hidden" name="bcountry" value="US" />
				<?php } ?>
			<?php if($skip_account_fields) { ?>
			<?php
				if($current_user->ID) {
					if(!$bemail && $current_user->user_email) {
						$bemail = $current_user->user_email;
					}
					if(!$bconfirmemail && $current_user->user_email) {
						$bconfirmemail = $current_user->user_email;
					}
				}
			?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bemail', 'pmpro_checkout-field-bemail' ); ?>">
				<label for="bemail"><?php _e('Email Address', 'paid-memberships-pro' );?></label>
				<input id="bemail" name="bemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bemail' ); ?>" size="30" value="<?php echo esc_attr($bemail); ?>" />
			</div> <!-- end pmpro_checkout-field-bemail -->
			<?php
				$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);
				if($pmpro_checkout_confirm_email) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bconfirmemail', 'pmpro_checkout-field-bconfirmemail' ); ?>">
						<label for="bconfirmemail"><?php _e('Confirm Email', 'paid-memberships-pro' );?></label>
						<input id="bconfirmemail" name="bconfirmemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bconfirmemail' ); ?>" size="30" value="<?php echo esc_attr($bconfirmemail); ?>" />
					</div> <!-- end pmpro_checkout-field-bconfirmemail -->
				<?php } else { ?>
					<input type="hidden" name="bconfirmemail_copy" value="1" />
				<?php } ?>
			<?php } ?>
		</div> <!-- end pmpro_checkout-fields -->
	</div> <!--end pmpro_billing_address_fields -->
	<?php } ?>

	<?php do_action("pmpro_checkout_after_billing_fields"); ?>

	<?php
		$pmpro_accepted_credit_cards = pmpro_getOption("accepted_credit_cards");
		$pmpro_accepted_credit_cards = explode(",", $pmpro_accepted_credit_cards);
		$pmpro_accepted_credit_cards_string = pmpro_implodeToEnglish($pmpro_accepted_credit_cards);
	?>
	
	<?php
		$pmpro_include_payment_information_fields = apply_filters("pmpro_include_payment_information_fields", true);
		if($pmpro_include_payment_information_fields) { ?>
		<div id="pmpro_payment_information_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_information_fields' ); ?> checkout_forth_part checkout_page" <?php if(!$pmpro_requirebilling || apply_filters("pmpro_hide_payment_information_fields", false) ) { ?>style="display: none;"<?php } ?>>
			<hr />
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Payment Information', 'paid-memberships-pro' );?></span>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php printf(__('We Accept %s', 'paid-memberships-pro' ), $pmpro_accepted_credit_cards_string);?></span>
			</h3>
			<?php $sslseal = pmpro_getOption("sslseal"); ?>
			<?php if(!empty($sslseal)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-display-seal' ); ?>">
			<?php } ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<?php
					$pmpro_include_cardtype_field = apply_filters('pmpro_include_cardtype_field', false);
					if($pmpro_include_cardtype_field) { ?>
						<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-card-type', 'pmpro_payment-card-type' ); ?>">
							<label for="CardType"><?php _e('Card Type', 'paid-memberships-pro' );?></label>
							<select id="CardType" name="CardType" class="<?php echo pmpro_get_element_class( '', 'CardType' ); ?>">
								<?php foreach($pmpro_accepted_credit_cards as $cc) { ?>
									<option value="<?php echo $cc; ?>" <?php if($CardType == $cc) { ?>selected="selected"<?php } ?>><?php echo $cc; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } else { ?>
						<input type="hidden" id="CardType" name="CardType" value="<?php echo esc_attr($CardType);?>" />						
					<?php } ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-account-number', 'pmpro_payment-account-number' ); ?>">
					<label for="AccountNumber"><?php _e('Card Number', 'paid-memberships-pro' );?></label>
					<input id="AccountNumber" name="AccountNumber" class="<?php echo pmpro_get_element_class( 'input', 'AccountNumber' ); ?>" type="text" size="30" value="<?php echo esc_attr($AccountNumber); ?>" data-encrypted-name="number" autocomplete="off" />
				</div>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-expiration', 'pmpro_payment-expiration' ); ?>">
					<label for="ExpirationMonth"><?php _e('Expiration Date', 'paid-memberships-pro' );?></label>
					<select id="ExpirationMonth" name="ExpirationMonth" class="<?php echo pmpro_get_element_class( '', 'ExpirationMonth' ); ?>">
						<option value="01" <?php if($ExpirationMonth == "01") { ?>selected="selected"<?php } ?>>01</option>
						<option value="02" <?php if($ExpirationMonth == "02") { ?>selected="selected"<?php } ?>>02</option>
						<option value="03" <?php if($ExpirationMonth == "03") { ?>selected="selected"<?php } ?>>03</option>
						<option value="04" <?php if($ExpirationMonth == "04") { ?>selected="selected"<?php } ?>>04</option>
						<option value="05" <?php if($ExpirationMonth == "05") { ?>selected="selected"<?php } ?>>05</option>
						<option value="06" <?php if($ExpirationMonth == "06") { ?>selected="selected"<?php } ?>>06</option>
						<option value="07" <?php if($ExpirationMonth == "07") { ?>selected="selected"<?php } ?>>07</option>
						<option value="08" <?php if($ExpirationMonth == "08") { ?>selected="selected"<?php } ?>>08</option>
						<option value="09" <?php if($ExpirationMonth == "09") { ?>selected="selected"<?php } ?>>09</option>
						<option value="10" <?php if($ExpirationMonth == "10") { ?>selected="selected"<?php } ?>>10</option>
						<option value="11" <?php if($ExpirationMonth == "11") { ?>selected="selected"<?php } ?>>11</option>
						<option value="12" <?php if($ExpirationMonth == "12") { ?>selected="selected"<?php } ?>>12</option>
					</select>/<select id="ExpirationYear" name="ExpirationYear" class="<?php echo pmpro_get_element_class( '', 'ExpirationYear' ); ?>">
						<?php
							$num_years = apply_filters( 'pmpro_num_expiration_years', 10 );

							for($i = date_i18n("Y"); $i < intval( date_i18n("Y") ) + intval( $num_years ); $i++)
							{
						?>
							<option value="<?php echo $i?>" <?php if($ExpirationYear == $i) { ?>selected="selected"<?php } ?>><?php echo $i?></option>
						<?php
							}
						?>
					</select>
				</div>
				<?php
					$pmpro_show_cvv = apply_filters("pmpro_show_cvv", true);
					if($pmpro_show_cvv) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-cvv', 'pmpro_payment-cvv' ); ?>">
						<label for="CVV"><?php _e('Security Code (CVC)', 'paid-memberships-pro' );?></label>
						<input id="CVV" name="CVV" type="text" size="4" value="<?php if(!empty($_REQUEST['CVV'])) { echo esc_attr($_REQUEST['CVV']); }?>" class="<?php echo pmpro_get_element_class( 'input', 'CVV' ); ?>" />  <small>(<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo pmpro_https_filter(PMPRO_URL); ?>/pages/popup-cvv.html','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');"><?php _e("what's this?", 'paid-memberships-pro' );?></a>)</small>
					</div>
				<?php } ?>
				<?php if($pmpro_show_discount_code) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-discount-code', 'pmpro_payment-discount-code' ); ?>">
						<label for="discount_code"><?php _e('Discount Code', 'paid-memberships-pro' );?></label>
						<input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="text" size="10" value="<?php echo esc_attr($discount_code); ?>" />
						<input type="button" id="discount_code_button" name="discount_code_button" value="<?php _e('Apply', 'paid-memberships-pro' );?>" />
						<p id="discount_code_message" class="<?php echo pmpro_get_element_class( 'pmpro_message', 'discount_code_message' ); ?>" style="display: none;"></p>
					</div>
				<?php } ?>
			</div> <!-- end pmpro_checkout-fields -->
			<?php if(!empty($sslseal)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-rightcol pmpro_sslseal', 'pmpro_sslseal' ); ?>"><?php echo stripslashes($sslseal); ?></div>
			</div> <!-- end pmpro_checkout-fields-display-seal -->
			<?php } ?>
		</div> <!-- end pmpro_payment_information_fields -->
	<?php } ?>
	
	<?php
		do_action('pmpro_checkout_after_user_fields');
	?>

	<?php
		do_action('pmpro_checkout_boxes');
	?>

	<?php do_action('pmpro_checkout_after_payment_information_fields'); ?>

	<?php if($tospage && !$pmpro_review) { ?>
		<div id="pmpro_tos_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_tos_fields' ); ?> checkout_forth_part checkout_page paypalexpress_part">
			<hr />
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php echo esc_html( $tospage->post_title );?></span>
			</h3>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<div id="pmpro_license" class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field', 'pmpro_license' ); ?>">
<?php 
	/**
	 * Hook to run formatting filters before displaying the content of your "Terms of Service" page at checkout.
	 *
	 * @since 2.4.1
	 *
	 * @param string $pmpro_tos_content The content of the post assigned as the Terms of Service page.
	 * @param string $tospage The post assigned as the Terms of Service page.
	 *
	 * @return string $pmpro_tos_content
	 */
	$pmpro_tos_content = apply_filters( 'pmpro_tos_content', do_shortcode( $tospage->post_content ), $tospage );
	echo $pmpro_tos_content;
?>
				</div> <!-- end pmpro_license -->
				<?php
					if ( isset( $_REQUEST['tos'] ) ) {
						$tos = intval( $_REQUEST['tos'] );
					} else {
						$tos = "";
					}
				?>
				<input type="checkbox" name="tos" value="1" id="tos" <?php checked( 1, $tos ); ?> /> <label class="<?php echo pmpro_get_element_class( 'pmpro_label-inline pmpro_clickable', 'tos' ); ?>" for="tos"><?php printf(__('I agree to the %s', 'paid-memberships-pro' ), $tospage->post_title);?></label>
				
				<div>
					<a href="https://www.planpets.com.br/coberturas-do-plano/" target="_blank" name="redirectplano" id="redirectplano" class="btn btn-success">Baixar Tabela de Cobertura do Plano | </a>
					<a href="https://www.planpets.com.br/wp-content/uploads/2021/04/contrato-planpets.pdf" target="_blank" name="redirectcontrato" id="redirectcontrato" class="btn btn-success">Baixar Contrato</a>
				</div>
			</div> <!-- end pmpro_checkout-fields -->
		</div> <!-- end pmpro_tos_fields -->


		<?php
		}
	?>

	<?php do_action("pmpro_checkout_after_tos_fields"); ?>

	<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_captcha', 'pmpro_captcha' ); ?>">
	<?php
		global $recaptcha, $recaptcha_publickey;
		if ( $recaptcha == 2 || ( $recaptcha == 1 && pmpro_isLevelFree( $pmpro_level ) ) ) {
			echo pmpro_recaptcha_get_html($recaptcha_publickey, NULL, true);
		}
	?>
	</div> <!-- end pmpro_captcha -->

	<?php
		do_action('pmpro_checkout_after_captcha');
	?>

	<?php do_action("pmpro_checkout_before_submit_button"); ?>

	
	<div class="<?php echo pmpro_get_element_class( 'pmpro_submit' ); ?>">
		<hr />
		<div id="pmpro_message_error_bottom" class="pmpro_message pmpro_error" style='display:none;'>
			Por favor complete todos os campos obrigatórios.
		</div>
		<?php if ( $pmpro_msg ) { ?>
			<div id="pmpro_message_bottom" class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg; ?></div>
		<?php } else { ?>
			<div id="pmpro_message_bottom" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></div>
		<?php } ?>
		
		<?php if($pmpro_review) { ?>

			<span id="pmpro_submit_span">
				<input type="hidden" name="confirm" value="1" />
				<input type="hidden" name="token" value="<?php echo esc_attr($pmpro_paypal_token); ?>" />
				<input type="hidden" name="gateway" value="<?php echo esc_attr($gateway); ?>" />
				<input type="submit" id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php _e('Complete Payment', 'paid-memberships-pro' );?> &raquo;" />
			</span>

		<?php } else { ?>

			<?php
				$pmpro_checkout_default_submit_button = apply_filters('pmpro_checkout_default_submit_button', true);
				
				if($pmpro_checkout_default_submit_button)
				{
				?>
				<span id="pmpro_submit_span">
					<input type="hidden" name="submit-checkout" value="1" />
					<input type="button" id="pmpro_checkout_previous" value='« anterior' class='pmpro_btn'>
					<input type="submit"  id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class(  'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
					<input type="button" id="pmpro_checkout_next" value='Próximo »' class = 'pmpro_btn'>
				</span>
				<?php
				}
			?>

		<?php } ?>

	
		<span id="pmpro_processing_message" style="visibility: hidden;">
			<?php
				$processing_message = apply_filters("pmpro_processing_message", __("Processing...", 'paid-memberships-pro' ));
				echo $processing_message;
			?>
		</span>
	</div>

	
</form>

<script>
	$(function(){
        let currentpage = $('#current_checkout_page').val();
		$(".checkout_page").addClass('hidden');
		$("." + currentpage).removeClass('hidden');

		$("#pmpro_btn-submit").css({'display':'none'});
		$("#pmpro_checkout_previous").prop('disabled', true);
		submit = $('#issubmit').val();
		if (submit == 'false'){
			$('#pmpro_message_error').css({'display':'none'})
			$('#pmpro_message_error_bottom').css({'display':'none'});
			
			$('.pmpro_message.pmpro_error').css({'display':'none'});
			$('.pmpro_message_bottom.pmpro_error').css({'display':'none'});
		}

		configBreed();

		let petdatalength = $('#pmpro_planpets_table tbody .petdata').length
		if (petdatalength > 0){
			$('#noitem_tr').hide();
		}

		$(".gateway_paypalexpress").hide();
		$("#pmpro_payment_method").hide();

    });

	$(document).ready(function($){
		$("#bzipcode").inputmask("99999-999");
		$("#_birthdate").inputmask("99/99/9999");
		$("#bcpf").inputmask("999.999.999-99");
		$("#bphone").inputmask("(99)999999999");
	})

	$("input[type=radio][name=gateway]").on('change', function(){
		let currentpage = $('#current_checkout_page').val();
		$(".checkout_page").addClass('hidden');
		$(".checkout_page").css({"display":"none"});

		if ($(this).val() == "paypal"){
			$("." + currentpage).removeClass('hidden');
			$("." + currentpage).css({"display":"block"});
		}else{
			$(".checkout_first_part").removeClass('hidden');
			$(".checkout_first_part").css({"display":"block"});

			$(".paypalexpress_part").css({"display":"block"})
		}

		$("#pmpro_btn-submit").css({'display':'none'});
		$("#pmpro_checkout_previous").prop('disabled', true);
		submit = $('#issubmit').val();
		if (submit == 'false'){
			$('#pmpro_message_error').css({'display':'none'})
			$('#pmpro_message_error_bottom').css({'display':'none'});
			
			$('.pmpro_message.pmpro_error').css({'display':'none'});
			$('.pmpro_message_bottom.pmpro_error').css({'display':'none'});
		}

		configBreed();

		let petdatalength = $('#pmpro_planpets_table tbody .petdata').length
		if (petdatalength > 0){
			$('#noitem_tr').hide();
		}
	})

	
	function isValidDate(datestring) {
		if (datestring.length > 10){
			return false;
		}
		var parms = datestring.split(/[\/]/);
		var yyyy = parseInt(parms[2],10);
		var mm   = parseInt(parms[1],10);
		var dd   = parseInt(parms[0],10);
		var date = new Date(yyyy,mm-1,dd,0,0,0,0);
		return mm === (date.getMonth()+1) && dd === date.getDate() && yyyy === date.getFullYear();

	}

	$('#pmpro_checkout_next').on('click',function(e){
		$(".checkout_page").addClass('hidden');
		$(".checkout_page").css({"display":"none"});

		$('#pmpro_message_error').css({'display':'none'})
		$('#pmpro_message_error_bottom').css({'display':'none'});

		$('#pmpro_message').css({'display':'none'});
		$('#pmpro_message_bottom').css({'display':'none'});

		$('.pmpro_message.pmpro_error').css({'display':'none'});
		$('.pmpro_message_bottom.pmpro_error').css({'display':'none'});
		

		let currentpage = '';
		let isNext = true;
		var isUserLogin = $('#isUserLogin').val();
		switch ($('#current_checkout_page').val()){
			case 'checkout_first_part':
				if (isUserLogin == 1){
					if ($('#bfirstname').val() == '' || $('#blastname').val() == '' || $('#cpf').val() == '' || $('#bphone').val() == '' || $('#password').val() == '' || $('#password2').val() == '' || $('#bemail').val() == '' || $('#bconfirmemail').val() == ''){
						$('#pmpro_message_error_bottom').css({'display':'block'});
						$('#pmpro_message').css({'display':'none'});
						$('#pmpro_message_bottom').css({'display':'none'});
						isNext = false;
						currentpage = 'checkout_first_part';
					}else{
						const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
						if (!re.test($('#bemail').val()) || $('#bemail').val() != $('#bconfirmemail').val() || $("#password").val() != $("#password2").val() || $('#password').val().length < 7)
						{
							$('#pmpro_message_error_bottom').css({'display':'block'});
							$('#pmpro_message').css({'display':'none'});
							$('#pmpro_message_bottom').css({'display':'none'});
							isNext = false;
							currentpage = 'checkout_first_part';
						}else{
							currentpage = 'checkout_second_part';
						}
					}
				}else{
					currentpage = 'checkout_second_part';	
				}
				break;
			case 'checkout_second_part':
				if ($('#bfirstname').val() == '' || $('#blastname').val() == '' || $( "#bcountry option:selected" ).text() == ''|| $('#baddress1').val() == '' || $('#bcity').val() == '' || $('#bstate').val() == '' || $('#bzipcode').val() == '' || $('#bphone').val() == '' || $('#bemail').val() == '' || $('#bconfirmemail').val() == ''){
					$('#pmpro_message_error').css({'display':'block'});
					$('#pmpro_message_error_bottom').css({'display':'block'});
					$('#pmpro_message').css({'display':'none'});
					$('#pmpro_message_bottom').css({'display':'none'});
					isNext = false;
					currentpage = 'checkout_second_part';
				}else{
					currentpage = 'checkout_third_part';
				}
				break;
			case 'checkout_third_part':
				if ($('#pmpro_planpets_table tbody .petdata').length == 0){
					$('#pmpro_message_error').css({'display':'block'});
					$('#pmpro_message_error_bottom').css({'display':'block'});
					$('#pmpro_message').css({'display':'none'});
					$('#pmpro_message_bottom').css({'display':'none'});
					isNext = false;
					currentpage = 'checkout_third_part';
				}else{
					let petnamelist = [];
					let petbirthdatelist = [];
					let petgenderlist = [];
					let petspecieslist = [];
					let petbreedlist = [];
					let petdatalength = $('#pmpro_planpets_table tbody .petdata').length;
					for (let petindex = 0; petindex < petdatalength; petindex ++){
						nthpetelement = $('#pmpro_planpets_table tbody').children("tr").eq(petindex + 1);
						petnamelist.push(nthpetelement.find('.petname').val());
						petbirthdatelist.push(nthpetelement.find('.birthdate').val());
						petgenderlist.push(nthpetelement.find('.petgender').val());
						petspecieslist.push(nthpetelement.find('.species').val());
						petbreedlist.push(nthpetelement.find('.breed').val());
					}

					$('#petname').val(petnamelist);
					$('#birthdate').val(petbirthdatelist);
					$('#petgender').val(petgenderlist);
					$('#species').val(petspecieslist);
					$('#breed').val(petbreedlist);

					currentpage = 'checkout_forth_part';
					$('#pmpro_checkout_next').prop('disabled', true);
				}
				break;
			default:
				break;
		}

		
		$('#current_checkout_page').val(currentpage)
		$("." + currentpage).removeClass('hidden');
		$("." + currentpage).css({"display":"block"});

		if (currentpage == 'checkout_forth_part'){
			$("#pmpro_btn-submit").css({'display':'initial'});
		}

		if(isNext){
			$("#pmpro_checkout_previous").prop('disabled', false);
		}

	})

	$('#pmpro_btn-submit').on('click', function(e){
		if ($('#AccountNumber').val() == '' || $('#CVV').val() == '' || !$("#tos").is(':checked')){
			$('#pmpro_message_error').css({'display':'block'});
			$('#pmpro_message_error_bottom').css({'display':'block'});
			$('#pmpro_message').css({'display':'none'});
			$('#pmpro_message_bottom').css({'display':'none'});
			e.preventDefault();
		}
	})

	$('#pmpro_checkout_previous').on('click', function(e){
		$(".checkout_page").addClass('hidden');
		$(".checkout_page").css({"display":"none"});

		$('#pmpro_message_error').css({'display':'none'})
		$('#pmpro_message_error_bottom').css({'display':'none'});
		

		$('#pmpro_message').css({'display':'none'});
		$('#pmpro_message_bottom').css({'display':'none'});

		$('.pmpro_message.pmpro_error').css({'display':'none'});
		$('.pmpro_message_bottom.pmpro_error').css({'display':'none'});

		let currentpage = '';
		switch ($('#current_checkout_page').val()){
			case 'checkout_second_part':
				currentpage = 'checkout_first_part';
				$('#pmpro_checkout_previous').prop('disabled', true);
				break;
			case 'checkout_third_part':
				currentpage = 'checkout_second_part';
				break;
			case 'checkout_forth_part':
				currentpage = 'checkout_third_part';
				break;
			default:
				break;
		}

		$('#current_checkout_page').val(currentpage)
		$("." + currentpage).removeClass('hidden');
		$("." + currentpage).css({"display":"block"});

		$("#pmpro_btn-submit").css({'display':'none'});
		$("#pmpro_checkout_next").prop('disabled', false);
	})

	$('#pmpro_add_planpet').on('click', function(e){
		e.preventDefault();
		if ($('#_petname').val() == '' || $('#_birthdate').val() == '' || $( "#_petgender option:selected" ).text() == ''|| $('#_species').val() == '' || $('#_breed').val() == '' || !isValidDate($('#_birthdate').val())){
			$('#pmpro_message_error').css({'display':'block'});
			$('#pmpro_message_error_bottom').css({'display':'block'});
			$('#pmpro_message').css({'display':'none'});
			$('#pmpro_message_bottom').css({'display':'none'});
		}else{
			$('#noitem_tr').hide();
			if ($('#petid').val() == 0){//add
				let petscount = $('#pmpro_planpets_table tbody .petdata').length + 1
				let newplanpetshtml = '<tr class="petdata" id="pet_' + petscount + '"><td><input class="pmpro_checkout-field table_data petname" id="petname_' + petscount + '" name="petname_' + petscount + '" value="' + $('#_petname').val() + '" readonly></td>'
										+  '<td><input class="pmpro_checkout-field table_data birthdate" id="birthdate_' + petscount + '" value="' + $('#_birthdate').val() + '" name="birthdate_' + petscount + '" readonly></td>'
										+  '<td><input class="pmpro_checkout-field table_data petgender" id="petgender_' + petscount + '" value="' + $('#_petgender').val() + '" name="petgender_' + petscount + '" readonly></td>'
										+  '<td><input class="pmpro_checkout-field table_data species" id="species_' + petscount + '" value="' + $('#_species').val() + '" name="species_' + petscount + '" readonly></td>'
										+  '<td><input class="pmpro_checkout-field table_data breed" id="breed_' + petscount + '" value="' + $('#_breed').val() + '" name="breed_' + petscount + '" readonly></td>'
										+  '<td><a type="button" onclick="petEdit(this);" id="petupdate_' + petscount + '" style="cursor:pointer;" >editar</a><a type="button" id="petdelete_' + petscount + '"  onclick="petDelete(this);" style="cursor:pointer;"> | excluir</a></td></tr>'
				$('#pmpro_planpets_table tbody').append(newplanpetshtml);
			}else{//update
				let petid = $('#petid').val();
				$('#pet_' + petid + ' .petname').val($('#_petname').val());
				$('#pet_' + petid + ' .birthdate').val($('#_birthdate').val());
				$('#pet_' + petid + ' .petgender').val($('#_petgender').val());
				$('#pet_' + petid + ' .species').val($('#_species').val());
				$('#pet_' + petid + ' .breed').val($('#_breed').val());
			}
			//format
			$('#_petname').val('');
			$('#_birthdate').val('');
			$( "#_petgender" ).val('Macho');
			$('#_species').val('Canino');
			configBreed();
			$('#_breed').val('Affenpinscher');
			$('#petid').val('0');

			
			$('#pmpro_message_error').css({'display':'none'})
			$('#pmpro_message_error_bottom').css({'display':'none'});
			
			$('.pmpro_message.pmpro_error').css({'display':'none'});
			$('.pmpro_message_bottom.pmpro_error').css({'display':'none'});
		}
	})

	function petDelete(ctl) {
		$(ctl).parents("tr").remove();
		//format
		$('#_petname').val('');
		$('#_birthdate').val('');
		$( "#_petgender" ).val('Macho');
		$('#s_pecies').val('Canino');
		configBreed();
		$('#_breed').val('Affenpinscher');
		$('#petid').val('0');
		//refresh index
		let petscount = $('#pmpro_planpets_table tbody .petdata').length;
		for (let petindex = 0; petindex < petscount; petindex ++){
			nthpetelement = $('#pmpro_planpets_table tbody').children("tr").eq(petindex + 1);
			nthpetelement.attr('id', 'pet_' + (petindex + 1));
			nthpetelement.find('.petname').attr('id', 'petname_' + (petindex + 1));
			nthpetelement.find('.petname').attr('name', 'petname_' + (petindex + 1));

			nthpetelement.find('.birthdate').attr('id', 'birthdate_' + (petindex + 1));
			nthpetelement.find('.birthdate').attr('name', 'birthdate_' + (petindex + 1));
			
			nthpetelement.find('.petgender').attr('id', 'petgender_' + (petindex + 1));
			nthpetelement.find('.petgender').attr('name', 'petgender_' + (petindex + 1));
			
			nthpetelement.find('.species').attr('id', 'species_' + (petindex + 1));
			nthpetelement.find('.species').attr('name', 'species_' + (petindex + 1));
			
			nthpetelement.find('.breed').attr('id', 'breed_' + (petindex + 1));
			nthpetelement.find('.breed').attr('name', 'breed_' + (petindex + 1));
		}
	}

	function petEdit(ctl){
		let rowElement = $(ctl).parent("td").parent("tr");
		let petname = rowElement.find(".petname").val();
		let birthdate = rowElement.find(".birthdate").val();
		let petgender = rowElement.find(".petgender").val();
		let species = rowElement.find(".species").val();
		let breed = rowElement.find(".breed").val();
		let petid = rowElement.attr("id").replace("pet_", "");
		$("#petid").val(petid);
		$("#_petname").val(petname);
		$("#_birthdate").val(birthdate);
		$("#_petgender").val(petgender);
		$("#_species").val(species);
		configBreed();
		$("#_breed").val(breed);
	}

	function configBreed(){
		let racahtml = "";
		
		$('.pmpro_checkout-field-breed').empty();
		racahtml = "<label for='breed'>Raça</label>";
		if ($('#_species').val() == 'Canino'){
			racahtml += "<select id='_breed' name='_breed' class='input pmpro_required'>" + 
							"<option value='Affenpinscher'>Affenpinscher</option>" +
							"<option value='Afghan Hound'>Afghan Hound</option>" +
							"<option value='Akita'>Akita</option>" +
							"<option value='Akita Inu'>Akita Inu</option>" +
							"<option value='American Bully'>American Bully</option>" +
							"<option value='American Staffordshire Terrier'>American Staffordshire Terrier</option>" +
							"<option value='Basenji'>Basenji</option>" +
							"<option value='Basset Hound'>Basset Hound</option>" +
							"<option value='Beagle'>Beagle</option>" +
							"<option value='Beagle Harrier'>Beagle Harrier</option>" +
							"<option value='Bearded Collie'>Bearded Collie</option>" +
							"<option value='Bernese Mountain Dog'>Bernese Mountain Dog</option>" +
							"<option value='Bichon Frisé'>Bichon Frisé</option>" +
							"<option value='Bichon Havanês'>Bichon Havanês</option>" +
							"<option value='Bloodhound'>Bloodhound</option>" +
							"<option value='Boiadeiro Australiano'>Boiadeiro Australiano</option>" +
							"<option value='Boiadeiro de Berna'>Boiadeiro de Berna</option>" +
							"<option value='Border Collie'>Border Collie</option>" +
							"<option value='Borzoi'>Borzoi</option>" +
							"<option value='Boston Terrier'>Boston Terrier</option>" +
							"<option value='Boxer'>Boxer</option>" +
							"<option value='Braco Húngaro'>Braco Húngaro</option>" +
							"<option value='Bull Terrier'>Bull Terrier</option>" +
							"<option value='Bulldog Francês'>Bulldog Francês</option>" +
							"<option value='Bulldog Inglês'>Bulldog Inglês</option>" +
							"<option value='Bullmastiff'>Bullmastiff</option>" +
							"<option value='Cane Corso'>Cane Corso</option>" +
							"<option value='Cão da Serra da Estrela'>Cão da Serra da Estrela</option>" +
							"<option value='Cão de Montanha dos Pirenéus'>Cão de Montanha dos Pirenéus</option>" +
							"<option value='Cão Pelado de Crista Chinês'>Cão Pelado de Crista Chinês</option>" +
							"<option value='Cavalier King Charles Spaniel'>Cavalier King Charles Spaniel</option>" +
							"<option value='Chihuahua'>Chihuahua</option>" +
							"<option value='Chow-Chow'>Chow-Chow</option>" +
							"<option value='Cocker Spaniel Americano'>Cocker Spaniel Americano</option>" +
							"<option value='Cocker Spaniel Inglês'>Cocker Spaniel Inglês</option>" +
							"<option value='Collie'>Collie</option>" +
							"<option value='Cton de Tuléar'>Cton de Tuléar</option>" +
							"<option value='Dachshund'>Dachshund</option>" +
							"<option value='Dalmata'>Dalmata</option>" +
							"<option value='Dandie Dinmont Terrier'>Dandie Dinmont Terrier</option>" +
							"<option value='Dobermann'>Dobermann</option>" +
							"<option value='Dogo Canário'>Dogo Canário</option>" +
							"<option value='Dogue Alemão'>Dogue Alemão</option>" +
							"<option value='Dogue Argentino'>Dogue Argentino</option>" +
							"<option value='Dogue de Bordeaux'>Dogue de Bordeaux</option>" +
							"<option value='Fila Brasileiro'>Fila Brasileiro</option>" +
							"<option value='Fox Paulistinha'>Fox Paulistinha</option>" +
							"<option value='Fox Terrier'>Fox Terrier</option>" +
							"<option value='Foxhound Americano'>Foxhound Americano</option>" +
							"<option value='Galfo Inglês'>Galfo Inglês</option>" +
							"<option value='Golden Retriever'>Golden Retriever</option>" +
							"<option value='Griffon de Bruxelas'>Griffon de Bruxelas</option>" +
							"<option value='Husky Siberiano'>Husky Siberiano</option>" +
							"<option value='Jack Russel Terrier'>Jack Russel Terrier</option>" +
							"<option value='Kerry BuleTerrier'>Kerry BuleTerrier</option>" +
							"<option value='Kuvasz'>Kuvasz</option>" +
							"<option value='Labrador'>Labrador</option>" +
							"<option value='Laika'>Laika</option>" +
							"<option value='Lakeland Terrier'>Lakeland Terrier</option>" +
							"<option value='Leonberger'>Leonberger</option>" +
							"<option value='Lhasa Apso'>Lhasa Apso</option>" +
							"<option value='Lulu da Pomerânia'>Lulu da Pomerânia</option>" +
							"<option value='Malamute do Alasca'>Malamute do Alasca</option>" +
							"<option value='Maltês'>Maltês</option>" +
							"<option value='Mastiff'>Mastiff</option>" +
							"<option value='Mastim Inglês'>Mastim Inglês</option>" +
							"<option value='Matino Napoletano'>Matino Napoletano</option>" +
							"<option value='Mudi'>Mudi</option>" +
							"<option value='Norwich Terrier'>Norwich Terrier</option>" +
							"<option value='Old English Sheepdog'>Old English Sheepdog</option>" +
							"<option value='Papillon/Staniel Anão Continental'>Papillon/Staniel Anão Continental</option>" +
							"<option value='Pastor - de - Shetland'>Pastor - de - Shetland</option>" +
							"<option value='Pastor Alemão'>Pastor Alemão</option>" +
							"<option value='Pastor Australiano'>Pastor Australiano</option>" +
							"<option value='Pastor Belga'>Pastor Belga</option>" +
							"<option value='Pastor Branco Suíço'>Pastor Branco Suíço</option>" +
							"<option value='Pequeno Lebrel Italiano'>Pequeno Lebrel Italiano</option>" +
							"<option value='Pequinês'>Pequinês</option>" +
							"<option value='Perdigueiro Português'>Perdigueiro Português</option>" +
							"<option value='Pinscher'>Pinscher</option>" +
							"<option value='Pitbull'>Pitbull</option>" +
							"<option value='Pointer Inglês'>Pointer Inglês</option>" +
							"<option value='Poodle'>Poodle</option>" +
							"<option value='Poodle Toy'>Poodle Toy</option>" +
							"<option value='Pug'>Pug</option>" +
							"<option value='Rottweiler'>Rottweiler</option>" +
							"<option value='Saluki'>Saluki</option>" +
							"<option value='Samoieda'>Samoieda</option>" +
							"<option value='São Bernardo'>São Bernardo</option>" +
							"<option value='Schipperke'>Schipperke</option>" +
							"<option value='Schnauzer Gigante'>Schnauzer Gigante</option>" +
							"<option value='Schnauzer Miniatura'>Schnauzer Miniatura</option>" +
							"<option value='Schnauzer Standard'>Schnauzer Standard</option>" +
							"<option value='Shar-pei'>Shar-pei</option>" +
							"<option value='Sheepdog'>Sheepdog</option>" +
							"<option value='Shiba-Inu'>Shiba-Inu</option>" +
							"<option value='Shih-tzu'>Shih-tzu</option>" +
							"<option value='Silky Terrier'>Silky Terrier</option>" +
							"<option value='Spitz Alemão'>Spitz Alemão</option>" +
							"<option value='SRD (Sem Raça Definida ou Vira-Lata)'>SRD (Sem Raça Definida ou Vira-Lata)</option>" +
							"<option value='Staffordshire'>Staffordshire</option>" +
							"<option value='Staffordshire Bull Terrier'>Staffordshire Bull Terrier</option>" +
							"<option value='Terrier Escocês'>Terrier Escocês</option>" +
							"<option value='Tiberan Terrier'>Tiberan Terrier</option>" +
							"<option value='Tosa'>Tosa</option>" +
							"<option value='Volpino Italiano'>Volpino Italiano</option>" +
							"<option value='Weimaraner'>Weimaraner</option>" +
							"<option value='Welsh Corgi Pembroke'>Welsh Corgi Pembroke</option>" +
							"<option value='Welsh Terrier'>Welsh Terrier</option>" +
							"<option value='West Highland White Terrier'>West Highland White Terrier</option>" +
							"<option value='Whippet'>Whippet</option>" +
							"<option value='Yorkshire'>Yorkshire</option>" +

						"</select>"
		}else if ($('#_species').val() == 'Felino'){
			racahtml += "<select id='_breed' name='_breed' class='input pmpro_required'>" + 
							"<option value='Abissínio'>Abissínio</option>" +
							"<option value='American Shorthair'>American Shorthair</option>" +
							"<option value='Angorá'>Angorá</option>" +
							"<option value='Balinês'>Balinês</option>" +
							"<option value='Bengal'>Bengal</option>" +
							"<option value='Bobtail Americano'>Bobtail Americano</option>" +
							"<option value='Bobtail Japonês'>Bobtail Japonês</option>" +
							"<option value='Bombay'>Bombay</option>" +
							"<option value='Burmês'>Burmês</option>" +
							"<option value='Burmês Vermelho'>Burmês Vermelho</option>" +
							"<option value='Chartreux'>Chartreux</option>" +
							"<option value='Colorpoint de Pelo Curto'>Colorpoint de Pelo Curto</option>" +
							"<option value='Cornish Rex'>Cornish Rex</option>" +
							"<option value='Curl Americano'>Curl Americano</option>" +
							"<option value='Cymric'>Cymric</option>" +
							"<option value='Devon Rex'>Devon Rex</option>" +
							"<option value='Himalaio'>Himalaio</option>" +
							"<option value='Jaguatirica'>Jaguatirica</option>" +
							"<option value='Javanês'>Javanês</option>" +
							"<option value='Korat'>Korat</option>" +
							"<option value='LaPerm'>LaPerm</option>" +
							"<option value='Maine Coon'>Maine Coon</option>" +
							"<option value='Manx'>Manx</option>" +
							"<option value='Mau Egípcio'>Mau Egípcio</option>" +
							"<option value='Mist Australiano'>Mist Australiano</option>" +
							"<option value='Munchkin'>Munchkin</option>" +
							"<option value='Norueguês da Floresta'>Norueguês da Floresta</option>" +
							"<option value='Ocicat'>Ocicat</option>" +
							"<option value='Pelo Curto Americano'>Pelo Curto Americano</option>" +
							"<option value='Pelo Curto Brasileiro'>Pelo Curto Brasileiro</option>" +
							"<option value='Pelo Curto Europeu'>Pelo Curto Europeu</option>" +
							"<option value='Pelo Curto Inglês'>Pelo Curto Inglês</option>" +
							"<option value='Persa'>Persa</option>" +
							"<option value='Pixie-Bob'>Pixie-Bob</option>" +
							"<option value='Ragdoll'>Ragdoll</option>" +
							"<option value='Russo Azul'>Russo Azul</option>" +
							"<option value='Sagrado da Birmânia'>Sagrado da Birmânia</option>" +
							"<option value='Savannah'>Savannah</option>" +
							"<option value='Scottish Fold'>Scottish Fold</option>" +
							"<option value='Selkirk Rex'>Selkirk Rex</option>" +
							"<option value='Siamês'>Siamês</option>" +
							"<option value='Siberiano'>Siberiano</option>" +
							"<option value='Singapura'>Singapura</option>" +
							"<option value='Somali'>Somali</option>" +
							"<option value='Sphynx'>Sphynx</option>" +
							"<option value='SRD (Sem Raça Definida ou Vira-Lata)'>SRD (Sem Raça Definida ou Vira-Lata)</option>" +
							"<option value='Thai'>Thai</option>" +
							"<option value='Tonquinês'>Tonquinês</option>" +
							"<option value='Toyger'>Toyger</option>" +
							"<option value='Turkish Angorá'>Turkish Angorá</option>" +
							"<option value='Usuri'>Usuri</option>" +
						"</select>"
		}else{
			racahtml += "<input id='_breed' name='_breed' type='text' class='input pmpro_required' size='30' value=''>";
		}
		racahtml += "<span class='pmpro_asterisk'></span>";

		$('.pmpro_checkout-field-breed').html(racahtml);
	}

	$('#_species').on('change',function(e){
		configBreed();
	})

	$('#_petname').on('keypress', function (event) {
		var regex = new RegExp(",");
		var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		if (regex.test(key)) {
			event.preventDefault();
			return false;
		}
	});
	
	$('#_breed').on('keypress', function (event) {
		var regex = new RegExp(",");
		var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		if (regex.test(key)) {
			event.preventDefault();
			return false;
		}
	});
</script>

<?php do_action('pmpro_checkout_after_form'); ?>

</div> <!-- end pmpro_level-ID -->
