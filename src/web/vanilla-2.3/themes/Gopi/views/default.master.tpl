<!DOCTYPE html>
<html lang="{$CurrentLocale.Lang}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {asset name="Head"}
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,400,600" rel="stylesheet">
</head>
<body id="{$BodyID}" class="{$BodyClass}">
    <div class="Head" id="Head">
        <div class="Container">
            <nav class="Row">
                <div class="col-3-nav nav-icon">
                    <ul class="mobile-close">
                        {discussions_link}
                        {categories_link}
                        {custom_menu}
                    </ul>
                    <a class="search" href="#search"><i class="icon icon-search"></i></a>
                </div>
                 <div class="col-3-nav">
                    <a class="logo" href="{link path="/"}">{logo}</a>
                </div>
                <div class="col-3-nav nav-icon">
                    {if $User.SignedIn}
                    <span class="mobile-close">
                        {module name="MeModule"}
                    </span> 
                    
                    <a class="mobile-open mobile-menu" href="#mobile-menu"><i class="icon icon-menu"></i></a>
                    {else}
                        {signin_link wrap="span" format="<a href='%url' title='%text' class='%class'><span class='icon icon-signin'><span></a>"}
                    {/if} 
                </div>    
            </nav>
        </div>
    </div>
    <div id="Body" class="Container">
        
        {if !InSection(array("CategoryList", "CategoryDiscussionList", "DiscussionList", "Entry", "Profile", "ActivityList", "ConversationList", "PostConversation", "Conversation", "PostDiscussion"))}
        <div class="Row">
            <div class="col-12 BreadcrumbsWrapper">{breadcrumbs}</div>
        </div>
        {/if}
        <div class="Row">
            <div class="col-9" id="Content">
                <div class="ContentColumn">
                    {asset name="Content"}
                </div>
            </div>
            {if !InSection(array("Entry", "PostDiscussion"))}
            <div class="col-3 PanelColumn" id="Panel">
                {asset name="Panel"}
            </div>
            {/if}
        </div>
    </div>
    <footer>
        <div class="Container">
            <div class="Row footer">
                <div class="col-6">
                    <a href="#" id="back-to-top" title="Back to top"><i class="icon icon-chevron-up"></i></a>

                    &copy; {$smarty.now|date_format:"%Y"} {t c="Copyright"}.
                    Powered by <a target="_blank" href="http://vanillaforums.org/">VanillaForums</a>, Designed by <a target="_blank" href="http://wptolik.com/" title="wptolik.com">WPtolik</a>. 
                </div>
                <div class="col-6 right-text">
                     
                </div>
            </div>
        </div>
        {asset name="Foot"}
    </footer>
<div id="search">
    <button class="modal-close"></button>
    {searchbox}
</div>
<div id="mobile-menu">
    <button class="modal-close"></button>
    <ul>
        {discussions_link}
        {categories_link}
        {custom_menu}
        {profile_link}
        {inbox_link}
        {bookmarks_link}
        {dashboard_link}
        {signinout_link}
    </ul>
</div>
{event name="AfterBody"}
{literal}
 <script>
 $('body').show();
 $('.version').text(NProgress.version);
 NProgress.start();
 setTimeout(function() { NProgress.done(); $('.fade').removeClass('out'); }, 1000);
 </script>
 {/literal}
</body>
</html>
