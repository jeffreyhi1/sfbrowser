<?php
//
// IMPORTANT: This init.php file is practically redundant because everything below is already automaticly handled by de main init.php. This file is just an example for how you'd handle custom initialisation.

$sSfbImgHtml = $oSFBrowser->getBody(SFB_PATH.'plugins/feather/browser.html');
$sPluginPath = SFB_PATH.'plugins/feather/';
echo T.T.'<link rel="stylesheet" type="text/css" media="screen" href="'.$sPluginPath.'css/screen.css" />'.N;
echo T.T.'<script type="text/javascript" src="'.$sPluginPath.'lang/'.SFB_LANG.'.js"></script>'.N;

echo T.T.'<script type="text/javascript"> 
			var _featherLoaded = false; 
			Feather_APIKey = "d1d1d338b04ba86a8fe199cec4fb9f65"; // 720cc9b6eed98a7e0bdd5be30872b351
			Feather_Theme = "bluesky"; 
			Feather_EditOptions = "all"; 
			Feather_OpenType = "inject";  // lightbox inject float
			Feather_CropSizes = "320x240,640x480,800x600,1280x1024"; 
			Feather_PostURL = "'.$sPluginPath.'connectors/php/feather.php";
		</script>
		<!--script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script-->
		<script type="text/javascript" src="'.$sPluginPath.'feather.js"></script>'.N;


echo T.T.'<script type="text/javascript" src="'.$sPluginPath.'jquery.sfbrowser.feather'.MIN.'.js"></script>'.N;
echo T.T.'<script type="text/javascript">'.N;
echo T.T.T.'jQuery.sfbrowser.defaults.feather = "'.$sSfbImgHtml.'"'.N;
echo T.T.'</script>'.N;
?>