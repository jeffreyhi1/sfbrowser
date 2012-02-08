;(function($) {
	// data from sfbrowser
	// functions
	var trace;
	var openDir;
	var addContextItem;
	var file;
	var lang;
	var gettext;
	var moveWindowDown;
	var resizeWindow;
	var getFilePath;
	// variables
	var aPath;
	var oSettings;
	var oTree;
	// display objects
	var $Document;
	var $Window;
	var $Body;
	var $SFB;
	var $SFBWin;
	var $TableH;
	var $Table;
	var $TbBody;
	var $TrLoading;
	var $Context;
	//
	// private vars
	var sConnector;
	var oFile;
	var mContextItem;
	//
	// dragging
	var mDragEl;
	var iDragEl = -1;
	//
	var fRzsAspR;
	var fRzsScale;
	//
	var iRzsW;
	var iRzsH;
	var iCrpW;
	var iCrpH;
	var iCrpX;
	var iCrpY;
	var iCrpXs;
	var iCrpYs;
	var iCrpWs;
	var iCrpHs;
	//
	$.fn.extend($.sfbrowser, {
		feather: function(p) {
			trace = p.trace;
			openDir = p.openDir;
			aPath = p.aPath;
			oTree = p.oTree;
			oSettings = p.oSettings;
			addContextItem = p.addContextItem;
			file = p.file;
			lang = p.lang;
			gettext = p.gettext;
			moveWindowDown = p.moveWindowDown;
			resizeWindow = p.resizeWindow;
			getFilePath = p.getFilePath;
			// display objects
			$Document = p.$Document;
			$Window = p.$Window;
			$Body = p.$Body;
			$SFB = p.$SFB;
			$SFBWin = p.$SFBWin;
			$TableH = p.$TableH;
			$Table = p.$Table;
			$TbBody = p.$TbBody;
			$TrLoading = p.$TrLoading;
			$Context = p.$Context;
			//
			var oSfb = $.sfbrowser;
			sConnector = oSettings.sfbpath+"plugins/feather/connectors/"+oSettings.connector+"/feather."+oSettings.connector;
			//
			//$SFB.find("#fbwin").prepend(oSettings.feather);
			$(oSettings.feather).prependTo($SFB.find("#fbwin")).hide().find('a');
			//
			// header
			$SFB.find("#sfbfeather>div.sfbheader>h3").mousedown(moveWindowDown);

			// feather
			/*
			#SFBfeatherUI
				#avpw_holder
					#avpw_controls
						#avpw_controls_2
							#avpw_wrapper_1
								#avpw_photo_content
								#avpw_tool_content_wrapper
									#avpw_tool_content
										#avpw_tool_main_container
											#avpw_control_main
												#avpw_control_main_scroll_panel
													#avpw_control_main_scrolling_region
												#avpw_tools_pager
									#avpw_filter_eggs_panel
			*/
			var $FthCancel = $('#avpw_control_cancel').hide();
			var $FthSave = $('#avpw_save_button').hide();
			$('#avpw_clouds').remove();
			$('#avpw_mountains,#avpw_mountains,#avpw_control_cancel_pane,#avpw_rightbevel,#avpw_topleft_corner,#avpw_topright_corner,#avpw_topbevel').remove();
			$('#avpw_control_undo,#avpw_control_redo').css({top:'-20px'});

//			$('#avpw_controls_2').width(iWdt).height(iHgt-80);
//			$('#avpw_control_main').height(iHgt-80);

			$('#avpw_holder').css({
				height: '100%'
			});
			$('#avpw_controls').css({
				height: '100%'
			});
			$('#avpw_controls_2').css({
				minHeight: '100px'
				,width: '100%'
				,height: '100%'
				,border: '0'
			});
			$('#avpw_wrapper_1').css({
				height: '100%'
			});
			$('#avpw_tool_content_wrapper').css({
				position: 'absolute'
				,right: '7px'
				,top: '13px'
				,paddingTop: '0'
				,height: '100%'
			});
			$('#avpw_control_main').css({
				height: '100%'
			});

			$('#avpw_filter_eggs_panel').css({
				bottom: '50px'
				,top: 'auto'
			});
			$('#avpw_tools_pager').css({
				position: 'absolute'
				,bottom: '10px'
				,left: '120px'
			});

				$('#avpw_header').css({
					position: 'absolute'
					,top: '-23px'
					,left: '48%'
				});

				$('.avpw_scrolling_region_panel').css({
					position: 'absolute'
					,top: '0px'
				});

				



			// cancel
			$SFB.find("div.cancelfeather").text(gettext('cancel')).click(function(){
				$FthCancel.click();
				closeFeather();
			});
			$SFB.find("div.feather").text(gettext('save')).click(function(){
				$FthSave.click();
				closeFeather();
			});

			// add contextmenu item
			mContextItem = addContextItem("feather",gettext('feather'),function(){featherImage()},0);
		}
	});
	$.extend($.sfbrowser.feather, {
		resizeWindow: function(iWdt,iHgt) {
			$('#SFBfeatherUI').width(iWdt).height(iHgt-80);
			$('#avpw_controls_2,#avpw_control_main,#avpw_control_main_scrolling_region').height(iHgt-80);
			
//			if (oFile) {
//				var iMaxW = $("#fbwin").width()-$("form#sfbsize").width()-20;
//				var iMaxH = $("#fbwin").height()-70;
//				fRzsScale = Math.min(1,Math.min(iMaxW/oFile.width,iMaxH/oFile.height));
//				$("div#sfbfeather>div.fbcontent>span#rszperc").text(Math.round(fRzsScale*100)+"%");
//				$("#sfbfeather div#org").css({width:(fRzsScale*oFile.width)+"px",height:(fRzsScale*oFile.height)+"px"});
//				//setView();
//			}
		}
		,checkContextItem: function(oFile,mCntx) {
			mContextItem.css({display:oFile.width!==undefined&&oFile.height!==undefined?"block":"none"});
		}
	});
	// resize Image
	function featherImage(el) {
		$("#winbrowser").hide();
		var $SFBFeather = $("#sfbfeather").show();
		oFile = file();
//		fRzsAspR = oFile.width/oFile.height;
//		iCrpWs = iCrpW = iRzsW = oFile.width;
//		iCrpHs = iCrpH = iRzsH = oFile.height;
//		iCrpXs = iCrpYs = iCrpX = iCrpY = 0;
		$("#sfbfeather>div.sfbheader>h3").text(gettext('Aviary feather')+": "+oFile.file);
		$SFBFeather.find("img").attr("src",oSettings.sfbpath+aPath.join("")+oFile.file+"?"+Math.random());
		//
				  
			Feather_OnSave = function(id, url) { 
				var e = document.getElementById(id); 
				e.src = url; 
				aviaryeditor_close();
				closeFeather();
			} 
			 
			Feather_OnLoad = function() { 
				_featherLoaded = true; 
			} 


		aviaryeditor(
			'featherImg'
			,getFilePath(oFile,false)
			,null
			,"SFBfeatherUI"
		);//aviaryeditor('editableimage1', 
		$('#avpw_controls_2').css({background:'none'});
		$.sfbrowser.feather.resizeWindow();
//		trace('oFile:',oFile); // TRACE ### oFile
//		trace('oFile:',getFilePath(oFile,false)); // TRACE ### oFile 'http://example.com/public/images/goat.jpg');
	}
	// closeFeather
	function closeFeather() {
		$("#sfbfeather").hide();
		$("#winbrowser").show();
		resizeWindow();
	}


























	// resize Image
	function resizeSend() {
		trace("sfb resizeSend");
		var iW = $("form#sfbsize input[name=rszW]").val();
		var iH = $("form#sfbsize input[name=rszH]").val();
		var iCX = $("form#sfbsize input[name=crpX]").val();
		var iCY = $("form#sfbsize input[name=crpY]").val();
		var iCW = $("form#sfbsize input[name=crpW]").val();
		var iCH = $("form#sfbsize input[name=crpH]").val();
		if (iW==oFile.width&&iH==oFile.height&&iCW==oFile.width&&iCH==oFile.height) {
			trace("sfb Will not resize to same size.");
		} else {
			trace("sfb Sending resize request...");
			$.ajax({type:"POST", url:sConnector, data:"a=bar&folder="+aPath.join("")+"&file="+oFile.file+"&w="+iW+"&h="+iH+"&cx="+iCX+"&cy="+iCY+"&cw="+iCW+"&ch="+iCH, dataType:"json", success:function(data, status){
				if (typeof(data.error)!="undefined") {
					if (data.error!="") {
						trace(lang(data.error));
						alert(lang(data.error));
					} else {
						oFile.width  = iCW;
						oFile.height = iCH;
//						for (var s in oFile) trace(s+": "+String(oFile[s]).split("\n")[0]);
						oFile.tr.find("td:eq(4)").attr("abbr",iCW*iCH).text(iCW+" x "+iCH+" px");
						// preview
						var mPrv = $("#fbpreview").clone(true);
						$("#fbpreview").html("");
						$("#fbpreview").html(mPrv.children())
						//
						$("#sfbfeather").hide();
						$("#winbrowser").show();
						resizeWindow();
					}
				}
			}});
		}
	}
})(jQuery);