<?php
    global $wpdb, $current_user;
    $proceduretablename = $wpdb->prefix . 'graceperiod_meta';
    $proceduredetailtablename = $wpdb->prefix . 'graceperiod_detail';
//     $proceduredatas = $wpdb->get_results("SELECT id, procedurename FROM $proceduretablename");

    $planpetdatas = $current_user->membership_levels;
	$procdata = array();
	if (!empty( $_REQUEST['procid'])){	
		$detailID = $_REQUEST['procid'];
		$petID = $_REQUEST['petid'];
    	$proceduredatas = $wpdb->get_results("SELECT t0.id, t0.procedurename FROM $proceduretablename t0 left join $proceduredetailtablename t1 on t1.meta_id = t0.id where t1.id=$detailID");
		foreach($proceduredatas as $proceduredata){
			$procdata = $proceduredata;
		}
	}
		
	
    if(isset($_POST['refund_save'])){
        $refund_tablename = $wpdb->prefix . 'refund';

        $serverinvoicefile = '';
        $serverreportfile = '';
        $serverpaymentfile = '';
        
        $serverinvoiceurl = '';
        $serverreporturl = '';
        $serverpaymenturl = '';

        $target_url = get_home_url() . '/upload/files';
        $target_dir = get_home_path() . 'upload/files';
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }


        $tmpinvoicefile = $_FILES['refund_invoicefile']["tmp_name"];
        $tmpreportfile = $_FILES['refund_reportfile']["tmp_name"];
        $tmppaymentfile = $_FILES['refund_paymentfile']["tmp_name"];
        
        if ($_FILES['refund_invoicefile']["name"] != ""){
            $serverinvoicefile = $target_dir . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_invoicefile']["name"];
            $serverinvoiceurl = $target_url . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_invoicefile']["name"];
            move_uploaded_file( $tmpinvoicefile, $serverinvoicefile);
        }
        
        if ($_FILES['refund_reportfile']["name"] != ""){
            $serverreportfile = $target_dir . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_reportfile']["name"];
            $serverreporturl = $target_url . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_reportfile']["name"];
            move_uploaded_file( $tmpreportfile, $serverreportfile);
        }
        
        if ($_FILES['refund_paymentfile']["name"] != ""){
            $serverpaymentfile = $target_dir . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_paymentfile']["name"];
            $serverpaymenturl = $target_url . '/' . $current_user->ID . '_' . date('YmdHis') . '_' . $_FILES['refund_paymentfile']["name"];
            move_uploaded_file( $tmppaymentfile, $serverpaymentfile);
        }

        $data = array(
            'membershipid' => $_POST['petplanid'],
            'plan' => $_POST['plan'],
            'petname' => $_POST['petname'],
            'consultation' => $_POST['refund_consultation'],
            'clinicname' => $_POST['refund_clinic'],
            'procedureid' => $_POST['refund_procedure'],
            'amount' => $_POST['refund_amount'],
            'accountbank' => $_POST['bankinfo'],
            'agency' => $_POST['agency'],
            'accountnumber' => $_POST['accountnumber'],
            'bankname' => $_POST['bankname'],
            'docnumber' => $_POST['cpf'],
            'clientname' => $_POST['refundname'],
            'invoicefilename' =>$serverinvoiceurl,
            'reportfilename' => $serverreporturl,
            'paymentfilename' => $serverpaymenturl,
            'refundstatus' => 1,
            'refunddate' => date('Y-m-d'),
            'userid' => $current_user->ID
            );

        $format = array('%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d');
        if(isset($_GET['id']) && !empty($_GET['id'])){
            //Update part
            $id = $_GET['id'];
            $where = array('id' => $_GET['id']);
            $data['dt_update'] = date('y-m-d H:i:s');
            $refundupdate = $wpdb->update($refund_tablename, $data, $where, $format);
            if ($refundupdate){
                $refundurl = pmpro_url("refund");
                header("Location: $refundurl");
            }else{
                echo "refund data not updated";
                $wpdb->show_errors();
                $wpdb->print_error();
                exit;
            }
        }else{
            //Insert part
            $data['dt_create'] = date('Y-m-d H:i:s');
            $refundinsert = $wpdb->insert($refund_tablename, $data, $format);
            
            $id = $wpdb->insert_id;
            if ($refundinsert){
                $refundurl = pmpro_url("refund");
                //header("Location: $refundurl");
				 echo "
					<script>
						window.location.href  = '$refundurl';
					</script>
				";
            }else{
                echo "refund data not inserted";
                $wpdb->show_errors();
                $wpdb->print_error();
                exit;
            }
        }
    }
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.60/inputmask/jquery.inputmask.js"></script>

<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.jquery.min.js"></script>
<style>
    .chosen-container-single .chosen-single {
        height:50px;
        display:flex;
        align-items:center;
        font-size:16px;
        padding-left:15px;
        color: #999;
        background:none;
    }
    
    a:hover{
        text-decoration:none;
    }
    /* .chosen-container-single .chosen-single div {
        display:none;
    } */

    .chosen-single.chosen-single-with-deselect .search-choice-close{
        display:none;
    }

    .chosen-container-single .chosen-single div b{
        background-position:0 15px;
    }

    .chosen-container-active.chosen-with-drop .chosen-single div b{
        background-position:-18px 15px;
    }

    #refund_save{
        background-color: #0f9e96;
        margin-top: 30px;
        margin-bottom: 15px;
        
        margin-bottom:15px;

        border-radius:0.25em;
    }

    .report_file_status, a.reportfile_download{
	    display:none;
    }

    .report_file_status.show, a.reportfile_download.show{
        display: block;
    }

    a.reportfile_download{
        margin-left:15px;
        background-color:#0f9e96;
        color:white;
    }

    .invoice_file_status, a.invoicefile_download{
        display:none;
    }

    .invoice_file_status.show, a.invoicefile_download.show{
        display: block;
    }

    a.invoicefile_download{
        margin-left:15px;
        background-color:#0f9e96;
        color:white;
    }

    .payment_file_status, a.paymentfile_download{
        display:none;
    }

    .payment_file_status.show, a.paymentfile_download.show{
        display: block;
    }

    a.paymentfile_download{
        margin-left:15px;
        background-color:#0f9e96;
        color:white;
    }

    .msg-hidden{
         display:none;
    }
	
	
/* 	.fusion-contact-info{
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

<div class="<?php echo pmpro_get_element_class( 'pmpro_add_new_refund' ); ?>">
    <form method='POST' action='' enctype="multipart/form-data" id="newrefund_form">

        <div class="row msg-hidden err_msg">
            <div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_error' . $pmpro_msgt, $pmpro_msgt ); ?>" style="width:100%;">
                Preencha todos os campos para continuar.
            </div>
        </div>
		
		
        <div class="row msg-hidden success_msg">
            <div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_success' . $pmpro_msgt, $pmpro_msgt ); ?>" style="width:100%;">
                Reembolso Solicitado com Sucesso.
            </div>
        </div>

        <div class="row">
            <h3 style="color:#0f9e96; margin-top:15px;">Solicitar Novo Reembolso</h3>
        </div>

        <div class="refund_petpart refund_part" style="margin-top:20px;">
            <div class='row'>
                <label for='refund_pet' class='title'>Selecione seu Pet</label>
            </div>
            <?php if (!empty( $_REQUEST['procid'])){
				foreach ($planpetdatas as $planpetdata){
				if ($planpetdata->subscription_id == $petID){?>
                <div class='row' style="margin-bottom:10px;">
                    <input type="radio" id="checkingpet" class="form-check-input pet-select" name="petinfo" value="<?php echo $planpetdata->subscription_id; ?>" style='margin-left:auto;' checked>
                    <div class="petname_div col-sm-3" style='border:1px solid gray; border-radius:5px;margin-right:10px;margin-left:20px;height:25px;'>
                    <span><?php echo $planpetdata->petname; ?></span>
                    </div>
                    <div class="petplan_div col-sm-3" style='border:1px solid gray; border-radius:5px;height:25px;'>
                    <span><?php 
                    switch ($planpetdata->id){
                        case '1':
                            echo 'slim';
                            break;
                        case '2':
                            echo 'comfort';
                            break;
                        case '3':
                            echo 'premium';
                            break;
                        default:
                            break;
                    }
                    ?></span>
                    </div>
                </div>
            <?php }}}else{
				foreach ($planpetdatas as $planpetdata){ ?>
				<div class='row' style="margin-bottom:10px;">
                    <input type="radio" id="checkingpet" class="form-check-input pet-select" name="petinfo" value="<?php echo $planpetdata->subscription_id; ?>" style='margin-left:auto;' checked>
                    <div class="petname_div col-sm-3" style='border:1px solid gray; border-radius:5px;margin-right:10px;margin-left:20px;height:25px;'>
                    <span><?php echo $planpetdata->petname; ?></span>
                    </div>
                    <div class="petplan_div col-sm-3" style='border:1px solid gray; border-radius:5px;height:25px;'>
                    <span><?php 
                    switch ($planpetdata->id){
                        case '1':
                            echo 'slim';
                            break;
                        case '2':
                            echo 'comfort';
                            break;
                        case '3':
                            echo 'premium';
                            break;
                        default:
                            break;
                    }
                    ?></span>
                    </div>
                </div>
			<?php }} ?>
            <div class='pet_realvalue'>
                <input type='hidden' name='petname' id='petname'>
                <input type='hidden' name='plan' id='petplan'>
                <input type='hidden' name='petplanid' id='petplanid'>
            </div>
        </div>
        
		<?php if (!empty( $_REQUEST['procid'])){ ?>
        <div class='refund_procedure_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_procedure' class='title'>Procedimento</label>
            </div>
			<div class='row'>
				<input type='text' name='refund_procedure_name' id='refund_procedure_name' value='<?php echo $procdata->procedurename; ?>' style="border-radius:10px;" readonly> 
				<input type='hidden' name='refund_procedure' id='refund_procedure' value='<?php echo $procdata->id; ?>' readonly> 
			</div>
        </div>
		<?php }else{ ?>
        <div class='refund_procedure_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_procedure' class='title'>Procedimento</label>
            </div>
			<div class='row'>
				<input type='text' name='refund_procedure_name' id='refund_procedure_name' value='Procedimento não encontrado na lista.' style="border-radius:10px;" readonly> 
				<input type='hidden' name='refund_procedure' id='refund_procedure' value='0' readonly> 
			</div>
        </div>
		<?php } ?>
        
        <div class='refund_consultation_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_consultation' class='title'>Data da Consulta</label>
            </div>
            <div class='row'>
                <input type='text' name='refund_consultation' id='refund_consultation' value='' placeholder='DD/MM/AAAA' style="border-radius:10px;">
            </div>
        </div>


        <div class='refund_clinic_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_clinic' class='title'>Nome da Clínica</label>
            </div>
            <div class='row'>
                <input type='text' name='refund_clinic' id='refund_clinic' value='' placeholder='Nome da Clínica' style="border-radius:10px;">
            </div>
        </div>

        
        <div class='refund_amount_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_amount' class='title'>Valor</label>
            </div>
            <div class='row'>
                <input type='number' name='refund_amount' id='refund_amount' step="0.01" value='' placeholder='Valor da Consulta' style="border-radius:10px;">
            </div>
        </div>

        <div class="refund_bank_part refund_part" style="margin-top:20px;">
            <div class='row'>
                <label for='refund_bank' class='title'>Dados Bancários</label> 
            </div>
            <div class='row'>
                <label class="form-check-label" for="checkingaccount" style="margin-left:20px;width:180px;">
                    <input type="radio" id="checkingaccount" class="form-check-input" name="bankinfo" value="1">
                    Conta Corrente
                </label>
                <label class="form-check-label" for="saving" style="width:180px;">
                    <input type="radio" id="saving" class="form-check-input" name="bankinfo" value="2">
                    Poupança
                </label>
            </div>
            <div class='row' style="margin-top:10px;">
                <div class="col-sm-2">
                    <input type='text' name='agency' id='refund_agency' value='' style='border-radius:10px' placeholder='Agência'>
                </div>
                <div class='col-sm-3'>
                    <input type='text' name='accountnumber' id='refund_accountnumber' value='' style='border-radius:10px' placeholder='Conta'>
                </div>
                <div class='col-sm-2'>
                    <input type='text' name='bankname' id='refund_bankname' value='' style='border-radius:10px' placeholder='Banco'>
                </div>
                <div class='col-sm-2'>
                    <input type='text' name='cpf' id='refund_cpf' value='' style='border-radius:10px' placeholder='CPF'>
                </div>
                <div class='col-sm-3'>
                    <input type='text' name='refundname' id='refund_name' value='' style='border-radius:10px' placeholder='Nome Completo'>
                </div>
            </div>
        </div>  
        
        <div class='refund_invoicefile_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_invoicefile' class='title'>Nota Fiscal</label>
            </div>
            <div class='row'>
                <!-- <a href="#" class="invoicefile_upload">Nota Fiscal</a>   -->
                <input type='file' name='refund_invoicefile' class='form-control-file' id='refund_invoicefile' value='' style='width:250px;'>
            </div>
            <div class='row'>
                <a class="btn invoicefile_download"><i class="fa fa-download"></i> Download</a>
                <label class='invoice_file_status' style='color:red;'>Not Selected</label>
            </div>
        </div>
        
        <div class='refund_reportfile_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_reportfile' class='title'>Laudo Veterinário</label>
            </div>
            <div class='row'>
                <!-- <a href="#" class="reportfile_upload">Laudo Veterinário</a>   -->
                <input type='file' name='refund_reportfile' class='form-control-file' id='refund_reportfile' value='' style='width:250px;'>
            </div>
            <div class='row'>
                <a class="btn reportfile_download"><i class="fa fa-download"></i> Download</a>
                <label class='report_file_status' style='color:red;'>Not Selected</label>
            </div>
        </div>
        
        <div class='refund_paymentfile_part refund_part' style="margin-top:20px;">
            <div class='row'>
                <label for='refund_paymentfile' class='title'>Comprovante de Pagamento</label>
            </div>
            <div class='row'>
                <!-- <a href="#" class="paymentfile_upload">Comprovante de Pagamento</a>   -->
                <input type='file' name='refund_paymentfile' class='form-control-file' id='refund_paymentfile' value='' style='width:250px;'>
            </div>
            <div class='row'>
                <a class="btn paymentfile_download"><i class="fa fa-download"></i> Download</a>
                <label class='payment_file_status' style='color:red;'>Not Selected</label>
            </div>
        </div>
        
        
        <div class="row">
            <input type="submit" value="Solicitar Novo Reembolso" name="refund_save" id="refund_save" class='btn btn-success'>
        </div>

        <div class="row msg-hidden err_msg">
            <div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_error' . $pmpro_msgt, $pmpro_msgt ); ?>" style="width:100%;">
                Preencha todos os campos para continuar.
            </div>
        </div>
		
		
        <div class="row msg-hidden success_msg">
            <div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_success' . $pmpro_msgt, $pmpro_msgt ); ?>" style="width:100%;">
                Reembolso Solicitado com Sucesso.
            </div>
        </div>
    </form>
</div>

<script>
	$(document).ready(function($){
		$("#refund_consultation").inputmask("99/99/9999");
		
        $("#petplanid").val($("input[name='petinfo']").val());
        $("#petplan").val($.trim($("input[name='petinfo']").siblings('.petplan_div').text()));
        $("#petname").val($.trim($("input[name='petinfo']").siblings('.petname_div').text()));
	})
	
    $("input[name='petinfo']").change(function(){
        $("#petplanid").val($(this).val());
        $("#petplan").val($.trim($(this).siblings('.petplan_div').text()));
        $("#petname").val($.trim($(this).siblings('.petname_div').text()));
    });

//     $(function(){
//         $("#refund_procedure").chosen({allow_single_deselect:true});
		
// 		if ($( "#refund_procedure option:selected" ).text() == ''){
// 			$('#toconsult').css({'display':'none'});
// 		}
//     });
	
	$('#refund_procedure').on('change', function(e){
		if ($( "#refund_procedure option:selected" ).text() != ''){
			$('#toconsult').css({'display':'initial'});
			$('#toconsult a').attr('href', 'https://www.planpets.com.br/consulta-de-carencias-de-procedimentos?procID=' + $('#refund_procedure').val());
		}
	})

    $("#newrefund_form").submit(function(e){
        $('.err_msg').addClass('msg-hidden');
				
        if ($('#petname').val() == '' || $('#refund_consultation').val() == '' ||
        $('#refund_clinic').val() == '' || $('#refund_amount').val() == '' || $('#refund_agency').val() == '' || $('#refund_accountnumber').val() == '' ||
        $('#refund_bankname').val() == '' || $('#refund_cpf').val() == '' || $('#refund_name').val() == '' || (!$('#checkingaccount').is(':checked') && !$('#saving').is(':checked'))){
            e.preventDefault();
            $('.err_msg').removeClass('msg-hidden');
        }else{
            $('.err_msg').addClass('msg-hidden');
			$('.success_msg').removeClass('msg-hidden');
        }
    });
    

</script>