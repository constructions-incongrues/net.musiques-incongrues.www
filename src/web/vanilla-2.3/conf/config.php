<?php if (!defined('APPLICATION')) exit();

// Conversations
$Configuration['Conversations']['Version'] = '2.3';

// Database
$Configuration['Database']['Name'] = 'net_musiquesincongrues_www_vanilla2';
$Configuration['Database']['Host'] = 'localhost';
$Configuration['Database']['User'] = 'root';
$Configuration['Database']['Password'] = 'root';

// Discussants
$Configuration['Discussants']['MaxVisible'] = 20;

// EnabledApplications
$Configuration['EnabledApplications']['Conversations'] = 'conversations';
$Configuration['EnabledApplications']['Vanilla'] = 'vanilla';

// EnabledPlugins
$Configuration['EnabledPlugins']['GettingStarted'] = false;
$Configuration['EnabledPlugins']['HtmLawed'] = 'HtmLawed';
$Configuration['EnabledPlugins']['Tagging'] = false;
$Configuration['EnabledPlugins']['AllViewed'] = true;
$Configuration['EnabledPlugins']['ButtonBar'] = false;
$Configuration['EnabledPlugins']['cleditor'] = false;
$Configuration['EnabledPlugins']['Emotify'] = false;
$Configuration['EnabledPlugins']['FileUpload'] = false;
$Configuration['EnabledPlugins']['editor'] = false;
$Configuration['EnabledPlugins']['VanillaInThisDiscussion'] = true;
$Configuration['EnabledPlugins']['Quotes'] = false;
$Configuration['EnabledPlugins']['EmojiExtender'] = false;
$Configuration['EnabledPlugins']['oembed'] = true;
$Configuration['EnabledPlugins']['DiscussionExtender'] = false;
$Configuration['EnabledPlugins']['Hashtag'] = false;
$Configuration['EnabledPlugins']['GooglePlus'] = false;
$Configuration['EnabledPlugins']['Facebook'] = true;
$Configuration['EnabledPlugins']['OpenGraph'] = false;
$Configuration['EnabledPlugins']['creator'] = false;
$Configuration['EnabledPlugins']['DateJumper'] = false;
$Configuration['EnabledPlugins']['Discussants'] = false;
$Configuration['EnabledPlugins']['FilterDiscussion'] = false;
$Configuration['EnabledPlugins']['InfiniteScroll'] = true;
$Configuration['EnabledPlugins']['IndexDiscussionImage'] = false;
$Configuration['EnabledPlugins']['quotemention'] = false;
$Configuration['EnabledPlugins']['timeago'] = true;
$Configuration['EnabledPlugins']['UsefulFunctions'] = false;
$Configuration['EnabledPlugins']['Timeago'] = false;
$Configuration['EnabledPlugins']['UnsubscribeDiscussion'] = true;
$Configuration['EnabledPlugins']['MarkDiscussionRead'] = true;
$Configuration['EnabledPlugins']['AutoLink'] = true;
$Configuration['EnabledPlugins']['DiscussionTypes'] = true;
$Configuration['EnabledPlugins']['DiscussionType_Event'] = true;

// Garden
$Configuration['Garden']['Title'] = 'Musiques Incongrues';
$Configuration['Garden']['Cookie']['Salt'] = '7bzqG8miHthIqDWb';
$Configuration['Garden']['Cookie']['Domain'] = '';
$Configuration['Garden']['Registration']['ConfirmEmail'] = false;
$Configuration['Garden']['Registration']['Method'] = 'Approval';
$Configuration['Garden']['Registration']['InviteExpiration'] = '1 week';
$Configuration['Garden']['Registration']['CaptchaPrivateKey'] = '6Lfj8c0SAAAAAMlVi6uxjO80rJ_xlvnGClnUXEvx';
$Configuration['Garden']['Registration']['CaptchaPublicKey'] = '6Lfj8c0SAAAAAMlVi6uxjO80rJ_xlvnGClnUXEvx';
$Configuration['Garden']['Registration']['InviteRoles']['3'] = '0';
$Configuration['Garden']['Registration']['InviteRoles']['4'] = '0';
$Configuration['Garden']['Registration']['InviteRoles']['8'] = '0';
$Configuration['Garden']['Registration']['InviteRoles']['16'] = '0';
$Configuration['Garden']['Registration']['InviteRoles']['32'] = '0';
$Configuration['Garden']['Email']['SupportName'] = 'Musiques Incongrues';
$Configuration['Garden']['Email']['Format'] = 'text';
$Configuration['Garden']['InputFormatter'] = 'Markdown';
$Configuration['Garden']['Version'] = '2.3';
$Configuration['Garden']['Cdns']['Disable'] = false;
$Configuration['Garden']['CanProcessImages'] = true;
$Configuration['Garden']['Installed'] = true;
$Configuration['Garden']['Theme'] = 'default';
$Configuration['Garden']['MobileInputFormatter'] = 'TextEx';
$Configuration['Garden']['AllowFileUploads'] = true;
$Configuration['Garden']['Format']['Hashtags'] = false;
$Configuration['Garden']['Analytics']['AllowLocal'] = true;

// Modules
$Configuration['Modules']['Vanilla']['Panel'] = array('MeModule', 'UserBoxModule', 'GuestModule', 'NewDiscussionModule', 'DiscussionFilterModule', 'SignedInModule', 'Ads');

// Plugin
$Configuration['Plugin']['IndexDiscussionImage']['Image'] = '/plugins/IndexDiscussionImage/design/images/default.png';

// Plugins
$Configuration['Plugins']['GettingStarted']['Dashboard'] = '1';
$Configuration['Plugins']['GettingStarted']['Discussion'] = '1';
$Configuration['Plugins']['GettingStarted']['Categories'] = '1';
$Configuration['Plugins']['GettingStarted']['Plugins'] = '1';
$Configuration['Plugins']['editor']['ForceWysiwyg'] = false;
$Configuration['Plugins']['Hashtag']['Minletters'] = '4';
$Configuration['Plugins']['Hashtag']['Maxletters'] = '140';
$Configuration['Plugins']['Hashtag']['SearchBody'] = '1';
$Configuration['Plugins']['Hashtag']['EmbedLinks'] = '1';
$Configuration['Plugins']['Hashtag']['Showrelated'] = '1';
$Configuration['Plugins']['Hashtag']['Panelhead'] = 'Similar Hashtag Set';
$Configuration['Plugins']['Hashtag']['HideEmptyPanel'] = '1';
$Configuration['Plugins']['Hashtag']['Panelsize'] = '8';
$Configuration['Plugins']['Hashtag']['Showinline'] = '1';
$Configuration['Plugins']['Hashtag']['Panelontop'] = '';
$Configuration['Plugins']['Hashtag']['IncompleteSetup'] = false;
$Configuration['Plugins']['FilterDiscussion']['Fieldnames'] = 'DiscussionID,CategoryID,InsertUserID,UpdateUserID,Name,Body,FirstCommentID,LastCommentID';
$Configuration['Plugins']['FilterDiscussion']['Ignoreparms'] = '';
$Configuration['Plugins']['AutoLink']['Tags'] = false;
$Configuration['Plugins']['AutoLink']['Links'] = '1';
$Configuration['Plugins']['AutoLink']['Precedence'] = false;

// Routes
$Configuration['Routes']['DefaultController'] = 'discussions';

// Vanilla
$Configuration['Vanilla']['Version'] = '2.3';
$Configuration['Vanilla']['Categories']['Use'] = false;

// Last edited by Johan (172.28.128.1)2017-05-11 11:37:02