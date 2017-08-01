<?php /* Smarty version 2.6.29, created on 2017-05-11 09:25:58
         compiled from /vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'asset', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 4, false),array('function', 'home_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 11, false),array('function', 'link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 24, false),array('function', 'logo', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 24, false),array('function', 'searchbox', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 33, false),array('function', 'dashboard_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 35, false),array('function', 'discussions_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 36, false),array('function', 'activity_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 37, false),array('function', 'inbox_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 38, false),array('function', 'custom_menu', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 39, false),array('function', 'profile_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 40, false),array('function', 'signinout_link', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 41, false),array('function', 'breadcrumbs', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 47, false),array('function', 'module', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 53, false),array('function', 'vanillaurl', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 61, false),array('function', 'event', '/vagrant/src/web/vanilla-2.3/themes/bittersweet/views/default.master.tpl', 66, false),)), $this); ?>
<!DOCTYPE html>
<html>
<head>
    <?php echo smarty_function_asset(array('name' => 'Head'), $this);?>

</head>
<body id="<?php echo $this->_tpl_vars['BodyID']; ?>
" class="<?php echo $this->_tpl_vars['BodyClass']; ?>
">
<div id="Frame">
    <div class="Top">
        <div class="Row">
            <div class="TopMenu">
                <?php echo smarty_function_home_link(array(), $this);?>

                <!--
                You can add more of your top-level navigation links like this:

                <a href="#">Store</a>
                <a href="#">Blog</a>
                <a href="#">Contact Us</a>
                -->
            </div>
        </div>
    </div>
    <div class="Banner">
        <div class="Row">
            <strong class="SiteTitle"><a href="<?php echo smarty_function_link(array('path' => "/"), $this);?>
"><?php echo smarty_function_logo(array(), $this);?>
</a></strong>
            <!--
            We've placed this optional advertising space below. Just comment out the line and replace "Advertising Space" with your 728x90 ad banner.
            -->
            <!-- <div class="AdSpace">Advertising Space</div> -->
        </div>
    </div>
    <div id="Head">
        <div class="Row">
            <div class="SiteSearch"><?php echo smarty_function_searchbox(array(), $this);?>
</div>
            <ul class="SiteMenu">
                <?php echo smarty_function_dashboard_link(array(), $this);?>

                <?php echo smarty_function_discussions_link(array(), $this);?>

                <?php echo smarty_function_activity_link(array(), $this);?>

                <?php echo smarty_function_inbox_link(array(), $this);?>

                <?php echo smarty_function_custom_menu(array(), $this);?>

                <?php echo smarty_function_profile_link(array(), $this);?>

                <?php echo smarty_function_signinout_link(array(), $this);?>

            </ul>
        </div>
    </div>
    <div class="BreadcrumbsWrapper">
        <div class="Row">
            <?php echo smarty_function_breadcrumbs(array(), $this);?>

        </div>
    </div>
    <div id="Body">
        <div class="Row">
            <div class="Column PanelColumn" id="Panel">
                <?php echo smarty_function_module(array('name' => 'MeModule'), $this);?>

                <?php echo smarty_function_asset(array('name' => 'Panel'), $this);?>

            </div>
            <div class="Column ContentColumn" id="Content"><?php echo smarty_function_asset(array('name' => 'Content'), $this);?>
</div>
        </div>
    </div>
    <div id="Foot">
        <div class="Row">
            <a href="<?php echo smarty_function_vanillaurl(array(), $this);?>
" class="PoweredByVanilla">Powered by Vanilla</a>
            <?php echo smarty_function_asset(array('name' => 'Foot'), $this);?>

        </div>
    </div>
</div>
<?php echo smarty_function_event(array('name' => 'AfterBody'), $this);?>

</body>
</html>