<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>jquery filebrowser</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" type="text/css" media="screen" href="style/screen.css" />

		<!--script type="text/javascript" src="http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js"></script-->

		<script type="text/javascript" src="scripts/jquery-1.2.6.min.js"></script>
		<?php include("sfbrowser/connectors/php/init.php"); ?>
		<script type="text/javascript">
			<!--
			function addFiles(aFiles) {
				if ($('#addfiles>ul').length==0) $('#addfiles').html('<ul/>');
				for (var i=0;i<aFiles.length;i++) $("#addfiles>ul").append("<li onclick=\"$(this).remove()\">"+aFiles[i].file+" is "+aFiles[i].size+"</li>");
			}
			function addImages(aFiles) {
				$.each(aFiles,function(i,o){
					$("#addimages").append("<img src=\""+o.file+"\" onclick=\"$(this).remove();\" />");
				});
			}
			$(function(){
				$("h1").text("jQuery."+$.sfbrowser.id+" "+$.sfbrowser.version);
				$("#page tr:odd").addClass("odd");
				$("#page tbody>tr").find("td:eq(0)").addClass("property");

				var mMenu = $("<ul id=\"menu\" />").appendTo("#header");
				$("h2").each(function(i,o){
					mMenu.append("<li><a href=\"#"+$(this).text()+"\">"+$(this).text()+"</a></li>");
					$(this).attr("id",$(this).text());
				});
			});
			$(window).load(function() {
				$.fn.sfbrowser();
			});
			-->
		</script>
	</head>
	<body>
		<div id="header">
			<h1><span>SFBrowser</span></h1>
		</div>
		<div id="page">
			<p><img src="data/screenshot.jpg" align="right" />SFBrowser is a file browser and uploader for jquery and php5. It returns a list of objects with containing the names and additional information of the selected files.<br/>
			You can use it, like any open-file-dialog, to select one or more files. Most inherent functionalities are also there like: file upload, file preview, creating folders and renaming or deleting files and folders.<br/>
			You can download SFBrowser at <a href="http://plugins.jquery.com/project/SFBrowser">http://plugins.jquery.com/project/SFBrowser</a>. This is also the place where you can report bugs or request new features.</p>

			<h3>caution</h3>
			<p>The initial intentions for this jQuery plugin were for use in a CMS, and those are normally password protected. If you intend to use this plugin in an unprotected part of your site make sure to doublecheck and test (hack) the server side scripts yourself. You are using this plugin at your own risk.<br/>
			Should you find holes, leaks or anything else that can be improved you can mail them to: sfbrowser at sjeiti dot com.</p>

			<h2>features</h2>

			<ul>
				<li>ajax file upload</li>
				<li>localisation (English, Dutch or Spanish)</li>
				<li>server side script connector</li>
				<li>plugin environment</li>
				<li>image resize/cropping plugin</li>
				<li>sortable file table</li>
				<li>file filtering</li>
				<li>file renaming</li>
				<li>file duplication</li>
				<li>file download</li>
				<li>file/folder context menu</li>
				<li>file preview (image and text/ascii)</li>
				<li>folder creation</li>
				<li>multiple files selection (not in IE for now)</li>
				<li>inline or overlay window</li>
				<li>window resizing</li>
			</ul>


			<h2>installation</h2>

			<p>SFBrowser can (theoretically) be connected to different server side languages. For sake of clarity this document will asume you're running PHP. Check the connectors paragraph if you want to use any other server side script.</p>

			<ul>
				<li>adjust 'sfbrowser/connectors/php/config.php' to your needs</li>
				<li>include the 'sfbrowser/connectors/php/init.php' in the head of the html</li>
				<li>if not on localhost set the correct chmod of the upload folder and it's contents</li>
			</ul>

			<h3>configuration file</h3>
			<p>The 'sfbrowser/connectors/php/config.php' file contains a few basic constants.</p>
			<table id="properties" cellpadding="0" cellspacing="0">
				<thead><tr><th>property</th><th>type</th><th>description</th><th>default</th></tr></thead>
				<tbody>
					<tr><td>SFB_PATH</td>			<td>String</td>		<td>path of sfbrowser (relative to the page it is run from)</td><td>"sfbrowser/"</td></tr>
					<tr><td>SFB_BASE</td>			<td>String</td>		<td>upload folder (relative to sfbpath)</td><td>"../data/"</td></tr>
					<tr><td>SFB_LANG</td>			<td>String</td>		<td>language ISO code</td><td>"en"</td></tr>
					<tr><td>SFB_DENY</td>			<td>String</td>		<td>forbidden file extensions</td><td>"php,php3,phtml"</td></tr>
					<tr><td>PREVIEW_BYTES</td>		<td>Integer</td>	<td>ASCII files can be previewed up to a certain amout of bytes.</td><td>600</td></tr>
					<tr><td>SFB_ERROR_RETURN</td>	<td>String</td>		<td>return value in case of error</td><td>"&lt;html&gt;&lt;head&gt;&lt;meta http-equiv="Refresh" content="0;URL=http:/" /&gt;&lt;/head&gt;&lt;/html&gt;"</td></tr>
				</tbody>
			</table>

			<h3>localisation</h3>
			<p>You can easily make SFBrowser into another language. Simply copy one of the existing language js files (sfbrowser/lang/[iso].js and sfbrowser/plugins/[name]/lang/[iso].js) and name them the <a href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements">ISO_3166 code</a> of the new language (in lowercase). Then edit the SFB_LANG constant in 'sfbrowser/connectors/php/config.php' to that ISO code.<br/>
			Should you make any language file other than the ones already present, I'd be happy to include them in a later release. Please send them to: sfbrowser at sjeiti dot com.</p>


			<h3>javascript</h3>

			<p>You can call up SFBrowser by '$.fn.sfbrowser();' or the shorter '$.sfb();'</p>
			<p>SFBrowser has a number of properties you can parse:</p>
			<table id="properties" cellpadding="0" cellspacing="0">
				<thead><tr><th>property</th><th>type</th><th>description</th><th>default</th></tr></thead>
				<tbody>
					<tr><td>title</td>	<td>String</td>		<td>title of the SFBrowser window</td><td>"SFBrowser"</td></tr>
					<tr><td>select</td>	<td>Function</td>	<td>calback function on choose</td><td>function(a){trace(a)}</td></tr>
					<tr><td>folder</td>	<td>String</td>		<td>a subfolder (relative to base, to which all returned files are relative)</td><td>""</td></tr>
					<tr><td>dirs</td>	<td>Boolean</td>	<td>allow visibility and creation/deletion of subdirectories.</td><td>true</td></tr>
					<tr><td>upload</td>	<td>Boolean</td>	<td>allow upload of files</td><td>true</td></tr>
					<tr><td>allow</td>	<td>Array&lt;String&gt;</td>		<td>allowed file extensions</td><td>[]</td></tr>
					<tr><td>resize</td>	<td>Array&lt;Integer&gt;</td>		<td>maximum image constraint: array(width,height) or null</td><td>null</td></tr>
					<tr><td>inline</td>	<td>String</td>		<td>a JQuery selector for inline browser</td><td>"body"</td></tr>
					<tr><td>fixed</td>	<td>Boolean</td>	<td>keep the browser open after selection (only works when inline is not "body")</td><td>false</td></tr>
				
					<tr><th colspan="4">the following properties normally need no change</th></tr>
					<tr><td>img</td>	<td>Array&lt;String&gt;</td>		<td>image file extensions for preview</td><td>["gif", "jpg", "jpeg", "png"]</td></tr>
					<tr><td>ascii</td>	<td>Array&lt;String&gt;</td>		<td>text file extensions for preview</td><td>["txt", "xml", "html", "htm", "eml", "ffcmd", "js", "as", "php", "css", "java", "cpp", "pl", "log"]</td></tr>
					
					<tr><th colspan="4">The following properties are set automaticly from the init file, explicitly setting these from js can lead to unexpected results.</th></tr>
					<tr><td>sfbpath</td><td>String</td>		<td>the path of sfbrowser</td><td>"sfbrowser/"</td></tr>
					<tr><td>base</td>	<td>String</td>		<td>the upload folder</td><td>"data/"</td></tr>
					<tr><td>deny</td>	<td>Array&lt;String&gt;</td>		<td>denied file extensions</td><td>["php", "php3", "phtml"]</td></tr>
					<tr><td>icons</td>	<td>Array&lt;String&gt;</td>		<td>list of existing file icons</td><td>["jpg", "html", "ico"...(etc)]</td></tr>
					<tr><td>preview</td><td>Integer</td>		<td>amount of bytes for ascii preview</td><td>600</td></tr>
					<tr><td>connector</td><td>String</td>		<td>server side script type</td><td>"php"</td></tr>
					<tr><td>lang</td><td>Object</td>		<td>language object</td><td>see lang/en.js</td></tr>
					
				</tbody>
			</table>

			<h3>select</h3>
			<p>The <span class="property">select</span> property is something you will want to set if you want SFBrowser to be usefull. It's value has to be a function with one parameter: an array containing objects for the selected files (for instance: function(a){alert(a)};). Each object in that array has the following properties (where applicable):</p>
			<table id="returnobjects" cellpadding="0" cellspacing="0">
				<thead><tr><th>property</th><th>type</th><th>description</th></tr></thead>
				<tbody>
					<tr><td>file</td>		<td>String</td>		<td>the file including its path (relative to base)</td></tr>
					<tr><td>mime</td>		<td>String</td>		<td>the filetype</td></tr>
					<tr><td>rsize</td>		<td>Integer</td>	<td>the size in bytes</td></tr>
					<tr><td>size</td>		<td>String</td>		<td>the size formatted to B, kB, MB, GB etc...</td></tr>
					<tr><td>time</td>		<td>Integer</td>	<td>the time in seconds from Unix Epoch</td></tr>
					<tr><td>date</td>		<td>String</td>		<td>the time formatted in 'j-n-Y H:i'</td></tr>
					<tr><td>width</td>		<td>Integer</td>	<td>if image, the width</td></tr>
					<tr><td>height</td>		<td>Integer</td>	<td>if image, the height</td></tr>
				</tbody>
			</table>
			<p>Keep in mind that all returned filepaths are relative to <span class="property">base</span>. If you run SFBrowser from within a CMS you'll have to alter the returned paths to the correct frontend path.</p>

			<!--h3>folder</h3>
			<p>For security reasons you will have to set the <span class="property">folder</span> property. You can leave it the default 'data/' but you cannot parse values like '/', '', './' or '../'.<br/>
			The <span class="property">folder</span> value you provide will be used as the base-path. From there you can go deeper into folders, but you can never go higher than the parsed <span class="property">folder</span> value.</p-->

			<h3>allow and deny</h3>
			<p>These properties are arrays containing file extensions that are, or are not shown in SFBrowser. This also applies to the file types that you upload.<br/>
			For security reasons the main deny list is located at 'sfbrowser/config.php' by the name of SFB_DENY (a comma separated list of extensions). Additional file types can be denied through javascript with the <span class="property">deny</span> property.<br/>
			If <span class="property">allow</span> is left empty (which is the default) all file types are allowed except those listed in <span class="property">deny</span>.<br/>
			Denying is stronger than allowing so an extension in both arrays will always be denied. The SFB_DENY constant in 'sfbrowser/config.php' always has priority over the <span class="property">deny</span> property.</p>


			<h2>usage</h2>

			<p>SFBrowser is designed to work like a normal OS's filebrowser, however, some interactions are not possible from within most web-browsers.</p>

			<h3>file selection</h3>
			<p>There are three ways to select a file: either press the 'Choose' button, double click the file, or select 'Choose' from the (right-click) context menu.</p>
			<p>To select multiple files you can hold CTRL while clicking files, or press CTRL-A to select all files.</p>

			<h3>context menu</h3>
			<p><img src="data/contextmenu.png" align="right" />Right clicking a file will popup a context menu with additional (or obvious) file operations. The two functions in here that are not found anywhere else in the interface are 'Duplicate' and 'Resize'.</p>
			<p>'Duplicate' creates a copy of the selected file and appends it with a number (multiple file duplication does not work yet).</p>
			<p><img src="data/resize_image.jpg" align="left" width="302" style="margin: 0px 30px 30px 0px;" />With 'Resize' you can size down larger jpeg images. Indexed color images (gif and png) require different code that isn't implemented yet.<br />
			Selecting 'Resize'  will bring up an overlay as show to the right here. Larger images are always scaled down to fit the window, this scale is shown as a percentage above the image.<br/>
			You can now drag the little white square to resize the image or just enter its desired with or height.<br/>
			Since upscaling mostly results in ugly images, upscaling is turned off. Also (for now) the images aspect ratio will always be maintained (meaning you can't just resize the width, the height will always follow accordingly).<br/>
			Cropping is not possible, it's on the to-do list though.</p>


			<h2>examples</h2>

			<h3>a simple one</h3>
			<p>The selected files are added to a list and their sizes are shown. Select multiple files by pressing CTRL and selecting. Start <a onclick="$.sfb({select:addFiles});">adding files.</a></p>
			<pre class="example">$.sfb({select:addFiles});</pre> 
			<div id="addfiles"></div>
			
			<h3>allowing only images</h3>
			<p>The <span class="property">allow</span> property is set to accept only images. The selected images are added to a div. Note also the title of the SFBrowser is now changed to: <a onclick="$.sfb({folder:'ImageFolder/',title:'Add some images',allow:['jpeg','png','gif','jpg'],resize:[640,480],select:addImages});">Add some images</a>.</p>
			<pre class="example">$.sfb({
	 folder:	'ImageFolder/'
	,title:		'Add some images'
	,allow:		['jpeg','png','gif','jpg']
	,resize:	[640,480]
	,select:	addImages
});</pre> 
			<div id="addimages"></div>
			
			<h3>inline</h3>
			<p>When you set the <span class="property">inline</span> property to something other than "body" SFBrowser will no appear as an overlay but inside the new value. The value has to be a regular JQuery selector with a single result. A selector with possible multiple results will really screw things up. If you're unsure about your selector simply add ':eq(0)' to it to ensure a single result<br/>
			Contrary to an overlay, an inline SFBrowser will keep the rest of your page clickable.<br/>
			Setting the <span class="property">fixed</span> property to 'true' will also disable closing the filebrowser (this will only work on inline SFBrowsers). However, calling up a new instance of SFBrowser will close any previous instance.<br/>
			<a onclick="$.sfb({ inline:'#inhere', fixed:true, select:function(a){alert(a.length)} });">Open inline fixed</a></p>
			<pre class="example">$.sfb({ inline:'#inhere', fixed:true, select:function(a){alert(a.length+" files selected")} });</pre> 
			<div id="inhere"></div>


			<h2>plugins</h2>

			<p>Lorem ipsum blaat.</p>


			<h2>connectors</h2>

			<p>A connector folder should at least have the following files.</p>
			<ul>
				<li>sfbrowser/connectors/[type]/config.[type]</li>
				<li>sfbrowser/connectors/[type]/init.[type]</li>
				<li>sfbrowser/connectors/[type]/sfbrowser.[type]</li>
			</ul>

			<h3>config file</h3>
			<p>The config file should at least have the following settings:</p>
			<ul>
				<li>path of sfbrowser</li>
				<li>the base upload folder</li>
				<li>the language ISO code</li>
				<li>ASCII preview ammount</li>
				<li>a list of forbidden file extensions</li>
				<li>an error return code (for severe misuse)</li>
			</ul>
			<p>The config file is used in both the init file (to parse to sfbrowser js) and in the actual sfbrowser file (which does the actual server side thinking).</p>

			<h3>init file</h3>
			<p>The init file must be called in the html documents header in order to write these lines:</p>
			<pre>&lt;link rel="stylesheet" type="text/css" media="screen" href="sfbrowser/css/sfbrowser.css" /&gt;
&lt;script type="text/javascript" src="sfbrowser/array.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="sfbrowser/jquery.tinysort.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="sfbrowser/jquery.sfbrowser.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="sfbrowser/lang/en.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
	&lt;!--
	$.sfbrowser.defaults.connector = "php";
	$.sfbrowser.defaults.sfbpath = "sfbrowser/";
	$.sfbrowser.defaults.base = "../data/";
	$.sfbrowser.defaults.preview = 600;
	$.sfbrowser.defaults.deny = ["php","php3","phtml"];
	$.sfbrowser.defaults.icons = ['ai','as','avi','bmp','cs','default',(...)];
	--&gt;
&lt;/script&gt;</pre>
			<p>As you might have noticed, most of these lines are formed to what is set in the config file. The icons variable is a folder readout from the sfbrowser/icons/ folder.</p>
			
			<h3>sfbrowser file</h3>
			<p>The sfbrowser file is the actual connector to the SFBrowser plugin. Mostly it will require some POST variables and a JSON output object.<br/>
			A POST will always contain a variable "a" that will determine the action to be taken.</p>
			<p>For all actions counts: make absolutely sure that the incoming data corresponds with that set in the config file!</p>
			<ul>
				<li>Do not upload forbidden filetypes.</li>
				<li>Only upload, rename, create, view, change and delete within base upload path.</li>
				<li>Rename files, but not the file extension.</li>
				<li>Do not return, force download or preview forbidden filetypes.</li>
			</ul>
			<p>Mostly, a POST (or GET in case of force download) will contain the variable "folder" and the variable "file". Check both of these against the base upload folder set in the config. If it's outside, somebody is probably screwing around.<br/>
			Just to be sure, check the number of _GET, _POST and _FILES variables. A _POST["a"] can only get a certain ammount of each, if these differ, something fishy might be going on.<br/>
			If suspicious action occurs you can redirect them to the return error code set in the config file in order to block acces or whatever you'd like to do(??haven't really figured out how to go from there, consider it a loose stub??).</p>
			<p>A number of the JSON outputs contain file descriptions. In the description of the types of actions the file object is referred to as &lt;FileObject&gt;. It looks like this:</p>
			<pre>JSON FileObject: {
	 file:		&lt;String&gt;	file name
	,mime:		&lt;String&gt;	file extention
	,rsize:		&lt;uint&gt;		file size in bytes
	,size:		&lt;String&gt;	file size in kB, MB or whatever
	,time:		&lt;uint&gt;		time in Unix Epoch
	,date:		&lt;String&gt;	time in "j-n-Y H:i"
	,width:		&lt;uint&gt;		image width in pixels
	,height:	&lt;uint&gt;		image height in pixels
}</pre>
			<p>The following are the possible values for _POST["a"]: the possible actions that can be taken and the required return values.</p>

			<h3>_POST["a"]=="chi" :: retreive file list</h3>
			<pre>input:
	_POST["folder"] &lt;String&gt;	path to folder
action:
	Read the contents of a folder
	Filter out the forbidden file types
	If applicable, filter for allowed file types
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
	,data:		&lt;Object&gt;	object containing &lt;FileObject&gt;'s
}
example: {
	error:	"",
	msg:	"fileListing",
	data: {
		a: &lt;FileObject&gt;,
		b: &lt;FileObject&gt;,
		c: &lt;FileObject&gt;,
		...
	}
}</pre>

			<h3>_POST["a"]=="fu" :: file upload</h3>
			<pre>input:
	_FILES["fileToUpload"]	&lt;Object&gt;	file object
	_POST["file"]		&lt;String&gt;	path to folder
	_POST["deny"]		&lt;String&gt;	pipe separated string of denied file extensions ("php|tpl|log")
	_POST["allow"]		&lt;String&gt;	pipe separated string of allowed file extensions ("gif|jpg|jpeg|png")
	_POST["resize"]		&lt;Array&gt;		resize image to [width,heigth]
	
action:
	Check for _FILES["fileToUpload"]["error"]
	Check _FILES["fileToUpload"]["tmp_name"] for actual upload
	Check forbidden filetypes
	Check allowed filetypes
	Check upload folder for similarly named file
	Move file to upload folder
	If applicable, resize image
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
	,data:		&lt;FileObject&gt;	uploaded file
}</pre>

			<h3>_POST["a"]=="kung" :: duplicate file</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	file name
	_POST["folder"]	&lt;String&gt;	file folder
action:
	Create new non-existing file name
	Duplicate file to that name
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
	,data:		&lt;FileObject&gt;	duplicated file
}</pre>

			<h3>_POST["a"]=="ka" :: file delete</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	file name
	_POST["folder"]	&lt;String&gt;	file folder
action:
	delete file
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
}</pre>

			<h3>_POST["a"]=="sui" :: file force download</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	file name
	_POST["folder"]	&lt;String&gt;	file folder
action:
	Force file download
output: nothing</pre>

			<h3>_POST["a"]=="mizu" :: read txt file contents</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	file name
	_POST["folder"]	&lt;String&gt;	file folder
action:
	Read and return file contents
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
	,data: {
		text:	&lt;String&gt;	first x amount of bytes from ascii file
	}
}</pre>

			<h3>_POST["a"]=="ho" :: rename file</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	original file name
	_POST["folder"]	&lt;String&gt;	file folder
	_POST["nfile"]	&lt;String&gt;	new file name
action:
	Check if new file name is valid
	Rename file
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
}</pre>

			<h3>_POST["a"]=="tsuchi" :: add folder</h3>
			<pre>input:
	_POST["folder"]		&lt;String&gt;	folder to create new folder into
	_POST["foldername"]	&lt;String&gt;	new folder name
action:
	Create new folder
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
	,data:		&lt;FileObject&gt;	newly created folder
}</pre>

			<h3>_POST["a"]=="bar" :: image resize (in imageresize plugin)</h3>
			<pre>input:
	_POST["file"]	&lt;String&gt;	file name
	_POST["folder"]	&lt;String&gt;	file folder
	_POST["w"]	&lt;uint&gt;		new width
	_POST["h"]	&lt;uint&gt;		new height
action:
	Resize image
output: JSON {
	 error:		&lt;String&gt;	error message (see lang.js)
	,msg:		&lt;String&gt;	succes message (see lang.js)
}</pre>

		</div>
		<div id="footer"> 
			<div>� 2008 <a href="http://www.sjeiti.com/">Ron Valstar</a></div>
		</div>
	</body>
</html>