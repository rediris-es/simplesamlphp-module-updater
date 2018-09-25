
<?php $this->data['header'] = $this->t('{updater:updater:updater_header}'); ?>
<?php $this->includeAtTemplateBase('includes/header.php'); ?>
    <style>
        div.input-container{
            width: 100%;
            max-width: 700px;
        }
    </style>
    <div>
    <?php 

        if(isset($this->data['sir']['success'])){
            $img_success = "/resources/icons/checkmark.48x48.png";
            echo '<div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #a5fc53;">';
            echo '  <img style="margin-right: 10px;margin-left: 5px;" class="float-l erroricon" src='.$img_success.'>';
            echo '  <p style="padding-top: 5px " >'.$this->data['sir']['success']. '</p>';
            echo '</div>';   
        } else if (count($this->data['sir']['errors']) > 0) {
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
                   <input readonly="readonly" value="<?php echo $this->data['sir']['currentVersion']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_path}'); ?>:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" value="<?php echo $this->data['sir']['backupPath']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label><?php echo $this->t('{updater:updater:updater_latestbackup}'); ?>:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" style="width:300px;" value="<?php echo $this->data['sir']['latestBackup']->filename; ?>">
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
                            <option value="<?php echo $value->filename; ?>"><?php echo $value->name; ?></option>
                        <?php } ?>
                    </select>
               </div>
               <div style="clear: both;"></div>
           </div>
           <div>
                <form id="form-backup" name="form-backup" method="POST">
                    <input type="hidden" value="backup" name="hook"/>
                    <input type="submit" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_backup}'); ?>"/>
                </form>
           </div>
           <div>
                <form id="form-restore" name="form-restore" method="POST" onsubmit="return restore_backup();">
                    <input type="hidden" value="restore" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_restore" id="selected_backup_restore"/>
                    <input type="submit" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_restore}'); ?>"/>
                </form>
           </div>
           <div>
                <form id="form-delete" name="form-delete" method="POST" onsubmit="return delete_backup();">
                    <input type="hidden" value="delete" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_delete" id="selected_backup_delete"/>
                    <input type="submit" name="send_form" value="<?php echo $this->t('{updater:updater:updater_btn_delete}'); ?>"/>
                </form>
           </div>
        </div>
    </fieldset>
        
</div>

<script>

    function delete_backup(){

        if (window.confirm("<?php echo $this->t('{updater:updater:updater_confirm_dialog}'); ?>")) { 
            document.getElementById("selected_backup_delete").value = document.getElementById("backups").value;
            return true;
        }else{
            return false;
        }

    }

    function restore_backup(){

        document.getElementById("selected_backup_restore").value = document.getElementById("backups").value;

        return true;

    }

</script>

<?php $this->includeAtTemplateBase('includes/footer.php'); ?>