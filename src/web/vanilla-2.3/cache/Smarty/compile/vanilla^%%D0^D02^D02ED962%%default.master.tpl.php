<?php /* Smarty version 2.6.29, created on 2017-05-10 03:50:40
         compiled from /vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'asset', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 6, false),array('function', 'discussions_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 15, false),array('function', 'categories_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 16, false),array('function', 'custom_menu', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 17, false),array('function', 'link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 22, false),array('function', 'logo', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 22, false),array('function', 'module', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 27, false),array('function', 'signin_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 32, false),array('function', 'breadcrumbs', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 42, false),array('function', 't', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 64, false),array('function', 'searchbox', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 76, false),array('function', 'profile_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 84, false),array('function', 'inbox_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 85, false),array('function', 'bookmarks_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 86, false),array('function', 'dashboard_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 87, false),array('function', 'signinout_link', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 88, false),array('function', 'event', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 91, false),array('modifier', 'date_format', '/vagrant/src/web/vanilla-2.3/themes/Gopi/views/default.master.tpl', 64, false),)), $this); ?>
<!DOCTYPE html>
<html lang="<?php echo $this->_tpl_vars['CurrentLocale']['Lang']; ?>
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php echo smarty_function_asset(array('name' => 'Head'), $this);?>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,400,600" rel="stylesheet">
</head>
<body id="<?php echo $this->_tpl_vars['BodyID']; ?>
" class="<?php echo $this->_tpl_vars['BodyClass']; ?>
">
    <div class="Head" id="Head">
        <div class="Container">
            <nav class="Row">
                <div class="col-3-nav nav-icon">
                    <ul class="mobile-close">
                        <?php echo smarty_function_discussions_link(array(), $this);?>

                        <?php echo smarty_function_categories_link(array(), $this);?>

                        <?php echo smarty_function_custom_menu(array(), $this);?>

                    </ul>
                    <a class="search" href="#search"><i class="icon icon-search"></i></a>
                </div>
                 <div class="col-3-nav">
                    <a class="logo" href="<?php echo smarty_function_link(array('path' => "/"), $this);?>
"><?php echo smarty_function_logo(array(), $this);?>
</a>
                </div>
                <div class="col-3-nav nav-icon">
                    <?php if ($this->_tpl_vars['User']['SignedIn']): ?>
                    <span class="mobile-close">
                        <?php echo smarty_function_module(array('name' => 'MeModule'), $this);?>

                    </span> 
                    
                    <a class="mobile-open mobile-menu" href="#mobile-menu"><i class="icon icon-menu"></i></a>
                    <?php else: ?>
                        <?php echo smarty_function_signin_link(array('wrap' => 'span','format' => "<a href='%url' title='%text' class='%class'><span class='icon icon-signin'><span></a>"), $this);?>

                    <?php endif; ?> 
                </div>    
            </nav>
        </div>
    </div>
    <div id="Body" class="Container">
        
        <?php if (! InSection ( array ( 'CategoryList' , 'CategoryDiscussionList' , 'DiscussionList' , 'Entry' , 'Profile' , 'ActivityList' , 'ConversationList' , 'PostConversation' , 'Conversation' , 'PostDiscussion' ) )): ?>
        <div class="Row">
            <div class="col-12 BreadcrumbsWrapper"><?php echo smarty_function_breadcrumbs(array(), $this);?>
</div>
        </div>
        <?php endif; ?>
        <div class="Row">
            <div class="col-9" id="Content">
                <div class="ContentColumn">
                    <?php echo smarty_function_asset(array('name' => 'Content'), $this);?>

                </div>
            </div>
            <?php if (! InSection ( array ( 'Entry' , 'PostDiscussion' ) )): ?>
            <div class="col-3 PanelColumn" id="Panel">
                <?php echo smarty_function_asset(array('name' => 'Panel'), $this);?>

            </div>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <div class="Container">
            <div class="Row footer">
                <div class="col-6">
                    <a href="#" id="back-to-top" title="Back to top"><i class="icon icon-chevron-up"></i></a>

                    &copy; <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <?php echo smarty_function_t(array('c' => 'Copyright'), $this);?>
.
                    Powered by <a target="_blank" href="http://vanillaforums.org/">VanillaForums</a>, Designed by <a target="_blank" href="http://wptolik.com/" title="wptolik.com">WPtolik</a>. 
                </div>
                <div class="col-6 right-text">
                     
                </div>
            </div>
        </div>
        <?php echo smarty_function_asset(array('name' => 'Foot'), $this);?>

    </footer>
<div id="search">
    <button class="modal-close"></button>
    <?php echo smarty_function_searchbox(array(), $this);?>

</div>
<div id="mobile-menu">
    <button class="modal-close"></button>
    <ul>
        <?php echo smarty_function_discussions_link(array(), $this);?>

        <?php echo smarty_function_categories_link(array(), $this);?>

        <?php echo smarty_function_custom_menu(array(), $this);?>

        <?php echo smarty_function_profile_link(array(), $this);?>

        <?php echo smarty_function_inbox_link(array(), $this);?>

        <?php echo smarty_function_bookmarks_link(array(), $this);?>

        <?php echo smarty_function_dashboard_link(array(), $this);?>

        <?php echo smarty_function_signinout_link(array(), $this);?>

    </ul>
</div>
<?php echo smarty_function_event(array('name' => 'AfterBody'), $this);?>

<?php echo '
 <script>
 $(\'body\').show();
 $(\'.version\').text(NProgress.version);
 NProgress.start();
 setTimeout(function() { NProgress.done(); $(\'.fade\').removeClass(\'out\'); }, 1000);
 </script>
 '; ?>

</body>
</html>