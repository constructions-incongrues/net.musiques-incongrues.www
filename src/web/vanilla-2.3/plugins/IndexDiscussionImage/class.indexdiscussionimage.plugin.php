<?php if (!defined('APPLICATION')) exit();
$PluginInfo['IndexDiscussionImage'] = array(
   'Name' => 'IndexDiscussionImage',
   'Description' => "Adds the linked Images from the Discussions to the Discussions Index as a Sampler and provides a tooltip to preview the content.",
   'Version' => '2.1',
   'RegisterPermissions' => "Garden.Settings.Manage",
   'Author' => "VrijVlinder",
   'AuthorEmail' => 'contact@gvrijvlinder.com',
   'AuthorUrl' => 'http://vrijvlinder.com',
   'License'=>"GNU GPL2"
);
class IndexDiscussionImagePlugin extends Gdn_Plugin {
   /**
    * Add style sheet.
    */
   public function Base_Render_Before($Sender) {
      $Sender->AddCssFile($this->GetResource('design/idi.css', FALSE, FALSE));
   }
   // Trigger on All Discussions.
   public function DiscussionsController_BeforeDiscussionContent_Handler($Sender) {
        $CssItem = $Sender->EventArguments['CssClass'];
        $CssItem = str_replace("Bookmarked"," ",$CssItem);
        $bodyLine = $Sender->EventArguments['Discussion']->Body;    
        $formline = strip_tags(str_replace(array('[',']'), array('<','>'), $bodyLine));   
        $sline = substr($formline, 0, 220) . "..." ;
        $oldName = $Sender->EventArguments['Discussion']->Name;
        $oldUrl = $Sender->EventArguments['Discussion']->Url;
        $ImageSrc = C('Plugin.IndexDiscussionImage.Image','/plugins/IndexDiscussionImage/design/images/default.png');
        preg_match('#\<img.+?src="([^"]*).+?\>#s', $Sender->EventArguments['Discussion']->Body, $images);
        if ($images[1]) {
            $ImageSrc = $images[1];
        }
        $newTitleAnchor  = '<a class="IndexImage" href="' . $Sender->EventArguments['Discussion']->Url  . '">' .  Img($ImageSrc,  array('title' => $sline, 'class' => "IndexImage")) . '</a>';
       echo "$newTitleAnchor";
   }
 
 //show in the specific category discussions list
   public function CategoriesController_BeforeDiscussionContent_Handler($Sender) {
        $CssItem = $Sender->EventArguments['CssClass'];
        $CssItem = str_replace("Bookmarked"," ",$CssItem);
        $bodyLine = $Sender->EventArguments['Discussion']->Body;    
        $formline = strip_tags(str_replace(array('[',']'), array('<','>'), $bodyLine));   
        $sline = substr($formline, 0, 220) . "..." ;
        $oldName = $Sender->EventArguments['Discussion']->Name;
        $oldUrl = $Sender->EventArguments['Discussion']->Url;
        $ImageSrc = C('Plugin.IndexDiscussionImage.Image','/plugins/IndexDiscussionImage/design/images/default.png');
        preg_match('#\<img.+?src="([^"]*).+?\>#s', $Sender->EventArguments['Discussion']->Body, $images);
        if ($images[1]) {
            $ImageSrc = $images[1];
        }
        $newTitleAnchor  = '<a class="IndexImage" href="' . $Sender->EventArguments['Discussion']->Url  . '">' .  Img($ImageSrc,  array('title' => $sline, 'class' => "IndexImage")) . '</a>';
       echo "$newTitleAnchor";
   }

   public function Base_GetAppSettingsMenuItems_Handler($Sender) {
      $Menu = $Sender->EventArguments['SideMenu'];
      $Menu->AddLink('Add-ons', 'IndexDiscussionImage', 'plugin/IndexDiscussionImage', 'Garden.Settings.Manage');
   }
 

  public function PluginController_IndexDiscussionImage_Create($Sender) {
         $Sender->Title('IndexDiscussionImage Plugin');
         $Sender->AddSideMenu('plugin/IndexDiscussionImage');
         $Sender->Form = new Gdn_Form();
        
         $this->Dispatch($Sender, $Sender->RequestArgs);
  }
    
   
  public function Controller_Index($Sender) {      
        
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->SetData('PluginDescription',$this->GetPluginKey('Description'));
        
      
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugin.IndexDiscussionImage.Image'=> '/plugins/IndexDiscussionImage/design/images/default.png',
            
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('idi-settings.php'));
   }
  
 
  public function Setup() {
      SaveToConfig('Plugin.IndexDiscussionImage.Image', '/plugins/IndexDiscussionImage/design/images/default.png');
     
  }





}