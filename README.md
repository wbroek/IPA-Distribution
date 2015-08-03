# IPA Distribution class

*Wouter van den Broek*<br />
*Version: 0.1.1*<br />
*woutervdbroek@gmail.com*<br />


Includes: cfpropertylist (Licensed Under MIT) and the CSS from UiUIKit Framework (Licensed Under GNU General Public License v3) for the example

## What is it

The IPA Distribution class is an PHP class which creates the Wireless Manifest from any IPA (iPhone/iPad/iPad application file)  which is needed for iOS Wireless App Distribution.<br />
If you put your Provisioning Profile of the application in the same folder it will look it up and returns the url to it.<br />
iOS Wireless App Distribution is available since iOS 4, it will <strong>not</strong> work on iPhone OS 3 or lower! 

## How to Use

### Setup

Upload the class file to a webserver and make the folder in which it stands writable (it needs to make some temporary files and an folder where the manifest is created) and include the class after which you can call the action.

		require("ipaDistrubution.php");
		$ipa = new ipaDistrubution("Location of IPA file");

### Getting the link

After you make a new instance you can get the install url with:

		$ipa->applink;	
		
If you open the URL in Safari on your iOS 4 device it will ask you if it can install the application (if the right Provision Profile is installed).

The url to the Provision Profile can be retreived by

		$ipa->provisionprofile;
		
Other variables that you can use are the app's icon (if GD library is installed and the iTunesArtwork is included in the IPA) and the app's title

		$ipa->icon;
		$ipa->appname;


## Disclaimer

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

### cfpropertylist

Copyright (c) 2009 Christian Kruse, Rodney Rehm

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

