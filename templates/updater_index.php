
<?php $this->data['header'] = $this->t('{updater:updater:updater_header}'); ?>
<?php $this->includeAtTemplateBase('includes/header.php'); ?>
    <style>

        div.input-container{
            width: 100%;
            max-width: 700px;
        }

        .loading-modal {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transform: scale(1.1);
            transition: visibility 0s linear 0.25s, opacity 0.25s 0s, transform 0.25s;
        }

        .show-modal {
            opacity: 1;
            visibility: visible;
            transform: scale(1.0);
            transition: visibility 0s linear 0s, opacity 0.25s 0s, transform 0.25s;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 1rem 1.5rem;
            width: 24rem;
            border-radius: 0.5rem;
        }

        #loader {
            border: 16px solid #f3f3f3; 
            border-top: 16px solid #3498db; 
            border-radius: 50%;
            width: 60px;
            height: 60px;
            margin: 0 auto;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>
    <div>

      <div id="breadcrumbs">
         <p><a href="<?php echo SimpleSAML\Module::getModuleURL('core/frontpage_config.php'); ?>"><?php echo $this->t('{updater:updater:updater_breadcrumbs_config}'); ?></a> -> <?php echo $this->t('{updater:updater:updater_breadcrumbs_updater}'); ?></p>
      </div>
    
      <?php 

          if (count($this->data['sir']['errors']) > 0) {
                $img_error = "/resources/icons/experience/gtk-dialog-error.48x48.png";
                echo '<div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5;">';
                echo '  <img style="margin-right: 10px;margin-left: 5px;" class="float-l erroricon" src='.$img_error.'>';
                echo '  <p style="padding-top: 5px " >'.implode("<br/>", $this->data['sir']['errors']) . '</p>';
                echo '</div>';               
          }

      ?>

    

    <fieldset class="fancyfieldset">
       <legend><?php echo $this->t('{updater:updater:updater_title_info}'); ?></legend> 
       <p style="margin: 1em 2em;"><?php echo $this->t('{updater:updater:updater_p1_info}'); ?></p>
       <p style="margin: 1em 2em;"><?php echo $this->t('{updater:updater:updater_p2_info}'); ?></p>
       
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_version}'); ?>:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" style="width: 100px; text-align: right; " value="<?php echo $this->data['sir']['currentVersion']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_path}'); ?>:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" style="width:300px; text-align: right" value="<?php echo $this->data['sir']['backupPath']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_latestbackup}'); ?>:</label>
               </div>
               <div class="float-r">
                   <input id="lastBackup" readonly="readonly" style="width:300px; text-align: right;" value="<?php echo ($this->data['sir']['latestBackup']==null ? $this->t('{updater:updater:updater_no_backups}') : $this->data['sir']['latestBackup']); ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
       </div>
    </fieldset>

     <fieldset class="fancyfieldset">
       <legend><?php echo $this->t('{updater:updater:updater_title_actualizacion}'); ?></legend> 
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_versiones_disponibles}'); ?>:</label>
               </div>
               <div class="float-r">
                   <select>
                       <?php foreach ($this->data['sir']['versions'] as $key => $value) { ?>
                            <option value="<?php echo $value->title; ?>"><?php echo $value->title; ?></option>
                        <?php } ?>
                   </select>
               </div>
               <div style="clear: both;"></div>
           </div>
           <div>
               <input type="submit" value="<?php echo $this->t('{updater:updater:updater_btn_update}'); ?>"/>
           </div>
           <div>
               <p><?php echo $this->t('{updater:updater:updater_versiones_nota}'); ?></p>
           </div>
        </div>
     </fieldset>

     <fieldset class="fancyfieldset">
       <legend><?php echo $this->t('{updater:updater:updater_title_backups}')?></legend> 
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t("{updater:updater:updater_list_backups}"); ?>:</label>
               </div>
               <div class="float-l">
                    <select id="backups">
                        <?php foreach ($this->data['sir']['backups'] as $key => $value) { ?>
                            <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
               </div>
               <div style="clear: both;"></div>
           </div>
           <div>
                <form id="form-backup" name="form-backup" method="POST">
                    <input type="hidden" value="backup" name="hook"/>
                    <input type="button" onclick="create_backup();" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_backup}'); ?>"/>
                </form>
           </div>
           <div>
                <form id="form-restore" name="form-restore" method="POST">
                    <input type="hidden" value="restore" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_restore" id="selected_backup_restore"/>
                    <input type="button" onclick="restore_backup();" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_restore}'); ?>"/>
                </form>
           </div>
           <div>
                <form id="form-delete" name="form-delete" method="POST">
                    <input type="hidden" value="delete" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_delete" id="selected_backup_delete"/>
                    <input type="button" onclick="delete_backup();" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_delete}'); ?>"/>
                </form>
           </div>
        </div>
    </fieldset>
        
</div>

<div id="modal" class="loading-modal">
    <div class="modal-content">
        <div id="status-msg"></div>
        <div id="loader-msg">Espere unos segundos</div>
        <div id="loader"></div>
        <div>
          <input type="button" onclick="toggleModal();" name="close_modal" value="Aceptar"/>
        </div>
    </div>
</div>



<script>

    var modal = document.getElementById("modal");

    function create_backup(){

        $("#status-msg").html("");

        $.ajax({
            type: "GET",
            url: "create_backup.php",
            data: $('#form-backup').serialize(),
            dataType: "json",
            beforeSend: function() {
                toggleModal();
                $('#loader, #loader-msg').show();
            },
            complete: function() {
                $('#loader, #loader-msg').hide();
            },
            success: function(data) {
                console.log(data);
                if(data.error==1){
                    for(var i=0; i<data.errors.length; i++){
                        $("#status-msg").append(data.errors[i]);
                        $("#status-msg").append("<br/>");
                    }
                }else{
                    $("#status-msg").text("Copia de seguridad creada correctamente");
                    reloadLastBackup(data.lastBackup);
                    reloadBackupList(data.backups);
                }

            },
            error: function() {
                console.log("No se ha podido obtener la información");
            }
        });
    }

    function delete_backup(){

        $("#status-msg").html("");

        if (window.confirm("<?php echo $this->t('{updater:updater:updater_confirm_dialog}'); ?>")) { 

            document.getElementById("selected_backup_delete").value = document.getElementById("backups").value;

            $.ajax({
                type: "GET",
                url: "delete_backup.php",
                data: $('#form-delete').serialize(),
                dataType: "json",
                beforeSend: function() {
                    toggleModal();
                    $('#loader, #loader-msg').show();
                },
                complete: function() {
                    $('#loader, #loader-msg').hide();
                },
                success: function(data) {

                    if(data.error==1){
                        for(var i=0; i<data.errors.length; i++){
                            $("#status-msg").append(data.errors[i]);
                            $("#status-msg").append("<br/>");
                        }
                    }else{
                        $("#status-msg").text("Copia de seguridad eliminada correctamente");
                        reloadLastBackup(data.lastBackup);
                        reloadBackupList(data.backups);
                    }
                    
                },
                error: function() {
                    console.log("No se ha podido obtener la información");
                }
            });

            return true;

        }else{

            return false;

        }

    }

    function restore_backup(){

        document.getElementById("selected_backup_restore").value = document.getElementById("backups").value;

        $("#status-msg").html("");

        $.ajax({
            type: "GET",
            url: "restore_backup.php",
            data: $('#form-restore').serialize(),
            dataType: "json",
            beforeSend: function() {
                toggleModal();
                $('#loader, #loader-msg').show();
            },
            complete: function() {
                $('#loader, #loader-msg').hide();
            },
            success: function(data) {

                if(data.error==1){
                    for(var i=0; i<data.errors.length; i++){
                        $("#status-msg").append(data.errors[i]);
                        $("#status-msg").append("<br/>");
                    }
                }else{
                    $("#status-msg").text("Copia de seguridad restaurada correctamente");
                }
                
            },
        });

    }

    function toggleModal() {
        modal.classList.toggle("show-modal");
    }

    function windowOnClick(event) {
        if (event.target === modal) {
            toggleModal();
        }
    }

    function reloadLastBackup(lastBackup){
        $("#lastBackup").val(lastBackup);
    }

    function reloadBackupList(backupList){
        $('#backups').find('option').remove();

        for(var i=0; i<backupList.length; i++){
            $('#backups').append('<option value="' + backupList[i] + '">' + backupList[i] + '</option>');  
        }
        
    }

    window.addEventListener("click", windowOnClick);

</script>

<?php $this->includeAtTemplateBase('includes/footer.php'); ?>