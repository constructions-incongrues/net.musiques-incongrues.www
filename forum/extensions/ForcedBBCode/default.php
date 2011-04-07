<?php
/*
Extension Name: Forced BBCode
Extension Url: #
Description: Force an install of Vanilla into BBCode only mode and add a BBCode editor bar
Version: 0.1.0
Author: Jeff Minard
Author Url: http://jrm.cc/
*/

// some definitions
$Context->Dictionary['bbbold'] = 'Bold';
$Context->Dictionary['bbitalic'] = 'Italic';
$Context->Dictionary['bbunderline'] = 'Underline';
$Context->Dictionary['bbstrikethrough'] = 'Strikethrough';
$Context->Dictionary['bbalignleft'] = 'Align Left';
$Context->Dictionary['bbaligncenter'] = 'Align Center';
$Context->Dictionary['bbalignright'] = 'Align Right';
$Context->Dictionary['bbcode'] = 'Code';
$Context->Dictionary['bbcolor'] = 'Font Color';
$Context->Dictionary['bbfont'] = 'Font Face';
$Context->Dictionary['bbimage'] = 'Insert Image';
$Context->Dictionary['bblist'] = 'List';
$Context->Dictionary['bbolist'] = 'Ordered List';
$Context->Dictionary['bbquote'] = 'Blockquote';
$Context->Dictionary['bbsub'] = 'Sub';
$Context->Dictionary['bbsup'] = 'Sup';
$Context->Dictionary['bburl'] = 'Insert Link';
$Context->Dictionary['bbmail'] = 'Insert Mail Link';

// set the default formatter to BBCode
$Context->Configuration['DEFAULT_FORMAT_TYPE'] = 'BBCode';

// by only having the BBCode option, you force all other formats to be parsed as BBCode
$Context->Configuration['FORMAT_TYPES'] = array('BBCode'); 

// overwrite a user preference for showing/hiding the format selector radio buttons
$Context->Session->User->Preferences['ShowFormatSelector'] = 0;

if (in_array($Context->SelfUrl, array("post.php", "comments.php"))) {

	class ForcedBBBar {
		var $Name;
		var $Context;
		
		function ForcedBBBar(&$Context) {
			$this->Name = "ForcedBBBar";
			$this->Context = &$Context;
		}
		function ForcedBBBar_Create() {
		
			$path = $this->Context->Configuration["BASE_URL"].'extensions/ForcedBBCode/';
			
			// get us a spot in the list
			echo '<li><div id="ForcedBBBar">';
			
			// base controls
			?>
			
			<a onclick="insert('b','/b')"><img src="<?php echo $path ?>buttons/text_bold.gif" alt="<?php echo $this->Context->GetDefinition('bbbold') ?>" title="<?php echo $this->Context->GetDefinition('bbbold') ?>" /></a>
			<a onclick="insert('i','/i')"><img src="<?php echo $path ?>buttons/text_italic.gif" alt="<?php echo $this->Context->GetDefinition('bbitalic') ?>" title="<?php echo $this->Context->GetDefinition('bbitalic') ?>" /></a>
			<a onclick="insert('u','/u')"><img src="<?php echo $path ?>buttons/text_underline.gif" alt="<?php echo $this->Context->GetDefinition('bbunderline') ?>" title="<?php echo $this->Context->GetDefinition('bbunderline') ?>" /></a>
			<a onclick="insert('s','/s')"><img src="<?php echo $path ?>buttons/text_strikethrough.gif" alt="<?php echo $this->Context->GetDefinition('bbstrikethrough') ?>" title="<?php echo $this->Context->GetDefinition('bbstrikethrough') ?>" /></a>
			<a onclick="insert('align=left','/align')"><img src="<?php echo $path ?>buttons/text_align_left.gif" alt="<?php echo $this->Context->GetDefinition('bbalignleft') ?>" title="<?php echo $this->Context->GetDefinition('bbalignleft') ?>" /></a>
			<a onclick="insert('align=center','/align')"><img src="<?php echo $path ?>buttons/text_align_center.gif" alt="<?php echo $this->Context->GetDefinition('bbaligncenter') ?>" title="<?php echo $this->Context->GetDefinition('bbaligncenter') ?>" /></a>
			<a onclick="insert('align=right','/align')"><img src="<?php echo $path ?>buttons/text_align_right.gif" alt="<?php echo $this->Context->GetDefinition('bbalignright') ?>" title="<?php echo $this->Context->GetDefinition('bbalignright') ?>" /></a>
			<a onclick="showColor()"><img src="<?php echo $path ?>buttons/text_color.gif" alt="<?php echo $this->Context->GetDefinition('bbcolor') ?>" title="<?php echo $this->Context->GetDefinition('bbcolor') ?>" /></a>
			<a onclick="showFont()"><img src="<?php echo $path ?>buttons/text_font.gif" alt="<?php echo $this->Context->GetDefinition('font') ?>" title="<?php echo $this->Context->GetDefinition('bbfont') ?>" /></a>
			<a onclick="insertURL()"><img src="<?php echo $path ?>buttons/text_url.gif" alt="<?php echo $this->Context->GetDefinition('bburl') ?>" title="<?php echo $this->Context->GetDefinition('bburl') ?>" /></a>

			<p>
			Si vous souhaitez insérer du contenu comme des vidéos, des images ou des mp3s ce n'est pas compliqué :<br />mettez juste le lien, on se charge de l'afficher dans le player qui va bien (cf. la <a title="Consulter la Foire aux Questions" href="<?php echo $Context->Configuration['WEB_ROOT']?>page/faq">FAQ</a>).
			</p>
			
			<?php
			
			// color picker
			echo '<div id="ForcedBBBarColorPicker" style="display:none;">';
			$array = array('CCFF','CCCC','CC99','CC66','CC33','CC00','6600','6633','6666','6699','66CC','66FF','00FF','00CC','0099','0066','0033','0000');
			$array2 = array('FF','CC','99','66','33','00');
			$zeile = 1;
			while ($zeile <= 6) {
				$spalte = 1;
				while ($spalte <= 18) {
					$i = $spalte -1;
					$j = $zeile - 1;
					$col = substr($array[$i],0,2).$array2[$j].substr($array[$i],2,2);
					echo '<div onclick="writeColor(\''.$col.'\'); " title="#'.$col.'" style="cursor:pointer; position:absolute; left:'.(10+($spalte-1)*10).'px; top:'.(($zeile-1)*10).'px; width:9px; height:9px; border:1px solid #000; background-color:#'.$col.'">&nbsp;</div>';
					$spalte++;	
				}
				$zeile++;
			}
			$array = array('FFFF','FFCC','FF99','FF66','FF33','FF00','9900','9933','9966','9999','99CC','99FF','33FF','33CC','3399','3366','3333','3300');
			$array2 = array('FF','CC','99','66','33','00');
			$zeile = 7;
			while ($zeile <= 12) {
				$spalte = 1;
				while ($spalte <= 18) {
					$i = $spalte -1;
					$j = 12 - $zeile;
					$col = substr($array[$i],0,2).$array2[$j].substr($array[$i],2,2);
					echo '<div onclick="writeColor(\''.$col.'\');" title="#'.$col.'" style="cursor:pointer; position:absolute; left:'.(10+($spalte-1)*10).'px; top:'.(($zeile-1)*10).'px; width:9px; height:9px; border:1px solid #000; background-color:#'.$col.';">&nbsp;</div>';
					$spalte++;	
				}
				$zeile++;
			}
			$array = array('000000','333333','666666','999999','CCCCCC','FFFFFF','FF0000','00FF00','0000FF','FFFF00','00FFFF','FF00FF');
			$zeile = 1;
			while ($zeile <= 12) {
				$i = $zeile -1;
				$col = $array[$i];
				echo '<div onclick="writeColor(\''.$col.'\');" title="#'.$col.'" style="cursor:pointer; position:absolute; left:0px; top:'.(($zeile-1)*10).'px; width:9px; height:9px; border:1px solid #000; background-color:#'.$col.';">&nbsp;</div>';
				$zeile++;
			}		
			echo '</div>';
			
			// font picker
			echo '<div id="ForcedBBBarFontPicker" style="display:none;">';	
			$array = array('Arial','Helvetica','Times New Roman','Times','Courier New','Courier','Georgia','Geneva');
			foreach ($array AS $value) {
				echo '<a class="ForcedBBBarFontList" onclick="writeFont(\''.$value.'\');" title="'.$value.'">&nbsp;&bull;&nbsp;<span style="font-family: '.$value.'">'.$value.'</span></a>';
			}
			echo'</div>';
			
			// close it up
			echo '</div></li>';
		}

	}
	
	$Head->AddStyleSheet('extensions/ForcedBBCode/style.css');
	$Head->AddScript('extensions/ForcedBBCode/functions.js');

	function AddForcedBBCodetoCommentForm(&$DiscussionForm) {
		$ForcedBBBar = new ForcedBBBar($DiscussionForm->Context);
		$ForcedBBBar->ForcedBBBar_Create();
	}
	
	if( $Context->Session->UserID > 0 ) {
		$Context->AddToDelegate('DiscussionForm', 'CommentForm_PreCommentsInputRender', 'AddForcedBBCodetoCommentForm');
		$Context->AddToDelegate('DiscussionForm', 'DiscussionForm_PreCommentRender',    'AddForcedBBCodetoCommentForm');
	}
	
}

// remove the preference for showing/hiding the "Show Format Selector"
if( $Context->SelfUrl == 'account.php' ) {
	function PreferencesForm_RemoveSFS(&$PreferencesForm) {
		unset($PreferencesForm->Preferences['CommentsForm']);
	}
	$Context->AddToDelegate('PreferencesForm', 'PreRender', 'PreferencesForm_RemoveSFS');
}

require_once('BBCodeParser.php');

class ForcedBetterBBCodeFormatter extends StringFormatter {

	function Parse($String, $Object, $FormatPurpose) {
		$parser = new HTML_BBCodeParser();
		
		if ($FormatPurpose == FORMAT_STRING_FOR_DISPLAY) {
			$String = $this->ProtectString($String);
			$String = $parser->qparse($String);
			$String = $this->wpautop($String);
		}
		
		return $String;
	}
		
	function ProtectString ($String) {
		$String = str_replace(array('<','>'), array('&lt;','&gt;'), $String);
		return $String;
	}
	
	// graciously borrowed from wordpress :D
	function wpautop($pee, $br = 1) {
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|style|script|object|input|param|p|h[1-6])';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
		if ($br) {
			$pee = preg_replace('/<(script|style).*?<\/\\1>/se', 'str_replace("\n", "<WPPreserveNewline />", "\\0")', $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if ( strstr( $pee, '<pre' ) )
			$pee = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' ", $pee);
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
		return $pee;
	}
	
}

// Instantiate the bbcode object and add it to the string manipulation methods
$ForcedBetterBBCodeFormatter = $Context->ObjectFactory->NewObject($Context, "ForcedBetterBBCodeFormatter");
$Context->StringManipulator->AddManipulator("BBCode", $ForcedBetterBBCodeFormatter);

?>