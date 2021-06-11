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
        $consultid = $_REQUEST['id'];
        $levelid = $_REQUEST['level'];
		$procmetaid = $_REQUEST['procID'];
        $detailtable = $wpdb->prefix . 'graceperiod_detail';
        $metatable = $wpdb->prefix . 'graceperiod_meta';
        $refundtable = $wpdb->prefix . 'refund';
        $membershipleveltable = $wpdb->prefix . 'pmpro_membership_levels';
        $membershipusertable = $wpdb->prefix . 'pmpro_memberships_users';
        $userid = $current_user->ID;
        if (isset($consultid) && isset($levelid)){
            $consults = $wpdb->get_results("SELECT t1.ID, t2.procedurename, t1.graceperiod, t1.usagelimit, t1.periodicity, t1.refund, t3.anual_refund  FROM $refundtable t0 LEFT JOIN $detailtable t1 ON t1.ID = t0.procedureid LEFT JOIN $metatable t2 ON t2.ID = t1.meta_id LEFT JOIN $membershipleveltable t3 ON t3.id = t1.n_coverage_id WHERE t0.membershipid = $levelid", OBJECT);
        }else if(isset($procmetaid)){
             $consults = $wpdb->get_results("select DISTINCT t0.ID, t1.procedurename, t0.graceperiod, t0.usagelimit, t0.periodicity, t0.refund, t2.anual_refund from $detailtable t0 left join $metatable t1 on t1.id = t0.meta_id left join $membershipleveltable t2 on t2.id = t0.n_coverage_id WHERE t1.id=$procmetaid", OBJECT);
		}else{
             $consults = $wpdb->get_results("select DISTINCT t0.ID, t3.petname, t3.ID petID, t1.procedurename, t0.graceperiod, t0.usagelimit, t0.periodicity, t0.refund, t2.anual_refund from $detailtable t0 left join $metatable t1 on t1.id = t0.meta_id left join $membershipleveltable t2 on t2.id = t0.n_coverage_id left join $membershipusertable t3 on t3.membership_id = t2.id  WHERE t3.status = 'active' and t3.user_id = $userid", OBJECT);
        }
        ?>



        <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
	
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

        <style>
            .dataTables_info{
                margin: 0;
                position: absolute;
                top: 50%;
                -ms-transform: translateY(-50%);
                transform: translateY(-50%);
                padding-top:0 !important;
            }

            table th{
                padding: 0.75rem !important;
                text-align:center;
            }
			
			.pet_selector{
				width: 100%;
				border: none;
				font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    			font-size: 14px;
/* 				background-color: #f9f9f9; */
				background:transparent;
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
			
			#petselector{
				width:150px;
				height:40px;
			}
			
			#petselector_container{
				margin-bottom:30px;
			}
        </style>
	<div id="petselector_container">
		<strong>Selecionar Pet : </strong>
		<select id="petselector">
		<?php 
		$allpetdatas = $wpdb->get_results("select DISTINCT t3.ID, t3.petname from $membershipusertable t3 WHERE t3.status = 'active' and t3.user_id = $userid", OBJECT);?>
		<option value="0">TODOS</option>
		<?php foreach ($allpetdatas as $petdata)	{?>
			<option value="<?php echo $petdata->ID; ?>"><?php echo $petdata->petname; ?></option>
		<?php } ?>
		</select>
	</div>
        <table id="pmpro_consults_table" class="table table-striped table-bordered" width="100%">
			<thead>
				<tr>
					<th hidden></th>
					<th>Nome do Pet</th>
					<th><?php _e('Name', 'paid-memberships-pro' ); ?></th>
					<th>Período de Carência</th>
					<th>Limite de Uso</th>
					<th>Periodicidade</th>
					<th>Reembolso</th>
					<th>Reembolso Anual</th>
					<th>Disponibilidade</th>
				</tr>
			</thead>
        <?php
		if($consults)
		{
			?>
			<tbody>
			<?php
				foreach($consults as $consult)
				{
					
					$pets = $wpdb->get_results("select DISTINCT t3.ID, t3.petname from $detailtable t0 left join $metatable t1 on t1.id = t0.meta_id left join $membershipleveltable t2 on t2.id = t0.n_coverage_id left join $membershipusertable t3 on t3.membership_id = t2.id  WHERE t3.status = 'active' and t3.user_id = $userid and t0.ID = $consult->ID", OBJECT);
					?>
					<?php 
						$availablecolor = '';
						$curdate = time();//date('Y-m-d');
						$usertable = $wpdb->prefix . 'users';
						$userid = $current_user->ID;
						$isgraceperiod = false;
						$ndiffgraceperiod = $consult->graceperiod;
						//$startdate_datas = $wpdb->get_results("select user_registered from $usertable WHERE id = $userid", OBJECT);
						$startdate_datas = $wpdb->get_results("select startdate from $membershipusertable WHERE id = $consult->petID", OBJECT);
						foreach($startdate_datas as $startdate_data){
							$startdate = strtotime($startdate_data->startdate);
							$diff = floor(($curdate - $startdate) / (60 * 60 * 24));
							$ndiffgraceperiod = $consult->graceperiod - $diff < 0 ? 0 : $consult->graceperiod - $diff;
							if ($diff < $consult->graceperiod){
								$isgraceperiod = true;
								$availablecolor = '&#128308';
							}
						}
					
						$isusagelimit = false;
						$detailID = $consult->ID;
						$nusagelimit = 0;
						$usagecounts = $wpdb->get_results("SELECT count(*) count FROM pp_refund t0 LEFT JOIN pp_graceperiod_detail t1 ON t1.meta_id = t0.procedureid WHERE t1.ID = $detailID AND t0.userid = $userid and t0.refundstatus!=4 and t0.membershipid=$consult->petID", OBJECT);
						foreach ($usagecounts as $usagecount){
							if ($usagecount->count >= $consult->usagelimit){
								$isusagelimit = true;
								$availablecolor = '&#128308';
							}else{
								$isusagelimit = false;
							}	
							$nusagelimit = $consult->usagelimit - $usagecount->count < 0 ? 0 : $consult->usagelimit - $usagecount->count;
						}
						$isperiodicity = false;
						$ndiffperiodicity = $consult->periodicity;
						$periodics = $wpdb->get_results("select t0.refunddate from pp_refund t0 left join pp_graceperiod_detail t1 on t1.meta_id = t0.procedureid where t1.id = $detailID and t0.userid = $userid and t0.refundstatus!=4 and t0.membershipid=$consult->petID order by t0.id desc limit 1", OBJECT);
						foreach ($periodics as $periodic){
							$refunddate = strtotime($periodic->refunddate);
							$diff = floor(($curdate - $refunddate) / (60 * 60 * 24));
							$ndiffperiodicity = $consult->periodicity - $diff < 0 ? 0 : $consult->periodicity - $diff;
							if ($diff < $consult->periodicity){
								$isperiodicity = true;
								$availablecolor = '&#128308';
							}else{
								$isperiodicity = false;
							}
						}
						
						$isrefund = false;
						$ndiffrefund = $consult->refund;
						$refunddatas = $wpdb->get_results("select t0.amount from pp_refund t0 left join pp_graceperiod_detail t1 on t1.meta_id = t0.procedureid where t1.id = $detailID and t0.userid = $userid and t0.refundstatus!=4 order by t0.id desc limit 1", OBJECT);
						foreach ($refunddatas as $refunddata){
							if ($refunddata->amount >= $consult->refund){
								$isrefund = true;
								$ndiffrefund = $refunddata->amount - $consult->refund;
								$availablecolor = '&#128308';
							}else{
								$isrefund = false;
								$ndiffrefund = $consult->refund - $refunddata->amount;
							}
						}
					
						$isanualrefund = false;
						$ndiffanualrefund = $consult->anual_refund;
						$anualrefunddatas = $wpdb->get_results("select sum(t0.amount) sum_amount from (select DISTINCT t0.* from pp_refund t0 left join pp_graceperiod_detail t1 on t1.meta_id = t0.procedureid where t0.userid = $userid and t0.refundstatus!=4) t0", OBJECT);
						foreach ($anualrefunddatas as $anualrefunddata){
							if ($anualrefunddata->sum_amount >= $consult->anual_refund){
								$isanualrefund = true;
								$ndiffanualrefund = $anualrefunddata->sum_amount - $consult->anual_refund;
								$availablecolor = '&#128308';
							}else{
								$isanualrefund = false;
								$ndiffanualrefund = $consult->anual_refund - $anualrefunddata->sum_amount;
							}
						}
						
					?>
					<tr>
						<td class="pet_selector_value" hidden><?php echo $consult->petID ?></td>
						<td><?php echo $consult->petname ?>
<!-- 							<select name="pet_selector" class="pet_selector">
								<?php 
								$petid = $pets[0]->ID;
								foreach ($pets as $pet){ ?>
								 	<option value="<?php echo $pet->ID; ?>"><?php echo $pet->petname; ?></option>
								<?php } ?>
                     		</select> -->
						</td>
						<td><?php echo $consult->procedurename ?></td>
						<td <?php if($isgraceperiod == true){echo 'isclick=\'1\' title=\'Aguarde o período de carência do seu plano para este procedimento. Faltam ' . $ndiffgraceperiod . ' dias para a liberação.\' style=\'background-color:#fdb9ae\'';} ?>><?php echo $ndiffgraceperiod ?></td>
						<td <?php if($isusagelimit == true){echo 'isclick=\'1\' title=\'Limite de uso do procedimento excedido.\' style=\'background-color:#fdb9ae\'';} ?>><?php echo $nusagelimit;?></td>
						<td <?php if($isperiodicity == true){echo 'isclick=\'1\' title=\'Aguarde o período de periodicidade do seu plano para este procedimento. Faltam ' . $ndiffperiodicity . ' dias para a liberação.\' style=\'background-color:#fdb9ae\'';} ?>><?php echo $ndiffperiodicity;?></td>
						<td <?php if($isrefund == true){echo 'isclick=\'1\' title=\'Você atingiu o limite de reembolso deste procedimento no seu plano. Você excedeu o valor em R$' . $ndiffrefund . '\' style=\'background-color:#fdb9ae\'';} ?>><?php echo pmpro_formatPrice($ndiffrefund);?></td>
						<td <?php if($isanualrefund == true){echo 'isclick=\'1\' title=\'Você atingiu o valor disponível para o reembolso anual do seu plano. Você excedeu o valor em R$' . $ndiffanualrefund . '\' style=\'background-color:#fdb9ae\'';} ?>><?php echo pmpro_formatPrice($ndiffanualrefund);?></td>
						<?php if ($availablecolor == ''){?>
							<td><a class='newrefundlink' href=<?php echo 'https://www.planpets.com.br/request-new-refund?procid='.$consult->ID.'&petid='.$consult->petID;?>>SOLICITAR REEMBOLSO</a></td>
						<?php }else{ ?>
							<td><?php echo $availablecolor;?></td>
					<?php } ?>
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
                <td valign="top" colspan="7" class="dataTables_empty">Nenhum procedimento encontrado.</td>
            </tr>
			<?php
		}
        ?>
        </table>
	
        <script type="text/javascript">
            $(document).ready(function() {										
                var consulttable = $('#pmpro_consults_table').DataTable({
					"info": false,
                    language: {
                    'search' : 'Buscar:',
                    "paginate": {
                        "previous": "Anterior",
                        "next": "Próxima"
                        },
                        "info": "Mostrando _START_ para _END_ de _TOTAL_ procedimentos"
                    },
                    "oLanguage": {
                        "sLengthMenu": "Mostrando _MENU_ procedimentos",
                    },
					"fnInitComplete": function ( oSettings ) {
						oSettings.oLanguage.sZeroRecords = "Procedimento não encontrada, por favor, solicite a adição do procedimento <a href=\"https://planpets.com.br/request-new-refund\">clicando aqui</a>"
					}
                });
				
				$('#petselector').change(function(){
					  consulttable.draw();
				})
				
								
				$('.pet_selector').change(function(){
					var linkElement = $(this).parent().parent().find('.newrefundlink').attr('href');
					var curpetid = $(this).val();
					$(this).parent().parent().find('.pet_selector_value').html(curpetid);
					if (linkElement){
						var newlink = linkElement.substring(0, linkElement.indexOf("petid=")) + "petid=" + curpetid;
						$(this).parent().parent().find('.newrefundlink').attr('href', newlink);
					}else{
						console.log('No Found Element');
					}
					
					
					var UpdateTD = $(this).parent().parent().find('.pet_selector_value');
					consulttable.cell( UpdateTD ).data( UpdateTD.html());
				});
				
            } );
			
			$.fn.dataTable.ext.search.push(
				function( settings, data, dataIndex ) {
					var petval = $('#petselector').val();
					var val = data[0];
					if (petval == 0){
						return true;
					}
					if ( petval == val )
					{
						return true;
					}
					return false;
				}
			);
			
			$("#wrapper").append('<!-- Modal --><div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="margin-top:100px;"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header" style="background:#64b5a2"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div></div></div></div>');

			$("#pmpro_consults_table").on('click','td',function() {
				var isclick = $(this).attr('isclick');
				if (isclick == '1'){
					var msg = $(this).attr('title');
// 					alert(msg);	
					$('#alertModal').find('.modal-body').html(msg);
    				$("#alertModal").modal();
					$("body").css({"padding-right":"0px"});
				}
        	});
			
        </script>
        <?php
    }
    ?>
<p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<span style="margin-top:20px;" class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-right' ); ?>"><a href="<?php echo pmpro_url("account")?>"><?php _e('View Your Membership Account &rarr;', 'paid-memberships-pro' );?></a></span>
	<?php if ( $pmpro_consult ) { ?>
		<span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-left' ); ?>"><a href="<?php echo pmpro_url("consult")?>"><?php _e('&larr; View All Consults', 'paid-memberships-pro' );?></a></span>
	<?php } ?>
</p> 
</div>

