<!DOCTYPE html>
<html>
	<head>
		<title>SFBrowser unit test</title>

		<meta charset="utf-8" />

		<link href="qunit/qunit.css" type="text/css" rel="stylesheet" />
		<style>html,body{padding:0;margin:0;}</style>

		<script type="text/javascript" src="scripts/jquery-1.7b2.js"></script>
		
		<?php include("sfbrowser/connectors/php/init.php"); ?>

		<script type="text/javascript" src="qunit/qunit.js"></script>

		<script type="text/javascript">
$(function(){
	var $Body = $('body');
	var mBody = $Body[0];
	function trace(){try{console.log.apply(console,arguments);}catch(e){};}
//	if (console&&console.log) var trace = console.log;

	var N = "\n";
	var T = "\t";

	var aExpose = [
		'oSettings'
		,'openSFB'
		,'closeSFB'
		,'setupKeyboardShortcuts'
		,'keyDown'
		,'keyUp'
		,'selectAll'
		,'getFilePath'
		,'addCopyPath'
		,'remCopyPath'
		,'sortFbTable'
		,'fillList'
		,'listAdd'
		,'upDateEntry'
		,'getTd'
		,'clickTrDown'
		,'mouseMoveTr'
		,'clearMoveTr'
		,'clickTrUp'
		,'chooseFile'
		,'openContext'
		,'closeContext'
		,'previewFile'
		,'openDir'
		,'onError'
		,'duplicateFile'
		,'showFile'
		,'gettext'
		,'cleanUri'
		,'formatSize'
	];
	var sExpose = '';
	$.each(aExpose,function(i,s){
		sExpose += (i===0?'{':',')+s+':'+s
	});
	sExpose += '}';

	
	// load tinysort and hack source to expose private functions for testing
	delete $.sfb;
	delete $.sfbrowser;
	delete $.fn.sfbrowser;
	trace('$.sfbrowser',$.sfbrowser);
	$.ajax({
		url:'sfbrowser/jquery.sfbrowser.js'
		,dataFilter: function(data) {
			return data.replace(/\$\.sfbrowser\s*=\s*{/g,'$.sfbrowser={expose:function(){return'+sExpose+';},');//,isNum:isNum
		}
		,success: startTest
	});

	function startTest(){

		trace('startTest');

		$.sfb({select:function(){},plugins:[]});
//		trace('asdf');
		var $SFB = $('#sfbrowser');
		$SFB.css({display:'none'});

		$('#qunit-header').text($.sfbrowser.id+' '+$.sfbrowser.version);
//		trace('$.sfb.expose',$.sfb.expose);
		var o = $.sfb.expose();
//		trace('o',o);

		// cleanUri
		test('cleanUri', function() {
			expect(0);
			var sUri;
			//
			sUri = 'a/..///../b/c.d';
			ok( (function(){
				return o.cleanUri(sUri)=='../b/c.d';
			})(),sUri);
			//
			sUri = '..//..//..//../a///../b/c.d';
			ok( (function(){
				return o.cleanUri(sUri)=='../../../../b/c.d';
			})(),sUri);
			//
			sUri = '/a/b/c/d/../e/f.g';
			ok( (function(){
				return o.cleanUri(sUri)=='a/b/c/e/f.g';
			})(),sUri);
			//
			sUri = 'a.h/b/c/d/../e/f.g';
			ok( (function(){
				return o.cleanUri(sUri)=='a.h/b/c/e/f.g';
			})(),sUri);
		});

		// formatSize
		test('formatSize', function() {
			expect(0);
			var iSize;
			//
			iSize = 1234512345;
			ok( (function(){
				return o.formatSize(iSize)=='1GB';
			})(),iSize);
			//
			iSize = 5321461;
			ok( (function(){
				return o.formatSize(iSize)=='5MB';
			})(),iSize);
			//
			iSize = 2341;
			ok( (function(){
				return o.formatSize(iSize)=='2kB';
			})(),iSize);
			//
			iSize = 12;
			ok( (function(){
				return o.formatSize(iSize)=='12B';
			})(),iSize);
		});

		// gettext
		test('gettext', function() {
			expect(0);
			ok( (function(){
				return o.gettext('fileDeleted')=='Bestand verwijderd';
			})(),"gettext('fileDeleted')");
		});
	}
});
///////////////////////////////////////
/*$(function(){
	var $Body = $('body');
	var mBody = $Body[0];
	function trace(){try{console.log.apply(console,arguments);}catch(e){};}

$.sfb({select:function(){},plugins:[]});
var $SFB = $('#sfbrowser');
$SFB.css({display:'none'});
//$.sfb.close();
*//*trace('$.sfb:'
	,$.sfb.expose()
	,$.sfb.expose
); // TRACE ### *//*

	var N = "\n";
	var T = "\t";

	$('#qunit-header').text($.sfbrowser.id+' '+$.sfbrowser.version);
	var o = $.sfb.expose();

//	o.closeSFB();

	module('SFBrowser');

	// cleanUri
	test('cleanUri', function() {
		expect(0);
		var sUri;
		//
		sUri = 'a/..///../b/c.d';
		ok( (function(){
			return o.cleanUri(sUri)=='../b/c.d';
		})(),sUri);
		//
		sUri = '..//..//..//../a///../b/c.d';
		ok( (function(){
			return o.cleanUri(sUri)=='../../../../b/c.d';
		})(),sUri);
		//
		sUri = '/a/b/c/d/../e/f.g';
		ok( (function(){
			return o.cleanUri(sUri)=='a/b/c/e/f.g';
		})(),sUri);
		//
		sUri = 'a.h/b/c/d/../e/f.g';
		ok( (function(){
			return o.cleanUri(sUri)=='a.h/b/c/e/f.g';
		})(),sUri);
	});

	// formatSize
	test('formatSize', function() {
		expect(0);
		var iSize;
		//
		iSize = 1234512345;
		ok( (function(){
			return o.formatSize(iSize)=='1GB';
		})(),iSize);
		//
		iSize = 5321461;
		ok( (function(){
			return o.formatSize(iSize)=='5MB';
		})(),iSize);
		//
		iSize = 2341;
		ok( (function(){
			return o.formatSize(iSize)=='2kB';
		})(),iSize);
		//
		iSize = 12;
		ok( (function(){
			return o.formatSize(iSize)=='12B';
		})(),iSize);
	});

	// gettext
	test('gettext', function() {
		expect(0);
		ok( (function(){
			return o.gettext('fileDeleted')=='Bestand verwijderd';
		})(),"gettext('fileDeleted')");
	});


//	module('regression');
//	test('issue 8', function() {
//		expect(1);
//		ok( (function(){
//			var s = '';
//			getUl(0,['Q','R','S','T','U','V','W','X','Y','Z','Å','Ä','Ö']).find('li').tsort({cases:true})
//			.each(function(i,el){ s += $(el).text(); });
//			return s=='QRSTUVWXYZÄÅÖ';
//		})(),'fixed using new');
//	});
//	test('issue 10', function() {
//		expect(1);
//		ok( (function(){
//			var s = '';
//			var $Li = getUl(1).find('li');
//			$Li.filter(':eq(2)')[0].removeAttribute('id');
//			$Li.tsort().each(function(i,el){ s += $(el).attr('id')||''; });
//			return s=='eek-oif-aar-oac-eax-';
//		})());
//	});

});*/


		</script>
	</head>
	<body>
		<h1 id="qunit-header">Tinysort</h1>  
		<h2 id="qunit-banner"></h2>  
		<h2 id="qunit-userAgent"></h2>  
		<ol id="qunit-tests"></ol>
	</body>
</html>
