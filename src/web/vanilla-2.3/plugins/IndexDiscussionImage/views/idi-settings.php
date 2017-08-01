<?php if (!defined('APPLICATION')) exit(); ?>


<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
   <?php echo T($this->Data['PluginDescription']); ?>
   You can add a default image url in case there is no image in a discussion for example your logo. </br>It is recomended that your default image is no larger than 60px.
</div>
<h3><?php echo T('Settings'); ?></h3>
<?php
   echo $this->Form->Open();
   echo $this->Form->Errors();
?>
<ul>
   <li><?php
      
      echo $this->Form->Label('Url to your Default Image goes here', 'Plugin.IndexDiscussionImage.Image');
	  echo $this->Form->TextBox('Plugin.IndexDiscussionImage.Image');
	  
   ?></li>
</ul>


<?php
   echo $this->Form->Close('Save');
