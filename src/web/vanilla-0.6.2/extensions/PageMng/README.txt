If you have mod_rewrite enabled (and are taking advantage of that in 
your vanilla install), add these lines to your .htaccess file:

#Pages
RewriteRule ^page/(.*)$ index.php?Page=$1 [QSA,L]