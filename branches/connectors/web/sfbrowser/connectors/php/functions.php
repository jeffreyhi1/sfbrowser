<?php

function errorHandler($errno, $errstr, $errfile, $errline) {
	throw new Exception($errstr, $errno);
}

if (!function_exists("dump")) {
	function dump($s) {
		echo "<pre>";
		print_r($s);
		echo "</pre>";
	}
}

function trace($s) {
	$oFile = fopen("log.txt", "a");
	$sDump  = $s."\n";
	fputs ($oFile, $sDump );
	fclose($oFile);
}

function format_size($size, $round = 0) {
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    for ($i=0; $size > 1024 && isset($sizes[$i+1]); $i++) $size /= 1024;
    return round($size,$round).$sizes[$i];
}

//function constantsToJs($a) {
//	foreach ($a as $s) {
//		$oVal = @constant($s);
//		$sPrefix = substr(gettype($oVal),0,1);
//		$sIsString = $sPrefix=="s"?"\"":"";
//		$sVal = 0;
//		switch ($sPrefix) {
//			case "s": $sVal = "\"".str_replace("\\","\\\\",$oVal)."\""; break;
//			case "b": $sVal = $oVal?"true":"false"; break;
//			case "d": $sPrefix = "f";
//			default: $sVal = $oVal;
//		}
//		if ($sPrefix!="N") echo "\t\t\tvar ".$sPrefix.camelCase($s)." = ".$sVal.";\n";
//		else  echo "\t\t\t// ".$s." could not be found or contains a null value.\n";
//	}
//}

function camelCase($in) {
	$out = "";
	foreach(explode("_", $in) as $n => $chunk) $out .= ucfirst(strtolower($chunk));
	return $out;
}

function numToAZ($i) {
	$s = "";
	for ($j=0;$j<strlen((string)$i);$j++) $s .= chr((int)substr((string)$i, $j, 1)%26+97);
	return $s;
}

function strip_html_tags($text) {
	$text = preg_replace(
		array(
		  // Remove invisible content
			'@<head[^>]*?>.*?</head>@siu',
			'@<style[^>]*?>.*?</style>@siu',
			'@<script[^>]*?.*?</script>@siu',
			'@<object[^>]*?.*?</object>@siu',
			'@<embed[^>]*?.*?</embed>@siu',
			'@<applet[^>]*?.*?</applet>@siu',
			'@<noframes[^>]*?.*?</noframes>@siu',
			'@<noscript[^>]*?.*?</noscript>@siu',
			'@<noembed[^>]*?.*?</noembed>@siu',
		  // Add line breaks before and after blocks
			'@</?((address)|(blockquote)|(center)|(del))@iu',
			'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
			'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
			'@</?((table)|(th)|(td)|(caption))@iu',
			'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
			'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
			'@</?((frameset)|(frame)|(iframe))@iu',
		),
		array(
			' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
			"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
			"\n\$0", "\n\$0",
		),
		$text );
	return strip_tags($text);
}

function getUriContents($sUri) {
	$sExt = array_pop(explode(".", $sUri));

	if ($sExt=="pdf")	$sContents = pdf2txt($sUri);
	else				$sContents = file_get_contents($sUri);

	$sContents = strip_html_tags($sContents);
	$sContents = preg_replace(
		array(
			"/(\r\n)|(\n|\r)/"
			,"/(\n){3,}/"
			,"/(?<=.)(\n)(?=.)/"
			,"/\|}/"
		), array(
			"\n"
			,"\n\n"
			," "
			,"!"
		), $sContents);

	return nl2br($sContents);
}


// The function returns the absolute path to the file to be included. 
// This path can be used as argument to include() and resolves the problem of nested inclusions.
function getFilePath($relative_path) { 
    // $abs_path is the current absolute path (replace "\\" to "/" for windows platforms) 
    $abs_path=str_replace("\\", "/", dirname($_SERVER['SCRIPT_FILENAME']));
    $relative_array=explode("/",$relative_path);
    $abs_array=explode("/",$abs_path);
    // for each "../" at the beginning of $relative_path
    // removes this 1st item from $relative_path and the last item from $abs_path
    while ($relative_array and ($relative_array[0]=="..")) {
        array_shift($relative_array);
        array_pop($abs_array);
    }
    // and implodes both arrays 
    return implode("/", $abs_array) . "/" . implode("/", $relative_array);   
}