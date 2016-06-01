<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Modules/JoomImages/trunk/changelog.php $
// $Id: changelog.php 4411 2014-07-12 08:45:56Z erftralle $
/****************************************************************************************\
 **   Module JoomImages for JoomGallery                                                  **
**   By: JoomGallery::ProjectTeam                                                       **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>

CHANGELOG Module JoomImages for JoomGallery

Legende / Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

***     Version 3.3                  ***
# Improvements in language files
+ Option for filtering images based on 'featured' status


***     Version 3.0                  ***
# slideshow options to show image title and image text not
  working as expected


***     Version 3.0 BETA3            ***
# module option 'Adapt to categories' has not been working
  since JDatabase::getEscaped() has been removed by Joomla!
^ slideshow javascript parameter 'random' changed from 'true'
  to 'false' so that common module parameter 'Sorting' now is
  also operational in the slideshow


***     Version 3.0 BETA2            ***
# inheritance of category access has not been considered
+ new parameter to show the number of downloads


***     Version 3.0 BETA             ***
+ changelog.php added to manifest file
^ use of JoomGallery's color picker form field
^ DS -> '/'
^ STRICT standards
+ Use of new openimage form field
^ small adaptions in pagination.js (Mootools 1.4.5)
# check images against user access rights
^ minor changes
^ CSS classes sectiontableentry1/sectiontableentry2 -> jg_row1/jg_row2


***     Version 2.0 BETA5 20120310   ***
# no images with default size parameters (no crop/autoresize)
# text parameters for slideshow not active
+ color picker in backend for configuring color based parameters interactively
  http://mootools.net/forge/p/moorainbow
  http://moorainbow.woolly-sheep.net


***     Version 2.0 BETA4 20120307   ***
# slideshow looks for images with 't' at the end of prefix in name, leads to
  404 errors visible in some browsers, template defaults now to thumbnails=false
  thx @firedog112
# image description not available in img objects of slideshow
  (Show Text Captions = yes) if text options of default view deactivated
  leads to php notices and js-errors
# wrong url in slideshow with &amp;

20120218
# Fatal error: Unknown column 'cmtcount' in 'order clause'
  if option 'number of comments' = 'No' and ordering by number of comments
  active
^ ordering option 'number of comments ascending' now shows images without comments
  thx @micha

***     Version 2.0 BETA3 20120204   ***
# wrong query parameters for timespan
^ CSS style width and height for img tags
# double output of title attribute in a tag

***     Version 2.0 BETA2 20111217   ***
# wrong SQL in in/excluding categories

***     Version 2.0 BETA1 20111022   ***

20110513
^ new version of slideshow working with mootools 1.3
  http://code.google.com/p/slideshow/

***     Version 1.5.7.2     **

20110629
# Deactivating the display of comment date doesn't show the comment text

***     Version 1.5.7 .1     **
20110428
^ output of html title tag now directly in code

20110421
#  sorting by comments only shows one image


***     Version 1.5.7       **

20110406
+ parameter for formatting date/time outputs

20110404
- parameter 'show vote count' removed
+ parameter 'Image in box' to choose the image in e.g. slimbox

20110321
! needs JoomGallery 1.5.7 or newer
^ functions of DB queries merged, basic preparation for JDatabaseQuery
+ show star based voting results if activated in JoomGallery
+ ordering by number of nametags
# if no images with comments available and text comment options are activated
  notice 'There are no images'
+ Parameter for free settings of timespan
+ Parameter for crop position
+ Parameter for selection of images with votes = x
+ Parameter for additional selection of hidden images or in hidden categories


                   ***     Version 1.5.6.1     **
20110116
# slideshow doesn't show panning for some images

20101128
# needless comma in declaration of slideshow removed

                   ***     Version 1.5.6       **
20101109
+ link to detail or category view in slideshow
  ! no support of opening the detail/original image directly to in e.g. Slimbox
# In paginated view the slimbox/thickbox doesn't work at images shown from page 2 and more
  ! problems with wrong continuously counter in thickbox
^ use the new crop parameters from JoomGallery 1.5.6 instead of script resize.php (removed)


                   ***     Version 1.5.5.2     **
20101014
# missing css definitions for alignment if image setted below the texts
# wrong call of resize.php (dynamic thumbnails) if image setted below the texts
# wrong output of link at image if setted below the texts

20100916
^  modified check for existent JoomGallery


                   ***     Version 1.5.5.1     **
20100830
# no view of number of comments, last comment, comment date if detail view
  in JoomGallery configuration deactivated
# wrong name of configuration variable for showing the category link


                   ***     Version 1.5.5       **
20100728
# detail or original picture not shown in slideshow

20100423
^ pagination now with no reload and not using POST, javascript based

20100419
+ latvian and russian language files added, thank you very much @Olegs

20100417
# input tag of pagination not closed properly
# invalid xhtml in parameters for dynamic thumb resize

20100412
+ pagination in default view
^ small changes in handling the debug output
+ include option subcategories for showing/hiding categories
^ rewritten in faster PHP handling of string output

20100303
from version 1.5.4:
+ new parameter 'show directly picture in box' to override setting of joomgallery configuration
+ parameter 'Type of images' now in common parameters, now all picture types availabe in standard view
+ more options for timespan filtering
+ check existence of JoomGallery
+ check version of JoomGallery

20100117
# wrong construction of links in default view

20100105
+ more timespan options
# no output of image date
# beginning with debug output in firephp (with activated firephp extension in joomla)
+ more language constants

20091229
# setting 'Picture link->No' deactivates displaying of all text elements

20091226
# no slideshow visible in detail view
# more corrections to 'view=category', same for detail

20091224
# correct 'func=viewcategory' to 'view=category', same for detail
# double display of 'count of comments'
^ some corrections in displaying links to detail view if not activated/allowed in JoomGallery

20090929
^ modifications to work with actual interface of joomgallery

20090906
+ sorting by ordering

                   ************************
                   *** Branch for JG MVC **
                   ************************

                   ************************
                   ***  Version 1.5.3    **
                   ************************

20090822
# completed danish language file
  @mestermotte: thank you very much
^ slideshow.js
  - automatic resize
  - earlier fade out of previous image
  - centering the images in certain effects
# strip html tags and ',LF from image title/description to avoid errors in JS

20090809
# random ordering not working
  http://www.forum.en.joomgallery.net/index.php?topic=1504.msg5284#msg5284

20090808
^ changed from 'Standard' to 'Default' in xml for param slideshowthis

20090801
^ output another rel tag as gallery to separate thickbox/slimbox view
  DE: http://www.joomgallery.net/forum/index.php/topic,2000

20090731
+ norwegian language file added
  @Obi: thank you

20090725
+ time filter options (week/month/year)
  @Arjan: thank you

20090721
+ link to category view in slideshow can be deactivated

20090718
+ dynamic cropping of thumbnails to reach a common size
  thank you sh0em0nkey: http://www.forum.en.joomgallery.net/index.php?topic=1116
# slideshow: not possible to output image title and/or image description separately

20060630
# error in typo 'cmttxt' -> 'cmttext'
  http://www.joomgallery.net/forum/index.php/topic,1861
  Danke @erftralle

20060627
# missing label/description for param 'scrollmouse'
+ Show the user name (http://www.forum.en.joomgallery.net/index.php?topic=1425)
  thank you Arjan

20060620
# wrong parameter request for borderwidth
+ added title tag
# wrong interpretation of 'imgheight' with activated' autoresize
  http://www.joomgallery.net/forum/index.php/topic,1777.msg8731.html
  Danke @erftralle
# Division by zero in default.php when auto_resize activated
  but no entry in auto_resize_max -> set default to 100
# DE language file -> error in JIABOVE

20090619
+ CSS border parameters for standard view
# no SEF URL in Slideshow links
  http://www.forum.en.joomgallery.net/index.php?topic=1246

20090618
# no translation of category and picture titles in JoomFish
  http://www.joomgallery.net/forum/index.php/topic,1778.0.html
  Danke hermann

20090615
+ new parameter Itemid

20090529
# slideshow wrong output of 'Itemid=xx' string

                   ************************
                   ***  Version 1.5.2  ****
                   ************************

20090513
^ generate individual dynamic CSS tags with module id to separate the settings
  on multiple instances

20090512
^  call another function of interface to avoid CSS tags of JoomGallery
   and generate CSS in module
+  new options to generate individual CSS tags for height/width with preserving the
   image proportions

200890509
# Sorting 'commentdate DESC/ASC' not working

20090508
^ parameter image width/height not working, now CSS based

20090507
^ module class prefix can be empty
^ css sectiontableentry1/2 if activated in module

20090506
# wrong SQL Query in getdbComments()
