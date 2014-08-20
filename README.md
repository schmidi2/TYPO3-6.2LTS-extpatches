Patched Extensions for TYPO3 6.2
=======
This Repository contains fixes for various TYPO3 Extensions, which are not compatible with the new TYPO3 6.2 LTS version. Most of these Extensions are not active anymore or obsolete, but still used by many TYPO3 websites.
The Repository doesn't contain *all* incompatible Extensions, only those I had to patch for my own migrations.
I hope people migrating to TYPO3 6.2 LTS can use my fixes. Please submit a new issue if you find a bug or fixed an Extension which is not in my repository jet. 
I also use this repository to create patch files for bug reports to submit them on TYPO3 Forge (https://forge.typo3.org/) or mail them to the developer, if possible.

This is not an official TYPO3 Repository.


Installation
-------------------------------------------------------------------------------
First check http://typo3.org/extensions/repository/. Maybe the author of your desired Extension released a new version which is now compatible with TYPO3 6.2. If not, you can download the whole repository by clicking on “Download ZIP”. Unpack the ZIP and cd into the folder of your desired extension and upload it to your website into the directory /typo3conf/ext/. Rename the folder name and remove the version (eg change "multicolumn-2.1.17" to "multicolumn"). Then go to your TYPO3 Backend to Extensions Manger. You will find the Extensions in the list - just activate it by clicking on the grey Lego icon on the left.

Ff you still get error messages delete the content of the folder typo3temp/ .