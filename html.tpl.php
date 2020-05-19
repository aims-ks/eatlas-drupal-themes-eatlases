<!DOCTYPE html>
<?php
// Original file: www/modules/system/html.tpl.php
?>
<html xmlns="http://www.w3.org/1999/xhtml"
		lang="<?php print $language->language; ?>"
		xml:lang="<?php print $language->language; ?>"
		version="XHTML+RDFa 1.0"
		dir="<?php print $language->dir; ?>"
		<?php print $rdf_namespaces; ?>>

<head profile="<?php print $grddl_profile; ?>">
	<?php print $head; ?>
	<title><?php print $head_title; ?></title>

	<!-- For mobile devices - also works on iPad -->
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0" />

	<!-- OpenLayers requirement for old environments like Internet Explorer and Android 4.x -->
	<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL,Array.prototype.includes,String.prototype.padStart"></script>

	<?php print $styles; ?>
	<?php print $scripts; ?>
</head>
<body class="<?php print $classes ?>" <?php print $attributes;?>>
	<div id="skip-link">
		<a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
	</div>
	<?php print $page_top; ?>
	<?php print $page; ?>
	<?php print $page_bottom; ?>
</body>
</html>
