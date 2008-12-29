;(function($) {
	//
	// data from sfbrowser
	// functions
	var trace;
	var openDir;
//	var closeSFB;
//	var addContextItem;
//	var file;
//	var lang;
	// variables
	var aPath;
//	var bOverlay;
	var oSettings;
	var oTree;
	var mFB;
	//
	// private vars
	var iFTW = 120;
	//
	$.fn.extend($.sfbrowser, {
		filetree: function(p) {
			trace = p.trace;
			openDir = p.openDir;
			aPath = p.aPath;
			oTree = p.oTree;
			oSettings = p.oSettings;
			mFB = p.mFB;
			//
			$("#fbtable").before("<div id=\"filetree\"><div></div><ul></ul></div>").before("<div id=\"divider\"></div>");
			$("#filetree").css({height:	$("#fbtable").height()+"px"});
			$("#divider").attr("title",oSettings.lang.dragMe).mousedown( function(e){
					var iXo = e.pageX-$(e.target).offset().left;
					$("body").mousemove(function(e){
						divide(e,iXo);
					});
				});
			$.sfbrowser.filetree.resizeWindow(123,123);
		}
	});
	$.extend($.sfbrowser.filetree, {
		resizeWindow: function(iWdt,iHgt) {
			var iHgt = $("#fbtable").height();
			var iTotW = $("div#winbrowser").width();
//			var iPrvW = iFTW;//$("div#filetree>ul").width();//$("div#fbpreview").width();
//			trace("iTotW: "+iTotW);
			var iTreeW = iFTW/iTotW*100;
			var iTbleW = (iTotW-iFTW-12)/iTotW*100;
//			trace("iTreeW: "+iPrvW+"/"+iTotW+"*200="+iTreeW);
			$("#filetree").css({width:iTreeW+"%",height:iHgt+"px"});
			$("#fbtable").css({width:iTbleW+"%"});
			$("#divider").css({height:(iHgt+4)+"px"});
		}
		,listAdd: function(oFile) {
			if (oFile.mime=="folder") {
				//oFile.tr.remove();
				checkUltree(oFile.file);
			} else if (oFile.mime=="folderup") {
				oFile.tr.remove();
			}
		}
		,openDir: function(dir) {
			checkUltree();
		}
	});
	function checkUltree(dir) {
//		trace("filetree.checkUltree\n\tdir:\t"+dir+"\n\tpath:\t"+aPath);
		var mUl = $("#filetree>ul");
		var mLi = null;
		$.each( aPath, function(i,sDir) { // check dirs in current path
			var sId = i==0?"root":sDir.replace(/\//gi,"");
			mLi = checkUlLi(mUl,sId,sDir);
			mUl = $(mLi.find("ul")[0]);
		});
		if (dir) { // check dirs in current folder
			var sId = dir.replace(/\//gi,"");
			mLi = checkUlLi(mUl,sId);
		}
	}
	function checkUlLi(mUl,sId,sDir) {
		var mLi = null;
		var mFLi = mUl.find("li#"+sId);
		if (mFLi.length==0) {
			mLi = $("<li id=\""+sId+"\"><strong>"+sId+"</strong><ul></ul></li>").appendTo(mUl).find("strong").mouseover(function() {
				$(this).addClass("over");
			}).mouseout( function() {
				$(this).removeClass("over");
			}).click( function(e) {
				switchDir(e,sDir?sDir:sId);
				//openDir(sDir?sDir:sId);
			});
		} else {
			mLi = $(mFLi[0]);
		}
		return mLi;
	}
	function switchDir(e,sDir) {
		//trace("switchDir "+e+" "+sDir+" "+aPath);
		$("#filetree .selected").removeClass("selected");
		$(e.target).addClass("selected");
		//trace("e "+" "+e.target.nodeName);
		//trace("e "+" "+e.currentTarget.nodeName);
		//trace(e);
		var mLi = $(e.target).parent();
		var aNPath = [mLi.attr("id")];
		while (mLi.parent().parent().get(0).nodeName=="LI") {
			mLi = mLi.parent().parent();
			aNPath.push(mLi.attr("id"));
		}
		aNPath.reverse();
		aNPath[0] = aPath[0];
//		trace(":aNPath\t"+aNPath);
//		trace(":aPath\t"+aPath);
		while (aPath.length>0) aPath.pop();
		for (var i=0;i<(aNPath.length-1);i++) aPath.push(aNPath[i]+(i>0?"/":""));
//		trace("aPath\t"+aPath+":::"+aNPath[aNPath.length-1]);
		openDir(aNPath[aNPath.length-1]+"/");

	}
	// divide
	function divide(e,xo) {
		iFTW = Math.min($("div#winbrowser").width()-100,Math.max(50,e.pageX+xo-$("#fbwin").offset().left));
		$.sfbrowser.filetree.resizeWindow(0,0);
	}
})(jQuery);