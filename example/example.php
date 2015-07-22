<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta id="viewport" name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	<title>Example</title>
	<link rel="stylesheet" href="stylesheets/iphone.css" />
	<style type="text/css">.smaller { font-size: 80%; } </style>
	<script type="text/javascript" charset="utf-8">
		window.onload = function() {
		  setTimeout(function(){window.scrollTo(0, 1);}, 100);
		}
	</script>
</head>
<body>
	
	<div id="header">
		<h1>Applications</h1>
	</div>
<?
include('../src/IpaDistribution.php');
foreach (glob("**/*.ipa") as $filename) {
$ipa = new Distribution\IpaDistribution($filename);
	if ($ipa->appname!=""&&$ipa->applink!="") {
?>	
	<ul>
		<li><a href="<?=$ipa->applink?>"><?=($ipa->appicon!=""?'<img src="'.$ipa->appicon.'" width="32" class="ico" /> ':'') ?> <?=$ipa->appname?></a></li>
		<? if ($ipa->provisionprofile!="")  { ?> 
		<li><a href="<?=$ipa->provisionprofile?>" class="smaller"><img src="http://www.footballbet.net/images/prov.png" height="32" class="ico" /> Provisioning File for <?=$ipa->appname?> </a></li>
		<? } ?>
	</ul>
<?	}
}
?>
	
<p><strong>Example for IpaDistribution class</strong><br />this example is made by Wouter van den Broek, CSS from UiUIKit Framework</p>
	
</body>
</html>





