<?php
/**
 * Creates an Manifest from any IPA iPhone application file for iOS Wireless App Distribution.
 * and searches for the right provision profile in the same folder
 *
 * Script needs GD Library (for resizing the images) and CFPropertyList class (http://code.google.com/p/cfpropertylist/)
 * @author Wouter van den Broek <wvb@webstate.nl>
 */
namespace Distribution;

use CFPropertyList\CFPropertyList;
 
class IpaDistribution { 
	
	/**
	* The base url of the script.
	*/
	protected $baseurl;
	/**
	* The base folder of the script.
	*/
	protected $basedir;
	/**
	* The folder of the app where the manifest will be written.
	*/
	protected $folder;
	/**
	* iTunesArtwork name which is an standard from Apple (http://developer.apple.com/iphone/library/qa/qa2010/qa1686.html).
	*/
	protected $itunesartwork = "iTunesArtwork";
	/**
	* App name which can be used for the HTML page.
	*/
	public $appname;
	/**
	* App ccon which can be used for the HTML page.
	*/
	public $appicon;
	/**
	* The link to the manifest for the iPhone .
	*/
	public $applink = "itms-services://?action=download-manifest&url=";
	/**
	* Bundle identifier which is used to find the proper provision profile.
	*/
	protected $identiefier;
	/**
	* Bundle icon name for extracting icon file
	*/
	protected $icon;
	/**
	* The name of the provision profile for the IPA iPhone application .
	*/
	public $provisionprofile;
	
	
	/**
	* Initialize the IPA and create the Manifest.
	*
	* @param String $ipa the IPA file for which an Manifest must be made
	*/
	public function __construct($ipa) { 
		$this->baseurl = "http".((!empty($_SERVER['HTTPS'])) ? "s" : "")."://".$_SERVER['SERVER_NAME'];

		$this->basedir = (strpos($_SERVER['REQUEST_URI'],".php")===false?$_SERVER['REQUEST_URI']:dirname($_SERVER['REQUEST_URI'])."/");
		
		$this->makeDir(substr($ipa, 0, strlen($ipa) - 4));
		
		$this->getPlist($ipa);
		
		$this->createManifest($ipa);
		
		$this->seekMobileProvision($this->identiefier);
		
		$this->getIcon($ipa);
		
		if (file_exists($this->itunesartwork)) {
			$this->makeImages();	
		}

		$this->cleanUp();
	} 
	
	/**
	* Make a folder where the Manifest and icon files are held.
	*
	* @param String $dirname name of the folder
	*/
	function makeDir($dirname) {
		$this->folder = $dirname;
		if (!is_dir($dirname)) {
			if (!mkdir($dirname)) {
				die('Failed to create folder '.$dirname.'... Is the current folder writeable?');
			}
		}
	}
	
	/**
	* Get de Plist and iTunesArtwork from the IPA file
	*
	* @param String $ipa the location of the IPA file
	*/
	function getPlist($ipa) {
		if (is_dir($this->folder)) {
			$zip = zip_open($ipa);
			if ($zip) {
				while ($zip_entry = zip_read($zip)) {
					$fileinfo = pathinfo(zip_entry_name($zip_entry));
					if ($fileinfo['basename']=="Info.plist" || $fileinfo['basename']==$this->itunesartwork) {
						$fp = fopen($fileinfo['basename'], "w");
						if (zip_entry_open($zip, $zip_entry, "r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							fwrite($fp,"$buf");
							zip_entry_close($zip_entry);
							fclose($fp);
						}
					}
				}
				zip_close($zip);
			}
		}
	}
	
	/**
	* Create the icon and itunes artwork from the original iTunesArtwork
	*/
	function makeImages () {
		if (function_exists("imagecreatefrompng")) {
			$im = @imagecreatefrompng ($this->itunesartwork);
			$x = @getimagesize($this->itunesartwork);
			$iTunesfile = @ImageCreateTrueColor (512, 512);
			@ImageCopyResampled ($iTunesfile, $im, 0, 0, 0, 0, 512, 512, $x[0], $x[1]);
			@ImagePNG($iTunesfile,$this->folder."/itunes.png",0);
			@ImageDestroy($iTunesfile);
			$iconfile = @ImageCreateTrueColor (57, 57);
			@ImageCopyResampled ($iconfile, $im, 0, 0, 0, 0, 57, 57, $x[0], $x[1]);
			@ImagePNG($iconfile,$this->folder."/icon.png",0);
			@ImageDestroy($iconfile);
			$this->appicon = $this->folder."/icon.png";
		}
	}
	
	
	/**
	* Get the icon file out of the IPA and place it in the right folder
	*/
	function getIcon ($ipa) {
		if (is_dir($this->folder)) {
			$zip = zip_open($ipa);
			if ($zip) {
			  while ($zip_entry = zip_read($zip)) {
				$fileinfo = pathinfo(zip_entry_name($zip_entry));
				if ($fileinfo['basename']==$this->icon) {
					$fp = fopen($this->folder.'/'.$fileinfo['basename'], "w");
					if (zip_entry_open($zip, $zip_entry, "r")) {
					  $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					  fwrite($fp,"$buf");
					  zip_entry_close($zip_entry);
					  fclose($fp);
					}
				$this->appicon = $this->folder."/".$this->icon;
				}
			  }
			  zip_close($zip);
			}
		}
	}
	
	/**
	* Parse the Plist and get the values for the creating an Manifest and write the Manifest
	*
	* @param String $ipa the location of the IPA file
	*/
	function createManifest ($ipa) {
			$plist = new CFPropertyList('Info.plist');
			$plistArray = $plist->toArray();
			//var_dump($plistArray);
			$this->identiefier = $plistArray['CFBundleIdentifier'];
			if (isset($plistArray['CFBundleDisplayName'])) {
				$this->appname = $plistArray['CFBundleDisplayName'];
			} else {
				$this->appname = $plistArray['CFBundleName'];
			}
			// $this->icon = ($plistArray['CFBundleIconFile']!=""?$plistArray['CFBundleIconFile']:(count($plistArray['CFBundleIconFile'])>0?$plistArray['CFBundleIconFile'][0]:null));
			
			if (isset($plistArray['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'])) {
				$icons = $plistArray['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'];
				$this->icon = $icons[count($icons) - 1] . '@2x.png';
			}
			$manifest = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>items</key>
	<array>
		<dict>
			<key>assets</key>
			<array>
				<dict>
					<key>kind</key>
					<string>software-package</string>
					<key>url</key>
					<string>'.$this->baseurl.'/'.$ipa.'</string>
				</dict>
				<dict>
					<key>kind</key>
					<string>full-size-image</string>
					<key>url</key>
					<string>'.$this->baseurl.'/'.$this->folder.'/itunes.png</string>
				</dict>
				<dict>
					<key>kind</key>
					<string>display-image</string>
					<key>url</key>
					<string>'.$this->baseurl.'/'. $this->folder.'/'.($this->icon==null?'icon.png':$this->icon).'</string>
				</dict>
			</array>
			<key>metadata</key>
			<dict>
				<key>bundle-identifier</key>
				<string>'.$plistArray['CFBundleIdentifier'].'</string>
				<key>bundle-version</key>
				<string>'.$plistArray['CFBundleVersion'].'</string>
				<key>kind</key>
				<string>software</string>
				<key>title</key>
				<string>' . $this->appname . '</string>
			</dict>
		</dict>
	</array>
</dict>
</plist>';
				
			if (file_put_contents($this->folder . "/".basename($ipa, ".ipa") . ".plist", $manifest)) {
				$this->applink = $this->applink.$this->baseurl.$this->basedir.$this->folder."/".basename($ipa, ".ipa").".plist";	
			} else {
				die("Wireless manifest file could not be created !?! Is the folder ".$this->folder." writable?");	
			} 
	}
	
	/**
	* Removes temporary files
	*/
	function cleanUp () {
		if (file_exists($this->itunesartwork)) @unlink($this->itunesartwork);
		if (file_exists("Info.plist"))  @unlink("Info.plist");
	}
	
	/**
	* Search for the right provision profile in de current folder
	*
	* @param String $identiefier the bundle identifier for the app
	*/
	function seekMobileProvision ($identiefier) {
		$wildcard = pathinfo($identiefier);
		
		$bundels = array();
		foreach (glob("*.mobileprovision") as $filename) {
			$profile = file_get_contents($filename);
			$seek = strpos(strstr($profile, $wildcard['filename']),"</string>");
			if ($seek!== false) $bundels[substr(strstr($profile, $wildcard['filename']),0,$seek)] = $filename;
		}
		
		if (array_key_exists($this->identiefier,$bundels)) $this->provisionprofile = $bundels[$this->identiefier];
		else if  (array_key_exists($wildcard['filename'].".*",$bundels)) $this->provisionprofile = $bundels[$wildcard['filename'].".*"];
		else $this->provisionprofile = null;
	}
}
