
<?php
	$this->data['header'] = $this->t('{updater:updater:updater_header}');
	$this->includeAtTemplateBase('includes/header.php');


	$contentFeed = file_get_contents("https://packagist.org/feeds/package.simplesamlphp/simplesamlphp.rss");
	$xmlFeed = new SimpleXmlElement($contentFeed);   
	$itemsFeed = array();
   	foreach($xmlFeed->channel->item as $entryFeed) {
       array_push($itemsFeed, $entryFeed);
    }

	include('../../../lib/SimpleSAML/Configuration.php');
	$cfg = new SimpleSAML_Configuration(array(),array());
?>

<div>
    <p class="info"><?php echo $this->t('{updater:updater:updater_version}'); ?>: <strong><?php echo $cfg->getVersion(); ?></strong></p>
    <p class="info"><?php echo $this->t('{updater:updater:updater_seleciona_version}'); ?>: 
    	<select>
    		<?php foreach ($itemsFeed as $key => $value) { ?>
		    	<option value="<?php echo $value->title; ?>"><?php echo $value->title; ?></option>
		    <?php } ?>
    	</select>
    	<input value="<?php echo $this->t('{updater:updater:updater_actualizar_version}'); ?>" type="submit">
    </p>
</div>

<?php $this->includeAtTemplateBase('includes/footer.php'); ?>