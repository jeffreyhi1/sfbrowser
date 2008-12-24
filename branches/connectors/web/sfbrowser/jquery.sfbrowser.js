/*
* jQuery SFBrowser
*
* Version: 2.5.4
*
* Copyright (c) 2008 Ron Valstar http://www.sjeiti.com/
*
* Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
*
* description
*   - A file browsing and upload plugin. Returns a list of objects with additional information on the selected files.
*
* requires
*   - jQuery 1.2+
*   - PHP5 (or any other server side script if you care to write the connectors)
*
* features
*   - ajax file upload
*   - localisation (English, Dutch or Spanish)
*	- server side script connector
*	- plugin environment
*	- image resize/cropping plugin
*   - sortable file table
*   - file filtering
*   - file renameing
*   - file duplication
*   - file download
*   - file/folder context menu
*   - file preview (image and text/ascii)
*	- folder creation
*   - multiple files selection (not in IE for now)
*	- inline or overlay window
*	- window resizing and dragging
*
* how it works
*   - sfbrowser returns a list of file objects.
*	  A file object contains:
*		 - file(String):		The file including its path
*		 - mime(String):		The filetype
*		 - rsize(int):			The size in bytes
*		 - size(String):		The size formatted to B, kB, MB, GB etc..
*		 - time(int):			The time in seconds from Unix Epoch
*		 - date(String):		The time formatted in "j-n-Y H:i"
*		 - width(int):			If image, the width in px
*		 - height(int):			If image, the height in px
*
* aknowlegdments
*   - ajax file upload scripts from http://www.phpletter.com/Demo/AjaxFileUpload-Demo/
*	- Spanish translation: Juan Razeto
*
* todo:
*	- add: view option: new or custom css files
*	- code: check what timeout code in upload code really does
*	- add: image preview: no-scaling on smaller images
*	- add: make text selection in table into multiple file selection
*	- add: make "j-n-Y H:i" for files variable
*   - new: make preview an option
*   - new: general filetype filter
*   - new: folder information such as number of files
*   - IE: fix IE and Safari scrolling (table header moves probably thanks to absolute positioning of parents)
*	- IE: fix multiple file selection
*	- FF: multiple file selection: disable table cell highlighting (border)
*   - new: add mime instead of extension (for mac)
*	- add: show zip and rar file contents in preview
*	- add: drag and drop files to folders
*	- new: folder treeview (plugin)
*   - new: create ascii file
*   - new: edit ascii file (plugin)
*   - maybe: drag sfbrowser
*   - maybe: copy used functions (copy, unique and indexof) from array.js
*	- maybe: thumbnail view
*
* in this update:
*	- added: server side script connectors (localisation is now js only)
*	- added: interface for plugins
*	- added: image resize/cropping plugin
*	- added: loading feedback for folder opening
*	- added: window dragging and resizing
*	- changed: php security
*	- changed: cleaned up some of code
*	- fixed: doubleclick vs rename
*
*/
;(function($) {
	// private variables
	var oSettings = {};
	var oContents = {};
	var aSort = [];
	var iSort = 0;
	var bHasImgs = false;
	var aPath = [];
	//
	var bOverlay = false;
	//
	var sFolder;
	var sReturnPath;
	//
	var mTrLoading;
	//
	// default settings
	$.sfbrowser = {
		 id: "SFBrowser"
		,version: "2.5.3"
		,defaults: {
			 title:		""						// the title
			,select:	function(a){trace(a)}	// calback function on choose
			,folder:	""						// subfolder (relative to base), all returned files are relative to base
			,dirs:		true					// allow visibility and creation/deletion of subdirectories
			,upload:	true					// allow upload of files
			,allow:		[]						// allowed file extensions
			,resize:	null					// resize images after upload: array(width,height) or null
			,inline:	"body"					// a JQuery selector for inline browser
			,fixed:		false					// keep the browser open after selection (only works when inline is not "body")
			// basic control (normally no need to change)
			,img:		["gif","jpg","jpeg","png"]
			,ascii:		["txt","xml","html","htm","eml","ffcmd","js","as","php","css","java","cpp","pl","log"]
			// set from init, explicitly setting these from js can lead to unexpected results.
			,sfbpath:	"sfbrowser/"			// path of sfbrowser (relative to the page it is run from)
			,base:		"data/"					// upload folder (relative to sfbpath)
			,deny:		[]						// not allowed file extensions
			,icons:		[]						// list of existing file icons
			,preview:	600						// amount of bytes for ascii preview
			,connector:	"php"					// connector file type (php)
			,lang:		{}						// language object
		}
		,addLang: function(oLang) {
			for (var sId in oLang) $.sfbrowser.defaults.lang[sId] = oLang[sId];
		}
	};
	// init
	$(function() {
		trace("SFBrowser init");
	});
	// call
	$.fn.extend({
		sfbrowser: function(_settings) {
			oSettings = $.extend({}, $.sfbrowser.defaults, _settings);
			oSettings.conn = oSettings.sfbpath+"connectors/"+oSettings.connector+"/sfbrowser."+oSettings.connector;
			aSort = [];
			bHasImgs = oSettings.allow.length===0||oSettings.img.copy().concat(oSettings.allow).unique().length<(oSettings.allow.length+oSettings.img.length);
			aPath = [];
			sFolder = oSettings.base+oSettings.folder;
			//
			//
			bOverlay = oSettings.inline=="body";
			if (bOverlay) oSettings.fixed = false;
			//
			// fix path and base to relative
			var aFxSfbpath =	oSettings.sfbpath.split("/");
			var aFxBase =		oSettings.base.split("/");
			var iFxLen = Math.min(aFxBase.length,aFxSfbpath.length);
			var iDel = 0;
			for (var i=0;i<iFxLen;i++) {
				var sFxFolder = aFxBase[i];
				if (sFxFolder==".."&&aFxSfbpath.length>0) {
					while (true) {
						var sRem = aFxSfbpath.pop();
						if (sRem!="") {
							iDel++;
							break;
						}
					}
				} else if (sFxFolder!="") {
					aFxBase = aFxBase.splice(iDel);
					break;
				}
			}
			sReturnPath = (aFxSfbpath.join("/")+"//"+aFxBase.join("/")).replace(/(\/+)/,"/").replace(/(^\/+)/,"");
			//
			// file browser
			mFB = $(oSettings.browser);
			mFB.find("div.sfbheader>h3").text(oSettings.title==""?oSettings.lang.sfb:oSettings.title);
			mFB.find("div#loadbar>span").text(oSettings.lang.loading);
			if (oSettings.dirs) mFB.find("ul#sfbtopmenu>li>a.newfolder").attr("title",oSettings.lang.newfolder).find("span").text(oSettings.lang.newfolder);
			else mFB.find("ul#sfbtopmenu>li>a.newfolder").parent().remove();
			if (oSettings.upload) mFB.find("ul#sfbtopmenu>li>a.upload").attr("title",oSettings.lang.upload).find("span").text(oSettings.lang.upload);
			else mFB.find("ul#sfbtopmenu>li>a.upload").parent().remove();
			if (!oSettings.fixed) mFB.find("ul#sfbtopmenu>li>a.cancelfb").attr("title",oSettings.lang.cancel).find("span").text(oSettings.lang.cancel);
			else mFB.find("ul#sfbtopmenu>li>a.cancelfb").parent().remove();
			mFB.find("table#filesDetails>thead>tr>th:eq(0)").text(oSettings.lang.name);
			mFB.find("table#filesDetails>thead>tr>th:eq(1)").text(oSettings.lang.type);
			mFB.find("table#filesDetails>thead>tr>th:eq(2)").text(oSettings.lang.size);
			mFB.find("table#filesDetails>thead>tr>th:eq(3)").text(oSettings.lang.date);
			mFB.find("table#filesDetails>thead>tr>th:eq(4)").text(oSettings.lang.dimensions);
			if (!bHasImgs) mFB.find("table#filesDetails>thead>tr>th:eq(4)").remove();
			mFB.find("div.choose").text(oSettings.lang.choose);
			mFB.find("div.cancelfb").text(oSettings.lang.cancel);
			mFB.find("div#sfbfooter").prepend("SFBrowser "+$.sfbrowser.version+" ");
			//
			mTrLoading = mFB.find("#filesDetails>tbody>tr").clone();
			//
			$("#sfbrowser").remove();
			mFB.appendTo(oSettings.inline);
			if (!bOverlay) {
				trace("sfb inline");
				mFB.css(					{position:"relative",width:"auto",heigth:"auto"});
				mFB.find("#fbbg").remove();
				mFB.find("#fbwin").css(		{position:"relative"});
			}
			//
			// context localize and functions
			addContextItem("choose",		oSettings.lang.choose,		function(){chooseFile()});
			addContextItem("rename",		oSettings.lang.rename,		function(){renameSelected()});
			addContextItem("duplicate",		oSettings.lang.duplicate,	function(){duplicateFile()});
			addContextItem("preview",		oSettings.lang.view,		function(){$("#sfbrowser tbody>tr.selected:first a.preview").trigger("click")});
			addContextItem("filedelete",	oSettings.lang.del,			function(){$("#sfbrowser tbody>tr.selected:first a.filedelete").trigger("click")});
			//
			// functions ($$move to localisation)
			//if (bOverlay) $(window).bind("resize", reposition); // $$OBSOLETE?
			// top menu
			mFB.find(".cancelfb").click(		closeSFB );
			mFB.find("#fileToUpload").change(	fileUpload);
			mFB.find(".newfolder").click(		addFolder );
			// table
			mFB.find("div.choose").click(		chooseFile);
			mFB.find("thead>tr>th:not(:last)").each(function(i,o){
				$(this).click(function(){sortFbTable(i)});
			}).append("<span>&nbsp;</span>");
			// context menu
			mFB.click(function(){
				$("#sfbcontext").slideUp("fast");
			});
			if (bOverlay) { // resize and move window
				mFB.find("h3").attr("title",oSettings.lang.dragMe).mousedown( function(){
					$("body").mousemove(moveWindow);
				});
				$("body").mouseup(function(){
					$("body").unbind("mousemove",moveWindow);
				});
				mFB.find("div#resizer").attr("title",oSettings.lang.dragMe).mousedown( function(){
					$("body").mousemove(resizeWindow);
				});
				$("body").mouseup(function(){
					$("body").unbind("mousemove",resizeWindow);
				});
			} else {
				mFB.find("div#resizer").remove();
			}
			//
			// plugins
			var oThis = {
				// functions
				 trace:				trace
				,closeSFB:			closeSFB
				,addContextItem:	addContextItem
				,file:				file
				,lang:				lang
				// variables
				,bOverlay:	bOverlay
				,oSettings:	oSettings
				,oContents:	oContents
				,aPath:		aPath
				,mFB:		mFB
			};
			$.each( oSettings.plugins, function(i,sPlugin) {
				$.sfbrowser[sPlugin](oThis);
			});
			//
			// start
			openDir(sFolder);
			//
			// keys
			// ESC : 27
			// (F1 : xxx : help)				#impossible: F1 browser help
			// F2 : 113 : rename
			// F4 : 115 : edit					#unimplemented
			// (F5 : xxx : copy)				#impossible: F5 reloads
			// (F6 : xxx : move)				#no key in SFB
			// (F7 : xxx : create directory)	#no key in SFB
			// F8 : 119	: delete				#unimplemented
			// F9 : 120	: properties			#unimplemented
			// (F10 : xxx : quit)				#no key in SFB
			// CTRL-A : xxx : select all
			oSettings.keys = [];
			$(window).keydown(function(e){
				oSettings.keys[e.keyCode] = true;
				//trace("key: "+e.keyCode+" ")
				if (e.keyCode==65&&oSettings.keys[17]) {
					$("#sfbrowser tbody>tr").each(function(){$(this).addClass("selected")});
					return false;
				}
			});
			$(window).keyup(function(e){
				//trace("key: "+e.keyCode+" ")
				if (oSettings.keys[113])	renameSelected();
				if (oSettings.keys[27])		closeSFB();
				oSettings.keys[e.keyCode] = false;
				return false;
			});
			
			if (bOverlay) {
				//reposition(); $$OBSOLETE??
				var fFbX = Math.round($(window).height()/2-$("#fbwin").height()/2);
				var fFbY = Math.round($(window).width()/2-$("#fbwin").width()/2);
				$("#fbwin").css({ top:fFbX, left:fFbY });
			}
			
			openSFB();
		}
	});
	
	///////////////////////////////////////////////////////////////////////////////// private functions
	//
	// open
	function openSFB() {
		trace("sfb open");
		// animation
		mFB.find("#fbbg").css({display:"none"});
		mFB.find("#fbbg").slideDown();
		mFB.find("#fbwin").css({display:"none"});
		mFB.find("#fbwin").slideDown();
	}
	//
	// close
	function closeSFB() {
		trace("sfb close");
		if (bOverlay&&!oSettings.fixed) {
			$("#sfbrowser #fbbg").fadeOut();
			$("#sfbrowser #fbwin").slideUp("normal",function(){$("#sfbrowser").remove();});
		}
	}
	// sortFbTable
	function sortFbTable(nr) {
		if (nr!==null) {
			iSort = nr;
			aSort[iSort] = aSort[iSort]=="asc"?"desc":"asc";
		} else {
			if (!aSort[iSort]) aSort[iSort] = "asc";
		}
		$("#sfbrowser tbody>tr.folder").tsort("td:eq(0)[abbr]",{attr:"abbr",order:aSort[iSort]});
		$("#sfbrowser tbody>tr:not(.folder)").tsort("td:eq("+iSort+")[abbr]",{attr:"abbr",order:aSort[iSort]});

		mFB.find("thead>tr>th>span").each(function(i,o){$(this).css({backgroundPosition:(i==iSort?5:-9)+"px "+(aSort[iSort]=="asc"?4:-96)+"px"})});
	}
	// fill list
	function fillList(data,status) {
		trace("sfb fillList");
		if (typeof(data.error)!="undefined") {
			if (data.error!="") {
				trace("sfb error: "+lang(data.error));
				alert(lang(data.error));
			} else {
				trace(lang(data.msg));
				$("#sfbrowser tbody").children().remove();
				$("#fbpreview").html("");
				clearObject(oContents);//oContents = {};
				aSort = [];
				$.each( data.data, function(i,oFile) {
					// todo: logical operators could be better
					var bDir = (oFile.mime=="folder"||oFile.mime=="folderup");
					if ((oSettings.allow.indexOf(oFile.mime)!=-1||oSettings.allow.length===0)&&oSettings.deny.indexOf(oFile.mime)==-1||bDir) {
						if ((bDir&&oSettings.dirs)||!bDir) listAdd(oFile);
					}
				});
				if (aPath.length>1) listAdd({file:"..",mime:"folderup",rsize:0,size:"-",time:0,date:""});
				$("#sfbrowser thead>tr>th:eq(0)").trigger("click");
			}
		}
	}
	// add item to list
	function listAdd(obj) {
		//trace("listAdd: "+obj.file);
		oContents[obj.file] = obj;
		var bFolder = obj.mime=="folder";
		var bUFolder = obj.mime=="folderup";
		var sMime = bFolder||bUFolder?oSettings.lang.folder:obj.mime;
		var sTr = "<tr id=\""+obj.file+"\" class=\""+(bFolder||bUFolder?"folder":"file")+"\">";
		sTr += "<td abbr=\""+obj.file+"\" title=\""+obj.file+"\" class=\"icon\" style=\"background-image:url("+oSettings.sfbpath+"icons/"+(oSettings.icons.indexOf(obj.mime)!=-1?obj.mime:"default")+".gif);\">"+(obj.file.length>20?obj.file.substr(0,15)+"(...)":obj.file)+"</td>";
		sTr += "<td abbr=\""+obj.mime+"\">"+sMime+"</td>";
		sTr += "<td abbr=\""+obj.rsize+"\">"+obj.size+"</td>";
		sTr += "<td abbr=\""+obj.time+"\">"+obj.date+"</td>";
		var bVImg = (obj.width*obj.height)>0;
		sTr += (bHasImgs?("<td"+(bVImg?(" abbr=\""+(obj.width*obj.height)+"\""):"")+">"+(bVImg?(obj.width+" x "+obj.height+" px"):"")+"</td>"):"");
		sTr += "<td>";
		if (!(bFolder||bUFolder)) sTr += "	<a onclick=\"\" class=\"sfbbutton preview\" title=\""+oSettings.lang.view+"\">&nbsp;<span>"+oSettings.lang.view+"</span></a>";
		if (!bUFolder) sTr += "	<a onclick=\"\" class=\"sfbbutton filedelete\" title=\""+oSettings.lang.del+"\">&nbsp;<span>"+oSettings.lang.del+"</span></a>";
		sTr += "</td>";
		sTr += "</tr>";
		// 
		var mTr = $(sTr).prependTo("#sfbrowser tbody");
		obj.tr = mTr;
		mTr.find("a.filedelete").click(deleteFile);
		mTr.find("a.preview").click(showFile);
		//mTr.find("td:last").css({textAlign:"right"}); // IE fix
		mTr.folder = bFolder||bUFolder;
		mTr.mouseover( function() {
			mTr.addClass("over");
		}).mouseout( function() {
			mTr.removeClass("over");
		}).mousedown( function(e) {
			mTr.mouseup( clickTr );
		}).dblclick( function(e) {
			chooseFile($(this));
		})//.find("a.preview").click( function(e) {
//			//trace(oSettings.conn+"?a=sui&file="+aPath.join("")+obj.file);
//			window.open(oSettings.conn+"?a=sui&file="+aPath.join("")+oFile.file,"_blank");
//		});
		mTr[0].oncontextmenu = function() {
			return false;
		};
//		mTr
		return mTr;
	}
	// clickTr: left- or rightclick table row
	function clickTr(e) {
		var mTr = $(e.currentTarget);
		mTr.unbind("mouseup");
		var oFile = file(mTr);
		var bFolder = oFile.mime=="folder";
		var bUFolder = oFile.mime=="folderup";
		var sFile = oFile.file;
		var bRight = e.button==2;
		var mCntx = $("#sfbcontext");
		//
		if (bRight) { // show context menu
			mCntx.slideUp("fast",function(){
				mCntx.css({left:e.clientX+1,top:e.clientY+1});
				// check context contents
				mCntx.children().css({display:"block"});
				if (bFolder||bUFolder) {
					mCntx.find("li:has(a.preview)").css({display:"none"});
					mCntx.find("li:has(a.duplicate)").css({display:"none"});
				}
				if (bUFolder) {
					mCntx.find("li:has(a.rename)").css({display:"none"});
					mCntx.find("li:has(a.filedelete)").css({display:"none"});
				}
				if (!oFile.width||!oFile.height) mCntx.find("li:has(a.resize)").css({display:"none"});
				//
				mCntx.slideDown("fast");
			});
		} else { // hide context menu
			mCntx.slideUp("fast");
		}
		//
		//if (!oSettings.keys[16]) trace("todo: shift selection");
		if (!oSettings.keys[17]) $("#sfbrowser tbody>tr").each(function(){if (mTr[0]!=$(this)[0]) $(this).removeClass("selected")});
		//
		// check if something is being renamed
		if (checkRename()[0]!=mTr[0]&&!bRight&&mTr.hasClass("selected")&&!bUFolder&&!oSettings.keys[17]) {
			// rename with timeout to enable doubleclick (input field stops propagation)
			setTimeout(renameSelected,500,mTr);
		} else {
			if (oSettings.keys[17]&&!bRight) mTr.toggleClass("selected");
			else mTr.addClass("selected");
		}
		// preview image
		$("#fbpreview").html("");
		if (oSettings.img.indexOf(oFile.mime)!=-1) {
			var sFuri = oSettings.sfbpath+aPath.join("")+sFile; // $$ cleanup img path
			$("<img src=\""+sFuri+"\" />").appendTo("#fbpreview").click(function(){$(this).parent().toggleClass("auto")});
		} else if (oSettings.ascii.indexOf(oFile.mime)!=-1) {// preview ascii
			$("#fbpreview").html(oSettings.lang.previewText);
			$.ajax({type:"POST", url:oSettings.conn, data:"a=mizu&folder="+aPath.join("")+"&file="+sFile, dataType:"json", success:function(data, status){
					if (typeof(data.error)!="undefined") {
					if (data.error!="") {
						trace("sfb error: "+lang(data.error));
						alert(lang(data.error));
					} else {
						trace(lang(data.msg));
						$("#fbpreview").html("<pre><div>"+oSettings.lang.previewPart.replace("#1",oSettings.preview)+"</div>\n"+data.data.text.replace(/\>/g,"&gt;").replace(/\</g,"&lt;")+"</pre>");
					}
				}
			}});
		}
		return false;
	}
	// chooseFile
	function chooseFile(el) {
		var a = 0;
		var aSelected = $("#sfbrowser tbody>tr.selected");
		var aSelect = [];
		// find selected trs and possible parsed element
		aSelected.each(function(){aSelect.push(oContents[$(this).attr("id")])});
		if (el&&el.find) aSelect.push(oContents[$(el).attr("id")]);
		// check if selection contains directory
		for (var i=0;i<aSelect.length;i++) {
			var oFile = aSelect[i];
			if (oFile.mime=="folder") {
				openDir(oFile.file+"/");
				return false;
			} else if (oFile.mime=="folderup") {
				openDir();
				return false;
			}
		}
		aSelect = aSelect.unique();
		// return clones, not the objects
		for (var i=0;i<aSelect.length;i++) {
			var oFile = aSelect[i];
			var oDupl = new Object();
			for (var p in oFile) oDupl[p] = oFile[p];
			aSelect[i] = oDupl;
		}
		// return
		if (aSelect.length==0) {
			alert(oSettings.lang.fileNotselected);
		} else {
			// correct path
			$.each(aSelect,function(i,oFile){oFile.file = sReturnPath+aPath.join("").replace(oSettings.base,"")+oFile.file;});
			oSettings.select(aSelect);
			closeSFB();
		}
	}
	///////////////////////////////////////////////////////////////////////////////// actions
	//
	// open directory
	function openDir(dir) {
		mFB.find("#filesDetails>tbody").html(mTrLoading);
		trace("sfb openDir "+dir+" to "+oSettings.conn);
		if (dir) aPath.push(dir);
		else aPath.pop();
		$.ajax({type:"POST", url:oSettings.conn, data:"a=chi&folder="+aPath.join(""), dataType:"json", success:fillList});
	}
	// duplicate file
	function duplicateFile(el) {
		var oFile = file(el);
		var sFile = oFile.file;
		//
		trace("sfb Sending duplication request...");
		$.ajax({type:"POST", url:oSettings.conn, data:"a=kung&folder="+aPath.join("")+"&file="+sFile, dataType:"json", success:function(data, status){
			if (typeof(data.error)!="undefined") {
				if (data.error!="") {
					trace(lang(data.error));
					alert(lang(data.error));
				} else {
					trace(lang(data.msg));
					listAdd(data.data).trigger('click');
				}
			}
		}});
	}
	// show
	function showFile(e) {
		var mTr = $(e.target).parent().parent();
		var oFile = file(mTr);
		//trace(oSettings.conn+"?a=sui&file="+aPath.join("")+obj.file);
		window.open(oSettings.conn+"?a=sui&file="+aPath.join("")+oFile.file,"_blank");
	}
	// delete
	function deleteFile(e) {
		var mTr = $(e.target).parent().parent();
		var oFile = file(mTr);
		var bFolder = oFile.mime=="folder";
		//
//		for (var sProp in e) trace("sProp: "+sProp+" "+e[sProp]);
//		trace("asdf: "+e.target+" "+$(e.target).parent().parent().attr("id"));
//		trace("qwer: "+this+" "+$(this).attr("class"));
		//
		if (confirm(bFolder?oSettings.lang.confirmDeletef:oSettings.lang.confirmDelete)) {
			$.ajax({type:"POST", url:oSettings.conn, data:"a=ka&folder="+aPath.join("")+"&file="+oFile.file, dataType:"json", success:function(data, status){
				if (typeof(data.error)!="undefined") {
					if (data.error!="") {
						trace(lang(data.error));
						alert(lang(data.error));
					} else {
						trace(lang(data.msg));
						$("#fbpreview").html("");
						delete oContents[oFile.file];
						mTr.remove();
					}
				}
			}});
		}
		e.stopPropagation();
	}
	// rename
	function renameSelected(e) {
		var oFile = file(e);
		if (oFile) {
			var mStd = oFile.tr.find("td:eq(0)");
			mStd.html("");
			$("<input type=\"text\" value=\""+oFile.file+"\" />").appendTo(mStd).click(stopEvt).dblclick(stopEvt).mousedown(stopEvt);
		}
	}
	function checkRename() {
		var aRenamed = $("#sfbrowser tbody>tr>td>input");
		if (aRenamed.length>0) {
			var mInput = $(aRenamed[0]);
			var mTd = mInput.parent();
			var mTr = mTd.parent();
			var oFile = oContents[mTr.attr("id")];
			var sFile = oFile.file;
			var sNFile = mInput.val();

			if (sFile==sNFile) {
				mInput.parent().html(sFile.length>20?sFile.substr(0,15)+"(...)":sFile);
			} else {
				$.ajax({type:"POST", url:oSettings.conn, data:"a=ho&folder="+aPath.join("")+"&file="+sFile+"&nfile="+sNFile, dataType:"json", success:function(data, status){
					if (typeof(data.error)!="undefined") {
						if (data.error!="") {
							trace(lang(data.error));
							alert(lang(data.error));
						} else {
							trace(lang(data.msg));
							mTd.html(sNFile.length>20?sNFile.substr(0,15)+"(...)":sNFile).attr("title",sNFile).attr("abbr",sNFile);
							oFile.file = sNFile;
						}
					}
				}});
			}
		}
		return mTr?mTr:false;
	}
	// add folder
	function addFolder() {
		trace("sfb addFolder");
		$.ajax({type:"POST", url:oSettings.conn, data:"a=tsuchi&folder="+aPath.join("")+"&foldername="+oSettings.lang.newfolder, dataType:"json", success:function(data, status){
			if (typeof(data.error)!="undefined") {
				if (data.error!="") {
					trace(lang(data.error));
					alert(lang(data.error));
				} else {
					trace(lang(data.msg));
					listAdd(data.data).trigger('click').trigger('click');
					sortFbTable(); // todo: fix scrolltop below because because of
					$("#sfbrowser #fbtable").scrollTop(0);	// IE and Safari
					$("#sfbrowser tbody").scrollTop(0);		// Firefox
				}
			}
		}});
	}
	// fileUpload
	function fileUpload() {
		trace("sfb fileUpload");
		
		$("#loadbar").ajaxStart(function(){
			$(this).show();
			loading();
		}).ajaxComplete(function(){
			$(this).hide();
		});

		ajaxFileUpload({ // fu
			url:			oSettings.conn,
			secureuri:		false,
			fileElementId:	"fileToUpload",
			dataType:		"json",
			success: function (data, status) {
				if (typeof(data.error)!="undefined") {
					if (data.error!="") {
						trace("sfb error: "+lang(data.error));
						alert(lang(data.error));
					} else {
						trace(lang(data.msg));
						listAdd(data.data).trigger('click');
						sortFbTable(); // todo: fix scrolltop below because because of
						$("#sfbrowser #fbtable").scrollTop(0);	// IE and Safari
						$("#sfbrowser tbody").scrollTop(0);		// Firefox
					}
					return false; // otherwise upload stays open...
				}
			},
			error: function (data, status, e){
				trace(e);
			}
		});
		return false;
	}
	// loading
	function loading() {
		var iPrgMove = Math.ceil((new Date()).getTime()*.3)%512;
		$("#loadbar>div").css("backgroundPosition", "0px "+iPrgMove+"px");
		$("#loadbar:visible").each(function(){setTimeout(loading,20);});
	}
	///////////////////////////////////////////////////////////////////////////////// misc methods
	//
	// get file object from tr
	function file(tr) {
		if (!tr) tr = $("#sfbrowser tbody>tr.selected:first");
		return oContents[$(tr).attr("id")];
	}
	// addContextItem
	function addContextItem(className,title,fnc,after) {
		if (after===undefined) $("<li><a class=\"textbutton "+className+"\" title=\""+title+"\"><span>"+title+"</span></a></li>").appendTo("ul#sfbcontext").find("a").click(fnc).click(function(){$("#sfbcontext").slideUp("fast")});
		else $("<li><a class=\"textbutton "+className+"\" title=\""+title+"\"><span>"+title+"</span></a></li>").insertAfter("ul#sfbcontext>li:eq("+after+")").find("a").click(fnc).click(function(){$("#sfbcontext").slideUp("fast")});
	}
//	// setButton
//	function setButton(src,find,lang,fnc) {
//		$("ul#sfbcontext")
//		var mBut = src.find(find);
//		if (lang&&lang!="") mBut.attr("title",lang).find("span").text(lang);
//		if (fnc) mBut.click(fnc);
//	}
	// lang
	function lang(s) {
		var aStr = s.split("#");
		sReturn = oSettings.lang[aStr[0]]?oSettings.lang[aStr[0]]:s;
		if (aStr.length>1) for (var i=1;i<aStr.length;i++) sReturn = sReturn.replace("#"+i,aStr[i]);
		return sReturn;
	}
	// clearObject
	function clearObject(o) {
		for (var sProp in o) delete o[sProp];
	}
	// moveWindow
	function moveWindow(e) {
		var mHd = $(".sfbheader>h3");
		var mPrn = $("#fbbg");
		var iWdt = e.pageX-mPrn.offset().left;// + mHd.offset().left;
		var iHgt = e.pageY-mPrn.offset().top;//  + mHd.offset().top;
		$("#sfbrowser div#fbwin").css({left:iWdt+"px",top:iHgt+"px"});
	}
	// resizeWindow
	function resizeWindow(e) {
		var mPrn = $("#fbwin");
		var iWdt = e.pageX-mPrn.offset().left;
		var iHgt = e.pageY-mPrn.offset().top;
		$("#sfbrowser div#fbwin").css({width:iWdt+"px",height:iHgt+"px"});
		$("#sfbrowser div#fbtable").css({height:(iHgt-230+$("#sfbrowser table>thead").height())+"px"});
		$("#sfbrowser table>tbody").css({height:(iHgt-230)+"px"});
		$.each( oSettings.plugins, function(i,sPlugin) {
			if ($.sfbrowser[sPlugin].resizeWindow) $.sfbrowser[sPlugin].resizeWindow(iWdt,iHgt);
		});
	}
//	// reposition
//	function reposition() {
//		var fFbX = Math.round($(window).height()/2-$("#fbwin").height()/2);
//		var fFbY = Math.round($(window).width()/2-$("#fbwin").width()/2);
//		$("#fbwin").css({
//			 top:  fFbX
//			,left: fFbY
//		});
//		$("#sfbcontext").slideUp("fast");
//		$.each( oSettings.plugins, function(i,sPlugin) {
//			if ($.sfbrowser[sPlugin].reposition) $.sfbrowser[sPlugin].reposition(fFbX,fFbY);
//		});
//	}
	// is numeric
	function isNum(n) {
		return (parseFloat(n)+"")==n;
	}
	// trace
	function trace(o) {
		if (window.console&&window.console.log) {
			if (typeof(o)=="string")	window.console.log(o);
			else						for (var prop in o) window.console.log(prop+": "+o[prop]);
		}
	}
	// stop event propagation
	function stopEvt(e) {
		e.stopPropagation();
	}
	////////////////////////////////////////////////////////////////
	//
	// here starts copied functions from http://www.phpletter.com/Demo/AjaxFileUpload-Demo/
	// - changed iframe and form creation to jQuery notation
	//
	function ajaxFileUpload(s) {
		trace("sfb ajaxFileUpload");
        // todo: introduce global settings, allowing the client to modify them for all requests, not only timeout		
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
		//
        var iId = new Date().getTime();
		var sFrameId = "jUploadFrame" + iId;
		var sFormId = "jUploadForm" + iId;
		var sFileId = "jUploadFile" + iId;
		//
		// create form
		var mForm = $("<form  action=\"\" method=\"POST\" name=\"" + sFormId + "\" id=\"" + sFormId + "\" enctype=\"multipart/form-data\"><input name=\"a\" type=\"hidden\" value=\"fu\" /><input name=\"folder\" type=\"hidden\" value=\""+aPath.join("")+"\" /><input name=\"allow\" type=\"hidden\" value=\""+oSettings.allow.join("|")+"\" /><input name=\"deny\" type=\"hidden\" value=\""+oSettings.deny.join("|")+"\" /><input name=\"resize\" type=\"hidden\" value=\""+oSettings.resize+"\" /></form>").appendTo('body').css({position:"absolute",top:"-1000px",left:"-1000px"});
		$("#"+s.fileElementId).before($("#"+s.fileElementId).clone(true).val("")).attr('id', s.fileElementId).appendTo(mForm);
		//
		// create iframe
		var mIframe = $("<iframe id=\""+sFrameId+"\" name=\""+sFrameId+"\"  src=\""+(typeof(s.secureuri)=="string"?s.secureuri:"javascript:false")+"\" />").appendTo("body").css({position:"absolute",top:"-1000px",left:"-1000px"});
		var mIframeIO = mIframe[0];
		//
        // Watch for a new set of requests
        if (s.global&&!jQuery.active++) jQuery.event.trigger("ajaxStart");
        var requestDone = false;
        // Create the request object
        var xml = {};
        if (s.global) jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout) {			
			var mIframeIO = document.getElementById(sFrameId);
            try {				
				if(mIframeIO.contentWindow) {
					xml.responseText = mIframeIO.contentWindow.document.body?mIframeIO.contentWindow.document.body.innerHTML:null;
					xml.responseXML = mIframeIO.contentWindow.document.XMLDocument?mIframeIO.contentWindow.document.XMLDocument:mIframeIO.contentWindow.document;
				} else if(mIframeIO.contentDocument) {
					xml.responseText = mIframeIO.contentDocument.document.body?mIframeIO.contentDocument.document.body.innerHTML:null;
                	xml.responseXML = mIframeIO.contentDocument.document.XMLDocument?mIframeIO.contentDocument.document.XMLDocument:mIframeIO.contentDocument.document;
				}						
            } catch(e) {
				jQuery.handleError(s, xml, null, e);
			}
            if (xml||isTimeout=="timeout") {				
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if (status!="error") {
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = uploadHttpData(xml, s.dataType);    
                        // If a local callback was specified, fire it and pass it the data
                        if (s.success) s.success(data, status);
                        // Fire the global callback
                        if (s.global) jQuery.event.trigger("ajaxSuccess", [xml, s]);
                    } else {
                        jQuery.handleError(s, xml, status);
					}
                } catch(e) {
                    status = "error";
                    jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if (s.global) jQuery.event.trigger("ajaxComplete", [xml, s]);

                // Handle the global AJAX counter
                if (s.global && ! --jQuery.active) jQuery.event.trigger("ajaxStop");

                // Process result
                if (s.complete) s.complete(xml, status);

				mIframe.unbind();

                setTimeout(function() {
					try {
						mIframe.remove();
						mForm.remove();
					} catch(e) {
						jQuery.handleError(s, xml, null, e);
					}
				}, 100);

                xml = null;
            }
        };
        // Timeout checker // Check to see if the request is still happening
        if (s.timeout>0) setTimeout(function() { if (!requestDone) uploadCallback("timeout"); }, s.timeout);
        
        try {
			mForm.attr("action", s.url).attr("method", "POST").attr("target", sFrameId).attr("encoding", "multipart/form-data").attr("enctype", "multipart/form-data").submit();
        } catch(e) {			
            jQuery.handleError(s, xml, null, e);
        }
		mIframe.load(uploadCallback);
        return {abort: function () {}};
    }
	function uploadHttpData(r, type) {
        var data = !type;
        data = type=="xml" || data?r.responseXML:r.responseText;
        // If the type is "script", eval it in global context
        if (type=="script")	jQuery.globalEval(data);
        // Get the JavaScript object, if JSON is used.
        if (type=="json")	eval("data = " + data);
        // evaluate scripts within html
        if (type=="html")	jQuery("<div>").html(data).evalScripts();
		//alert($('param', data).each(function(){alert($(this).attr('value'));}));
        return data;
    }
	// set functions
	$.sfb = $.fn.sfbrowser;
})(jQuery);