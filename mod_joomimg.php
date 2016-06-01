<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Modules/JoomImages/trunk/mod_joomimg.php $
// $Id: mod_joomimg.php 4313 2013-07-06 08:48:31Z erftralle $
/****************************************************************************************\
**   Module JoomImages for JoomGallery                                                  **
**   By: JoomGallery::ProjectTeam                                                       **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

// Deny direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
jimport('joomla.filesystem.file');

if(!JComponentHelper::isEnabled('com_joomgallery', true))
{
  echo JText::_('JIJGNOTINSTALLED');
  return;
}

// Include JoomGallery's interface class
$jg_ifpath = JPATH_ROOT.'/components/com_joomgallery/interface.php';
if(JFile::exists($jg_ifpath))
{
  require_once $jg_ifpath;
}
else
{
  echo JText::_('JIJGNOTINSTALLED');
  return;
}

// Include the helper class only once
require_once dirname(__FILE__).'/helper.php';

// Get id of current module instance
$moduleid = $module->id;

// Create helper object
$joomimgObj = new modJoomImagesHelper();

if($joomimgObj->getGalleryVersion() < "3.3")
{
  echo JText::sprintf('JIJOOMGALLERY_NOT_UPTODATE', '3.3');
  return;
}

// Fill the interface object and get the images
$imgobjects = $joomimgObj->fillObject($params,$moduleid);

// Get slideshow or default view
if($joomimgObj->getConfig('slideshowthis') == 1)
{
  $path = JModuleHelper::getLayoutPath('mod_joomimg', 'slideshow');
}
else
{
  $path = JModuleHelper::getLayoutPath('mod_joomimg', 'default');
}
if(JFile::exists($path))
{
  require $path;
}