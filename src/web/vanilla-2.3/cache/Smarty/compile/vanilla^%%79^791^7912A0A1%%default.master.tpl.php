<?php /* Smarty version 2.6.29, created on 2017-05-10 04:15:52
         compiled from /vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'asset', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 6, false),array('function', 't', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 13, false),array('function', 'link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 18, false),array('function', 'logo', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 18, false),array('function', 'categories_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 23, false),array('function', 'discussions_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 24, false),array('function', 'custom_menu', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 25, false),array('function', 'module', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 30, false),array('function', 'profile_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 33, false),array('function', 'inbox_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 34, false),array('function', 'bookmarks_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 35, false),array('function', 'dashboard_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 36, false),array('function', 'signinout_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 37, false),array('function', 'signin_link', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 41, false),array('function', 'breadcrumbs', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 53, false),array('function', 'searchbox', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 61, false),array('function', 'event', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 78, false),array('modifier', 'date_format', '/vagrant/src/web/vanilla-2.3/themes/auro2/views/default.master.tpl', 71, false),)), $this); ?>
<!DOCTYPE html>
<html lang="<?php echo $this->_tpl_vars['CurrentLocale']['Lang']; ?>
" class="sticky-footer-html">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo smarty_function_asset(array('name' => 'Head'), $this);?>

  </head>
  <body id="<?php echo $this->_tpl_vars['BodyID']; ?>
" class="<?php echo $this->_tpl_vars['BodyClass']; ?>
 sticky-footer-body">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><?php echo smarty_function_t(array('c' => 'Toggle navigation'), $this);?>
</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo smarty_function_link(array('path' => 'home'), $this);?>
"><?php echo smarty_function_logo(array(), $this);?>
</a>
        </div>

        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <?php echo smarty_function_categories_link(array(), $this);?>

            <?php echo smarty_function_discussions_link(array(), $this);?>

            <?php echo smarty_function_custom_menu(array(), $this);?>

          </ul>
		 
          <?php if ($this->_tpl_vars['User']['SignedIn']): ?>
            <ul class="nav navbar-nav navbar-right hidden-xs">
              <?php echo smarty_function_module(array('name' => 'MeModule'), $this);?>

            </ul>
            <ul class="nav navbar-nav navbar-right visible-xs">
              <?php echo smarty_function_profile_link(array(), $this);?>

              <?php echo smarty_function_inbox_link(array(), $this);?>

              <?php echo smarty_function_bookmarks_link(array(), $this);?>

              <?php echo smarty_function_dashboard_link(array(), $this);?>

              <?php echo smarty_function_signinout_link(array(), $this);?>

            </ul>
          <?php else: ?>
            <ul class="nav navbar-nav navbar-right">
              <?php echo smarty_function_signin_link(array(), $this);?>

            </ul>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <section class="container">
      <div class="row">
        <main class="page-content" role="main">
          
		   <?php if (! InSection ( array ( 'CategoryList' , 'CategoryDiscussionList' , 'DiscussionList' , 'RegisterPage' ) )): ?>
           <?php echo smarty_function_breadcrumbs(array(), $this);?>

		    <?php endif; ?>
          
          <?php echo smarty_function_asset(array('name' => 'Content'), $this);?>

        </main>

        <aside class="page-sidebar" role="complementary">
		 <?php if (InSection ( array ( 'CategoryList' , 'CategoryDiscussionList' , 'DiscussionList' ) )): ?>
            <div class="well search-form"><?php echo smarty_function_searchbox(array(), $this);?>
</div>
          <?php endif; ?>
          <?php echo smarty_function_asset(array('name' => 'Panel'), $this);?>

        </aside>
      </div>
    </section>

    <footer class="page-footer sticky-footer">
      <div class="container">
        <div class="clearfix">
          <p class="pull-left"><?php echo smarty_function_t(array('c' => 'Copyright'), $this);?>
 &copy; <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <p>
         <p class="pull-right"><a href="http://auronews.in/">Auronews.in</a></p>
        </div>
        <?php echo smarty_function_asset(array('name' => 'Foot'), $this);?>

      </div>
    </footer>

    <?php echo smarty_function_event(array('name' => 'AfterBody'), $this);?>



  </body>
</html>