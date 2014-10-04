<?php if (function_exists('newrelic_get_browser_timing_footer')): ?>
	<?php echo newrelic_get_browser_timing_footer(); ?>
<?php endif; ?>
<?php
// Note: This file is included from the library/Framework/Framework.Control.PageEnd.php class.
echo '</body>
</html>';
?>