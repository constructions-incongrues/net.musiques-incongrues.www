<?php
// TODO: insert default vanilla extension header

// Instanciate and configure extension
$extension = new Constructions_Vanilla_Extension_Dummy();
$extension->setAllowedUsers(array(1, 2, 47))
$extension->setTemplatesRoot(dirname(__FILE__).'/templates');

// Handle request
return $extension->handleRequest();
