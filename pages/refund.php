<div class="<?php echo pmpro_get_element_class( 'pmpro_consult_wrap' ); ?>">
    <?php
	global $wpdb, $pmpro_consult, $pmpro_msg, $pmpro_msgt, $current_user;

	if($pmpro_msg)
	{
	?>
	<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg?></div>
	<?php
	}
	?>

    <?php
    if ($pmpro_consult){
        ?>
    
        <?php
    }else{
        $refundtable = $wpdb->prefix . 'refund';
        $proceduremeta = $wpdb->prefix . 'graceperiod_meta';
		$membershipusers = $wpdb->prefix . 'pmpro_memberships_users';
		$userid = $current_user->ID;
        
        $refunds = $wpdb->get_results("select t0.ID, t0.petname, t1.procedurename, t0.consultation, t0.amount, t0.refundstatus, t0.invoicefilename, t0.clinicname from $refundtable t0 LEFT JOIN $proceduremeta t1 ON t0.procedureid = t1.id LEFT JOIN $membershipusers t2 on t2.id = t0.membershipid where t2.user_id = $userid", OBJECT);
        
        ?>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <style>
            #addnewrefund{
                background-color: #0f9e96;
                margin-top: 30px;
                margin-bottom: 15px;
                
                margin-left: 15px;
                margin-bottom:15px;
            }

			#pmpro_refund_table th{
				text-align:center;
			}
			
			
			a:hover{
				text-decoration:none;
			}
			
/* 			.fusion-contact-info{
				width:316.06px;
				height:48px;
			} */
			
			.botaoacesso{
				line-height:initial;
				height:32px;
				font-size: 13.33px;
    			font-weight: 400;
			}
        </style>
        <table id="pmpro_refund_table" class="pmpro_table pmpro_refund" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>Nome do Pet</th>
					<th>Procedimento</th>
					<th>Data da Consulta</th>
					<th>Valor</th>
					<th>Status do Reembolso</th>
					<th>Nota Fiscal</th>
					<th>Nome da Clínica</th>
				</tr>
			</thead>
        <?php
		if($refunds)
		{
			?>
			<tbody>
			<?php
				foreach($refunds as $refund)
				{
					?>
					<tr>
						<td><?php echo $refund->petname ?></td>
						<td><?php echo $refund->procedurename ?></td>
						<td><?php echo $refund->consultation ?></td>
						<td><?php echo pmpro_formatPrice($refund->amount);?></td>
						<td><?php switch ($refund->refundstatus){
							case "1":
								echo 'Pendente';
								break;
							case '2':
								echo 'Agendado';
								break;
							case '3':
								echo 'Pago';
								break;
							default:
								echo 'Pendente';
								break;
						}?></td>
						<td><a href="<?php echo $refund->invoicefilename;?>">
						<?php if (isset($refund->invoicefilename) && !empty($refund->invoicefilename))
							{
								echo "Baixar Nota";
							}else{
								echo "Não selecionado";
							}
						 ?></a></td>
						<td><?php echo $refund->clinicname;?></td>
					</tr>
					<?php
				}
			?>
            
			</tbody>
			<?php
		}
		else
		{
			?>
            <tr>
                <td valign="top" colspan="7" class="dataTables_empty"><?php _e('Nenhum reembolso solicitado.', 'paid-memberships-pro' );?></td>
            </tr>
			<?php
		}
        ?>
        </table>
        <a href="<?php echo pmpro_url("requestnewrefund") ?>" name="addnewrefund" id="addnewrefund" class="btn btn-success" hidden>Solicitar Novo Reembolso</a>
        <?php
    }
    ?>
    <p style="float: right;width: 300px;" class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
        <span style="margin-top:30px;" class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-right' ); ?>"><a href="<?php echo pmpro_url("account")?>"><?php _e('View Your Membership Account &rarr;', 'paid-memberships-pro' );?></a></span>
        <?php if ( $pmpro_consult ) { ?>
            <span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-left' ); ?>"><a href="<?php echo pmpro_url("consult")?>"><?php _e('&larr; View All Consults', 'paid-memberships-pro' );?></a></span>
        <?php } ?>
    </p> 
</div>
