<?php /* Smarty version 2.6.29, created on 2017-05-10 05:23:54
         compiled from /vagrant/src/web/vanilla-2.3/themes/musiquesincongrues/views/default.master.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'asset', '/vagrant/src/web/vanilla-2.3/themes/musiquesincongrues/views/default.master.tpl', 6, false),array('function', 'event', '/vagrant/src/web/vanilla-2.3/themes/musiquesincongrues/views/default.master.tpl', 15, false),)), $this); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php echo smarty_function_asset(array('name' => 'Head'), $this);?>

    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body id="<?php echo $this->_tpl_vars['BodyID']; ?>
" class="<?php echo $this->_tpl_vars['BodyClass']; ?>
">
    <!--[if lt IE 8]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <?php echo smarty_function_asset(array('name' => 'Foot'), $this);?>

    <?php echo smarty_function_event(array('name' => 'AfterBody'), $this);?>

  </body>
</html>