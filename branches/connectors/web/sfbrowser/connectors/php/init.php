<?php // this file needs to be called in the header of your document because it adds css and js

include("config.php");
include("functions.php");

// add javascript to header
echo "\t\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".SFB_PATH."css/sfbrowser.css\" />\n";
echo "\t\t<script type=\"text/javascript\" src=\"".SFB_PATH."array.js\"></script>\n";
echo "\t\t<script type=\"text/javascript\" src=\"".SFB_PATH."jquery.tinysort.min.js\"></script>\n";
echo "\t\t<script type=\"text/javascript\" src=\"".SFB_PATH."jquery.sfbrowser.js\"></script>\n";
echo "\t\t<script type=\"text/javascript\" src=\"".SFB_PATH."lang/".SFB_LANG.".js\"></script>\n";
echo "\t\t<script type=\"text/javascript\">\n";
echo "\t\t\t<!--\n";
echo "\t\t\t$.sfbrowser.defaults.connector = \"php\";\n";
echo "\t\t\t$.sfbrowser.defaults.sfbpath = \"".SFB_PATH."\";\n";
echo "\t\t\t$.sfbrowser.defaults.base = \"".SFB_BASE."\";\n";
echo "\t\t\t$.sfbrowser.defaults.preview = ".PREVIEW_BYTES.";\n";
echo "\t\t\t$.sfbrowser.defaults.deny = (\"".SFB_DENY."\").split(\",\");\n";
// check existing icons
$aIcons = array();
if ($handle = opendir(SFB_PATH."icons/")) while (false !== ($file = readdir($handle))) if (filetype(SFB_PATH."icons/".$file)=="file") $aIcons[] = array_shift(explode(".",$file));
echo "\t\t\t$.sfbrowser.defaults.icons = ['".implode("','",$aIcons)."'];\n";
echo "\t\t\t-->\n";
echo "\t\t</script>\n";
?>