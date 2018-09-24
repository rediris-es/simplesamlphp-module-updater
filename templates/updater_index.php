
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
       <legend>Información sobre este módulo</legend> 
       <p style="margin: 1em 2em;">Este módulo muestra información sobre la versión actualmente instalada del software, y permite realizar y restaurar copias de seguridad sobre su instancia de simpleSAMLphp.</p>
       <p style="margin: 1em 2em;">Por favor, lea la información sobre el uso de este módulo, y tome precauciones antes de hacer una actualización. Si su instalación depende de bases de datos, realice copia de seguridad de las mismas.</p>
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label>Versión actualmente instalada:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" value="<?php echo $this->data['sir']['currentVersion']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label>Ruta donde se almacenan los backups:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" value="<?php echo $this->data['sir']['backupPath']; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
           <div class="input-container">
               <div class="float-l">
                   <label>Última copia de seguridad disponible:</label>
               </div>
               <div class="float-r">
                   <input readonly="readonly" style="width:300px;" value="<?php echo $this->data['sir']['latestBackup']->filename; ?>">
               </div>
               <div style="clear: both;"></div>
           </div>
       </div>
    </fieldset>

     <fieldset class="fancyfieldset">
       <legend>Actualización</legend> 
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label>Versiones disponibles para actualizar:</label>
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
               <input type="submit" value="Actualizar a la versión seleccionada"/>
           </div>
           <div>
               <p>(NOTA: la actualización no funcionará si no se han realizado copias de seguridad en los últimos 5 minutos)</p>
           </div>
        </div>
    </fieldset>

     <fieldset class="fancyfieldset">
       <legend>Copias de seguridad</legend> 
       <div style="margin: 1em 2em;">
           <div class="input-container">
               <div class="float-l">
                   <label>Copias de seguridad anteriores disponibles:</label>
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
                    <input type="submit" name="send_form" value="Realizar una nueva copia de seguridad"/>
                </form>
           </div>
            <div>
                <form id="form-restore" name="form-restore" method="POST" onsubmit="return restore_backup();">
                    <input type="hidden" value="restore" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_restore" id="selected_backup_restore"/>
                    <input type="submit" name="send_form" value="Restaurar la copia de seguridad seleccionada"/>
                </form>
           </div>
            <div>
                <form id="form-delete" name="form-delete" method="POST" onsubmit="return delete_backup();">
                    <input type="hidden" value="delete" name="hook"/>
                    <input type="hidden" value="" name="selected_backup_delete" id="selected_backup_delete"/>
                    <input type="submit" name="send_form" value="Eliminar la copia de seguridad seleccionada"/>
                </form>
           </div>
        </div>
    </fieldset>
        
</div>

<script>

function delete_backup(){

    if (window.confirm("¿Estás seguro de que quieres eliminar la copia de seguridad seleccionada?")) { 
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