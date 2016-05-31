<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Modules/JoomImages/trunk/helper.php $
// $Id: helper.php 4353 2014-02-02 10:04:37Z erftralle $
/****************************************************************************************\
**   Module JoomImages for JoomGallery                                                  **
**   By: JoomGallery::ProjectTeam                                                       **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for module JoomImages
 *
 */
class modJoomImagesHelper extends joominterface
{
  // Own class variable because db->escaped strips the ""
  var $scrollmousecode;

  // True if output of one or more comment texts active
  var $cmttext;

  // True if one of the sorting by comments options active
  var $cmtsort;

  /**
   * Entry function
   *
   * @param object $params - backend parameters
   * @param object $modObject - interface object
   * @return object - picture objects
   */
  function fillObject(&$params, &$moduleid)
  {
    $doc = JFactory::getDocument();

    // Initialize with null to determine them in cmttest()
    $this->cmttext = null;
    $this->cmtsort = null;

    // Read the parameters
    $this->getParams($params, $moduleid);

    // Get the images
    $objects = $this->getDBImages();

    // *** Slideshow ***
    if($this->getConfig('slideshowthis') == 1)
    {
      // Includes CSS of slideshow
      $doc->addStyleSheet(JURI::base().'media/mod_joomimg/css/slideshow.css');

      // Include javascripts
      JHtml::_('behavior.framework', true);
      $doc->addScript(JURI::base().'media/mod_joomimg/js/slideshow.js');

      $transtype = $this->getConfig('transType');
      switch($transtype)
      {
        case 'flash':
          $doc->addScript(JURI::base().'media/mod_joomimg/js/slideshow.flash.js');
          break;
        case 'fold':
          $doc->addScript(JURI::base().'media/mod_joomimg/js/slideshow.fold.js');
          break;
        case 'kenburns':
          $doc->addScript(JURI::base().'media/mod_joomimg/js/slideshow.kenburns.js');
          break;
        case 'push':
          $doc->addScript(JURI::base().'media/mod_joomimg/js/slideshow.push.js');
          break;
        default:
          break;
      }

      // Set the width of slideshow per CSS
      $cssslidewidth  = '#slideshow'.$moduleid.' {'."\n";
      $cssslidewidth .= '  width: '.$this->getConfig("width").'px;'."\n";
      $cssslidewidth .= '}'."\n";

      // Set the height of text field, font size of text, color of text and backgrond color
      $cssslidewidth .= '.slideshow-captions-visible{'."\n";
      $cssslidewidth .= '  height: '.$this->getConfig("heightCaption").' !important;'."\n";
      $cssslidewidth .= '  font-size: '.$this->getConfig("titleSize").' !important;'."\n";
      $cssslidewidth .= '  color: '.$this->getConfig("titleColor").' !important;'."\n";
      $cssslidewidth .= '  background: '.$this->getConfig("titleBGColor").' !important;'."\n";
      $cssslidewidth .= '}'."\n";


      $doc->addStyleDeclaration($cssslidewidth);
    }
    else
    {
      // Include CSS files of JoomGallery
      $this->getPageHeader();

      // Modify content of images for default view
      $this->modContent($objects);

      // Include Pagination javascript if activated
      if($this->getConfig('pagination'))
      {
        JHtml::_('behavior.framework', true);
        $doc->addScript(JURI::base().'media/mod_joomimg/js/pagination.js');

        $jsstart="window.addEvent('domready', function(){
          var joomimgpagination$moduleid = new JoomImgPagination(
          {
            moduleid:$moduleid,
            pagpersite: ".$this->getConfig('paginationct').",
            csstag: '".$this->getConfig('csstag')."'
            });
        });";
        $doc->addScriptDeclaration($jsstart);
      }
      // Create and include the dynamic css for default view
      // according to backend settings
      $this->renderCSS();
    }
    // Include common css
    $doc->addStyleSheet(JURI::base().'media/mod_joomimg/css/mod_joomimg.css');
    return $objects;
  }

  /**
   * Get the params setted in module backend
   *
   * @param object $params - backend parameters
   */
  function getParams(&$params, &$moduleid)
  {
    // Get the parameters and add them to the config
    $this->addConfig('group', 'joomgallerymodji');
    $this->addConfig('Itemid', $params->get('Itemid', ''));
    $this->addConfig('limit', $params->get('limit', 4));
    $this->addConfig('img_per_row', $params->get('img_per_row', 2));
    $this->addConfig('sorting', $params->get('sorting', 'rand()'));
    $this->addConfig('resultbytime', $params->get('resultbytime', 0));
    if($this->getConfig('resultbytime') == 99)
    {
      $this->addConfig('resultbytimefrom', $params->get('resultbytimefrom', 0));
      $this->addConfig('resultbytimeto', $params->get('resultbytimeto', 0));
    }
    $this->addConfig('cats', $params->get('cats', ''));
    $this->addConfig('showorhidecats', $params->get('showorhidecats', 1));
    $this->addConfig('includesubcats', $params->get('includesubcats', 0));
    $this->addConfig('dynamiccats', $params->get('dynamiccats', 0));
    $this->addConfig('showhidden', $params->get('showhidden', 0));
    $this->addConfig('showfeatured', $params->get('showfeatured', 0));
    if(is_numeric($params->get('votesctsel')) && $params->get('votesctsel') >= 0)
    {
      $this->addConfig('votesctsel', $params->get('votesctsel', -1));
    }
    else
    {
      $this->addConfig('votesctsel', -1);
    }

    $this->addConfig('pagination', $params->get('pagination', 0));
    $this->addConfig('paginationct', $params->get('paginationct', 0));
    $this->addConfig('paginationpos', $params->get('paginationpos', 0));

    // Deactivate pagination if number of images per site >= limit
    if($this->getConfig('paginationct') >= $this->getConfig('limit'))
    {
      $this->addConfig('pagination', 0);
    }

    $this->addConfig('crop_img', $params->get('crop_img', 0));
    $this->addConfig('crop_pos', $params->get('crop_pos', 2));
    $this->addConfig('crop_sizewidth', $params->get('crop_sizewidth', 50));
    $this->addConfig('crop_sizeheight', $params->get('crop_sizeheight', 150));
    $openimage = $params->get('openimage', 'default');
    if($openimage == 'cat')
    {
      $this->addConfig('catlink', 1);
    }
    else
    {
      if($openimage != 'default' && $openimage != 'none')
      {
        $this->addConfig('openimage', $openimage);
      }
    }
    if($openimage == 'none')
    {
      $this->addConfig('setjilink', 0);
    }
    else
    {
      $this->addConfig('setjilink', 1);
    }
    $this->addConfig('openimagesrc', $params->get('openimagesrc', 'img'));
    $this->addConfig('show_empty_message', $params->get('show_empty_message', 1));
    $this->addConfig('image_position', $params->get('image_position', 1));
    $this->addConfig('auto_resize', $params->get('auto_resize', 0));
    $this->addConfig('auto_resize_max', $params->get('auto_resize_max', 100));
    $this->addConfig('imgwidth', $params->get('imgwidth', 0));
    $this->addConfig('imgheight', $params->get('imgheight', 0));
    $this->addConfig('showtext', $params->get('showtext', 1));

    if($this->getConfig('showtext') == 0)
    {
      $this->addConfig('disable_infos', 1);
    }
    $this->addConfig('dateformat', $params->get('dateformat', "%d.%m.%Y"));
    if($this->getConfig('dateformat') == 1)
    {
      $this->addConfig('dateformat', 'DATE_FORMAT_LC1');
    }
    $this->addConfig('showtitle', $params->get('showtitle', 1));
    $this->addConfig('strtitlewrap', $params->get('strtitlewrap', 0));
    $this->addConfig('showdescription', $params->get('showdescription', 0));
    $this->addConfig('strdescount', $params->get('strdescount', 0));
    $this->addConfig('strdeswrap', $params->get('strdeswrap', 0));
    $this->addConfig('showauthor', $params->get('showuser', 0));
    $this->addConfig('showcategory', $params->get('showcatg', 0));
    $this->addConfig('showhits', $params->get('showhits', 0));
    $this->addConfig('showdownloads', $params->get('showdownloads', 0));
    $this->addConfig('showrate', $params->get('showrating', 0));
    $this->addConfig('showimgdate', $params->get('showimgdate', 0));
    $this->addConfig('showcmtdate', $params->get('showcmtdate', 0));
    $this->addConfig('showcmttext', $params->get('showcmttext', 0));
    $this->addConfig('shownumcomments', $params->get('showcmtcount', 0));
    $this->addConfig('strcmtcount', $params->get('strcmtcount', 0));
    $this->addConfig('strcmtwrap', $params->get('strcmtwrap', 0));
    $this->addConfig('showcmtmore', $params->get('showcmtmore', 0));
    $this->addConfig('scrollthis', $params->get('scrollthis', 0));
    $this->addConfig('scrolldirection', $params->get('scrolldirection', 'left'));
    $this->addConfig('scrollheight', $params->get('scrollheight', 250));
    $this->addConfig('scrollwidth', $params->get('scrollwidth', 230));
    $this->addConfig('scrollamount', $params->get('scrollamount', 1));
    $this->addConfig('scrolldelay', $params->get('scrolldelay', 10));
    $this->scrollmousecode=($params->get('scrollmouse', 1)==1) ? "onmouseover=\"this.stop()\" onmouseout=\"this.start()\"" : "";
    $this->addConfig('dir_hor', $params->get('dir_hor', 'left'));
    $this->addConfig('dir_vert', $params->get('dir_vert', 'top'));
    $this->addConfig('sectiontableentry', $params->get('sectiontableentry', 0));

    $this->addConfig('slideshowthis', $params->get('slideshowthis', 0));

    if(    $this->getConfig('showtext')        == 1
        && $this->getConfig('showtitle')       == 0
        && $this->getConfig('showdescription') == 0
        && $this->getConfig('showauthor')      == 0
        && $this->getConfig('showcategory')    == 0
        && $this->getConfig('showhits')        == 0
        && $this->getConfig('showdownloads')   == 0
        && $this->getConfig('showrate')        == 0
        && $this->getConfig('showimgdate')     == 0
        && $this->getConfig('showcmtdate')     == 0
        && $this->getConfig('showcmttext')     == 0
        && $this->getConfig('shownumcomments') == 0
      )
    {
      $this->addConfig('disable_infos', 1);
    }

    $this->addConfig('type', $params->get('type', 'thumb'));
    $this->addConfig('piclinkslideshow', $params->get('piclinkslideshow', 0));
    $this->addConfig('showCaption', $params->get('showCaption', 1));
    $this->addConfig('showTitleCaption', $params->get('showTitleCaption', 1));
    $this->addConfig('heightCaption', $params->get('heightCaption', 45));
    $this->addConfig('width', $params->get('width', 400));
    $this->addConfig('height', $params->get('height', 300));
    $this->addConfig('imageDuration', $params->get('imageDuration', 9000));
    $this->addConfig('transDuration', $params->get('transDuration', 2000));
    $this->addConfig('transType', $params->get('transType', 'combo'));
    $this->addConfig('transition', $params->get('transition', 'Expo.easeOut'));
    $this->addConfig('pan', $params->get('pan', 50));
    $this->addConfig('zoom', $params->get('zoom', 50));
    $this->addConfig('loadingDiv', $params->get('loadingDiv', 1));
    $this->addConfig('imageResize', $params->get('imageResize', 1));
    $this->addConfig('titleSize', $params->get('titleSize', '13px'));
    $this->addConfig('titleColor', $params->get('titleColor', '#fff'));
    $this->addConfig('titleBGColor', $params->get('titleBGColor', '#000'));

    $this->addConfig('csstag', "joomimg".$moduleid."_");

    // CSS border
    $this->addConfig('border', $params->get('border', 0));
    $this->addConfig('borderwidth', $params->get('borderwidth', '2px'));
    $this->addConfig('borderstyle', $params->get('borderstyle', 'solid'));
    $this->addConfig('bordercolor', $params->get('bordercolor', '#000'));
    $this->addConfig('borderpadding', $params->get('borderpadding', '2px'));
  }

  /**
   * Assemble the query for reading the image data from database
   *
   * @return object - image objects
   */
  function getDBImages()
  {
    $db       = JFactory::getDBo();
    $query    = $db->getQuery(true);
    $user     = JFactory::getUser();
    $limit    = $this->getConfig('limit');
    $sorting  = $this->getConfig('sorting');
    $objects  = array();

    // Deny sorting by nametags if not activated or no permission
    if(stristr($sorting, 'n.ndate'))
    {
      if($this->getJConfig('jg_nameshields') == 0)
      {
        return $objects;
      }
      else
      {
        if($user->guest && !$this->getJConfig('jg_nameshields_unreg'))
        {
          return $objects;
        }
      }
    }

    if($this->cmtTest(1))
    {
      if($this->getConfig('sorting') == "commentrand")
      {
        $sorting = 'rand()';
      }
    }

    // *** Assemble the select part ***
    $query->select('p.id AS id, p.catid, p.imgthumbname, p.imgfilename, p.imgtitle,p.imgtext');

    // Show author
    if($this->getConfig('showauthor'))
    {
      $query->select('p.imgauthor, p.owner');
    }

    // Show category name
    if($this->getConfig('showcategory'))
    {
      $query->select('c.name AS cattitle');
    }

    // Show hits
    if($this->getConfig('showhits'))
    {
      $query->select('p.hits');
    }

      // Show downloads
    if($this->getConfig('showdownloads'))
    {
      $query->select('p.downloads');
    }

    // Show rating or sort by rating
    if(    stristr($this->getConfig('sorting'), 'rating')
        || $this->getConfig('showrate')
      )
    {
      $query->select('p.imgvotes');
      $query->select(JoomHelper::getSQLRatingClause().' AS rating');
    }

    // Show date of image
    if($this->getConfig('showimgdate') || $this->getConfig('showpicasnew'))
    {
      $query->select('p.imgdate as imgdate');
    }

    $query->select('c.cid AS ccid, c.catpath AS catpath');

    if(stristr($sorting, 'ntcount'))
    {
      $query->select('COUNT(nid) FROM '._JOOM_TABLE_NAMESHIELDS.' AS na'
                      .'  WHERE p.id=na.nid) AS ntcount');
    }

    if($this->cmtTest(1))
    {
      $cmtdate = 'co.cmtdate';
      if($sorting == 'co.cmtdate ASC')
      {
        $cmtdate = 'MAX(co.cmtdate)';
        $sorting = 'cmtdate ASC';
      }
      else if($sorting == 'co.cmtdate DESC')
      {
        $cmtdate = 'MAX(co.cmtdate)';
        $sorting = 'cmtdate DESC';
      }
      $query->select($cmtdate.' AS cmtdate');
    }

    // Check if text option 'number of comments'
    // or 'sorting by comments' activated
    if(   $this->getConfig('shownumcomments')
       || strstr($sorting, 'cmtcount'))
    {
      $query->select('count(co.cmtid) AS cmtcount');
    }

    // *** Assemble the from part ***
    if($this->cmtTest(1))
    {
      $query->from(_JOOM_TABLE_COMMENTS.' as co');
    }
    else
    {
      $query->from(_JOOM_TABLE_IMAGES.' AS p');
    }

    // *** Assemble the join clauses ***
    if($this->cmtTest(1))
    {
      $query->join('RIGHT',_JOOM_TABLE_IMAGES.' AS p ON co.cmtpic = p.id');
    }

    $query->join('LEFT',_JOOM_TABLE_CATEGORIES.' AS c ON c.cid = p.catid');

    // If sorting by date of nametags
    if(stristr($sorting, 'n.ndate'))
    {
      $query->join('LEFT',_JOOM_TABLE_NAMESHIELDS.' AS n ON n.npicid = p.id');
    }

    // Check if any comment text options activated
    if($this->getConfig('shownumcomments') && !$this->cmtTest(1))
    {
      $query->join('LEFT',_JOOM_TABLE_COMMENTS.' AS co ON co.cmtpic = p.id');
    }

    // *** Assemble the where clauses ***
    $authorisedViewLevels = implode(",", $user->getAuthorisedViewLevels());
    $query->where('c.published = 1');
    $query->where('c.access IN ('.$authorisedViewLevels.')');
    $query->where('p.published = 1');
    $query->where('p.approved = 1');
    $query->where('p.access IN ('.$authorisedViewLevels.')');

      // Inheritance of category access must be considered here
    $allowed_cids = array_keys($this->_ambit->getCategoryStructure());
    if(!empty($allowed_cids))
    {
      $query->where('p.catid IN ('.implode(',', $allowed_cids).')');
    }

    // Check the current category shown in JoomGallery
    if(    $this->getConfig('dynamiccats')
        && ($currcat =$this->getCurrentCat()) != 0
       )
    {
      $query->where('p.catid = '.$currcat);
    }

    // Show or hide categories
    if($this->getConfig('cats'))
    {
      $catinnotin = $this->getConfig('showorhidecats') == 1 ? ' IN' : ' NOT IN';

      if($this->getConfig('includesubcats'))
      {
        // Include subcategories
        $catsincsubcats = $this->getSubcategories($this->getConfig('cats'));
        $query->where('p.catid'.$catinnotin.' ('.$catsincsubcats.')');
      }
      else
      {
        $query->where('p.catid'.$catinnotin.' ('.$this->getConfig('cats').')');
      }
    }

    // Timespan filter
    if($this->getConfig('resultbytime') != 0)
    {
      $query->where($this->getSQLTimestring($this->getConfig('resultbytime')));
    }

    // Show only not hidden images from not hidden categories
    if(!$this->getConfig('showhidden'))
    {
      $query->where('c.hidden    = 0');
      $query->where('c.in_hidden = 0');
      $query->where('p.hidden    = 0');
    }

    // Show only featured images
    if($this->getConfig('showfeatured'))
    {
      $query->where('p.featured  = 1');
    }

    // Show only images with votes = x
    if($this->getConfig('votesctsel') != -1)
    {
       $query->where('p.imgvotes = '.$this->getConfig('votesctsel'));
    }

    // *** Assemble the group clause ***
    // Check if any comment sorting active
    if($this->cmtTest(1))
    {
      $query->group('p.id');
    }

    // *** Assemble the order clause ***
    $query->order($sorting);

    $limit = $this->getConfig('limit');
    // Total number of images or pagination set
    if($limit != 0)
    {
      $db->setQuery($query, 0, $limit);
    }
    else
    {
      $db->setQuery($query);
    }

    $objects = $db->loadObjectList('id');

    if ($error = $db->getErrorMsg())
    {
      throw new Exception($error);
    }

    // Get the date and/or text from last comment if one of the options activated
    if($this->cmtTest(2))
    {
      $this->getLastComments($objects);
    }

    // Deactivate pagination if there are not enough images
    if(    $this->getConfig('pagination')
      && count($objects) <= $this->getConfig('paginationct')
    )
    {
      $this->addConfig('pagination', 0);
    }

    return $objects;
  }

  /**
   * Get the IDs of subcategories
   * @return string with catids
   */
  function getSubcategories()
  {
    $cats = array();

    $cats = explode(',', $this->getConfig('cats'));
    if(count($cats) == 0)
    {
      return '';
    }

    // Delete double values
    $cats = array_unique($cats);

    $subcats = array();
    // Iterate through array and call getAllSubCategories
    // in helper class of JoomGallery
    foreach($cats as $cat)
    {
      if(!in_array($cat, $subcats))
      {
        $subcats = array_merge($subcats, JoomHelper::getAllSubCategories($cat));
      }
    }
    $cats = array_merge($cats, $subcats);
    // Delete double values
    $cats = array_unique($cats);

    $catsstring = implode(',', $cats);
    return $catsstring;
  }

 /**
 * Create the where clause for timespan dependent query
 *
 * @param integer $option $this->getConfig('resultbytime')
 * @return string where clause
 */
  function getSQLTimestring ($option)
  {
    $timequery = '';
    switch ($option)
    {
      // Current day
      case 1:
        $timequery = 'p.imgdate >= CURRENT_DATE()';
        break;
      // Current week
      case 2:
        $startWeek = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24);
        $timequery = 'p.imgdate >= FROM_UNIXTIME('.$startWeek.')';
        break;
      // Current month
      case 3:
        $startMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $timequery = 'p.imgdate >= FROM_UNIXTIME('.$startMonth.')';
        break;
      // Current year
      case 4:
        $startYear = mktime(0, 0, 0, 1, 1, date('Y'));
        $timequery = 'p.imgdate >= FROM_UNIXTIME('.$startYear.')';
        break;
      // Last 24 hours
      case 5:
        $timequery = 'p.imgdate >= (NOW() - INTERVAL 1 DAY)';
        break;
      // Last 7 days
      case 6:
        $timequery = 'p.imgdate >= (NOW() - INTERVAL 7 DAY)';
        break;
      // Last 30 days
      case 7:
        $timequery = 'p.imgdate >= (NOW() - INTERVAL 30 DAY)';
        break;
      // Last 12 months
      case 8:
        $timequery = 'p.imgdate >= (NOW() - INTERVAL 12 MONTH)';
        break;
      // Free settings
      case 99:
        $timefrom   = $this->getConfig('resultbytimefrom');
        $timeto = $this->getConfig('resultbytimeto');

        if($timefrom == '0' && $timeto == '0')
        {
          break;
        }
        if($timefrom == '0' && $timeto != '0')
        {
          // Timespan until 'to'
          $timequery = "p.imgdate <= STR_TO_DATE('".$timeto."',GET_FORMAT(DATE,'EUR'))";
        }
        else if($timefrom != '0' && $timeto  == '0')
        {
          // Timespan since 'from'
          $timequery = "p.imgdate >= STR_TO_DATE('".$timefrom."',GET_FORMAT(DATE,'EUR'))";
        }
        else
        {
          $timequery = "p.imgdate BETWEEN STR_TO_DATE('".$timefrom."',GET_FORMAT(DATE,'EUR'))"
                     . " AND STR_TO_DATE('".$timeto."',GET_FORMAT(DATE,'EUR'))";
        }

        break;
      default:
        $timequery= '';
        break;
    }
    return $timequery;
  }

  /**
   * Get the html code for text fields of image
   *
   * @param object $obj - picture object
   * @return string - html code
   */
  function showText($obj)
  {
    $output = "";

    // Decode BB-Tags and shorten the text of comment
    if(    $this->getConfig('showcmttext')
        && !is_null($obj->cmtdate))
    {
      $obj->cmttext = $this->decodetext($obj->cmttext,
                                        $this->getConfig('strcmtcount'),
                                        $this->getConfig('strcmtwrap'),
                                        $this->getConfig('showcmtmore'));
    }

    // Comment text
    if(    $this->getConfig('showcmtmore') == 1
       && $this->getConfig('strcmtcount') > 0
       && $this->getJConfig('jg_detailpic_open') == 0
       && ((    $this->getJConfig('jg_showdetailpage') == 0 && $user->get('aid') != 0)
             || $this->getJConfig('jg_showdetailpage') == 1)
      )
    {
      $obj->cmttext .= '&nbsp;&#0133;<a href="'
                      .$this->route('index.php?view=detail&id='.$obj->id)
                      .'#joomcomments'.'">'
                      .JText::_('READMORE').'</a>';
    }
    $output = $this->displayDesc($obj);

    return $output;
  }

  /**
   * Read the last comment of images and complete the image objects
   *
   * @param array $objects
   */
  function getLastComments($objects)
  {
    if(count($objects))
    {
      $db    = JFactory::getDBo();
      $query = $db->getQuery(true);

      $picidsarr = array_keys($objects);
      $picids = '('.implode(',', $picidsarr).')';
      $query->select('co.cmtpic AS cocmtpic');

      if($this->getConfig('showcmttext'))
      {
        $query->select('co.cmttext');
      }

      $query->select('co.cmtdate as cmtdate, co.userid AS cmtuserid, co.cmtname AS cmtname');
      $query->from(_JOOM_TABLE_COMMENTS.' AS co');

      $query->join('LEFT',_JOOM_TABLE_COMMENTS.' AS co2'
                   .' ON co.cmtpic = co2.cmtpic AND co.cmtdate < co2.cmtdate'
                   .' WHERE'
                   .'   co2.cmtpic IS NULL'
                   .'   AND'
                   .'     co.cmtpic IN '.$picids
                   .'   AND ((co.published=1 AND co.approved=1) OR co.published IS NULL)');

      $db->setQuery($query);
      $commobjects = $db->loadObjectList('cocmtpic');

      // And complete objects with data of last comment
      foreach($objects as $key => &$object )
      {
        $object->cmttext   = isset($commobjects[$key]->cmttext) ? $commobjects[$key]->cmttext:null;
        $object->cmtdate   = isset($commobjects[$key]->cmtdate) ? $commobjects[$key]->cmtdate:null;
        $object->cmtuserid = isset($commobjects[$key]->cmtuserid) ? $commobjects[$key]->cmtuserid:null;
        $object->cmtname   = isset($commobjects[$key]->cmtname) ? $commobjects[$key]->cmtname:null;
      }
    }
  }


  /**
   * Assembles text and html for image elements of default view
   * @param $objects array aof images
   */
  function modContent(&$objects)
  {
    $csstag = $this->getConfig("csstag");
    $imgcount = 0;
    if(!count($objects))
    {
      return;
    }
    foreach($objects as $key => $obj)
    {
      $imgcount++;
      // Wordwrap for imgtitle
      if($this->getConfig('strtitlewrap') > 0)
      {
        $objects[$key]->imgtitle = wordwrap($obj->imgtitle,$this->getConfig('strtitlewrap'),'<br />', true);
      }

      // Shorten the image description
      if(    $this->getConfig('strdescount') > 0
          && isset($obj->imgtext) && strlen($obj->imgtext)>0
        )
      {
        if(strlen($obj->imgtext) > $this->getConfig('strdescount'))
        {
          $objects[$key]->imgtext = substr(strip_tags($objects[$key]->imgtext), 0, $this->getConfig('strdescount')).'&hellip;';
        }
      }

     // Wordwrap for image description
      if(    $this->getConfig('strdeswrap') > 0
          && isset($obj->imgtext) && strlen($obj->imgtext) > 0
        )
      {
        $objects[$key]->imgtext = wordwrap(strip_tags($obj->imgtext), $this->getConfig('strdeswrap'), '<br />', true);
      }

      // Check for link to category
      if($this->getConfig('catlink') == 1)
      {
        $objects[$key]->link = $this->route('index.php?view=category&catid='.$obj->catid);
      }
      else
      {
        // Otherwise link to detail view
        $objects[$key]->link = $this->getImageLink($obj, $this->getConfig('openimagesrc'));
      }

      // Get the image dimensions to set the CSS width/height styles
      $objects[$key]->css_styledimension = '';
      if($this->getConfig('image_position') != 0)
      {
        switch($this->getConfig('type'))
        {
          case 'thumb':
            $imgpath   = JPATH_SITE.'/'.$this->getJConfig('jg_paththumbs');
            break;
          case 'img':
            $imgpath   = JPATH_SITE.'/'.$this->getJConfig('jg_pathimages');
            break;
          case 'orig':
            $imgpath   = JPATH_SITE.'/'.$this->getJConfig('jg_pathoriginalimages');
            break;
        }

        $imgsize   = getimagesize($imgpath.$obj->catpath.'/'.$obj->imgthumbname);
        $imgWidth  = $imgsize[0];
        $imgHeight = $imgsize[1];

        // Set the CSS width/height styles
        // in case of auto_resize determine the max settings with keeping the ratio
        if($this->getConfig('auto_resize'))
        {
          // Get the max dimension
          $maxdim=(int) $this->getConfig('auto_resize_max');

          if($imgWidth > $imgHeight)
          {
            // Set width as max. dimension
            $ratio      = $imgWidth/$maxdim;
            $imgWidth   = $maxdim;
            $imgHeight  = (int)($imgHeight / $ratio);
          }
          else
          {
            // Set height as max. dimension
            $ratio       = $imgHeight/$maxdim;
            $imgHeight   = $maxdim;
            $imgWidth    = (int)($imgWidth / $ratio);
          }
          $objects[$key]->css_styledimension = ' style="height:'
            .$imgHeight
            .'px;width:'
            .$imgWidth
            .'px;" ';
          $objects[$key]->imagesource = $this->route($this->_ambit->getImg($this->getConfig('type').'_url', $obj,null,0,false));
        }
        else if($this->getConfig('crop_img'))
        {
          // Crop the image, use the crop functionality of JoomGallery
          $cropsizewidth  = $this->getConfig('crop_sizewidth');
          $cropsizeheight = $this->getConfig('crop_sizeheight');
          $imgHeight      = $cropsizeheight;
          $imgWidth       = $cropsizewidth;
          $croppos   = $this->getConfig('crop_pos');
          $objects[$key]->imagesource = $this->route($this->_ambit->getImg($this->getConfig('type').'_url', $obj,null,0,false,$cropsizewidth, $cropsizeheight, $croppos));
        }
        else
        {
          // Set the default
          $objects[$key]->imagesource = $this->route($this->_ambit->getImg($this->getConfig('type').'_url', $obj,null,0,false));
        }

        $objects[$key]->css_styledimension = ' style="height:'
          .$imgHeight
          .'px;width:'
          .$imgWidth
          .'px;" ';
      }

      switch ($this->getConfig('image_position'))
      {
        case 0:
          // No image
          $objecttxt=$this->showText($obj);
          if(!empty($objecttxt))
          {
            $objects[$key]->imgelem = '<div class="'.$csstag.'txt">'."\n"
                                     .$objecttxt."\n"
                                     .'</div>'."\n";
          }
          else
          {
            $objects[$key]->imgelem = '';
          }
          break;
        case 1:
        case 2:
        case 3:
          // Image above (1) or left (2) or right(3) to text
          $objects[$key]->imgelem = '<div class="'.$csstag.'img">'."\n";
          if($this->getConfig('setjilink'))
          {
            $objects[$key]->imgelem .= '  <a href="'.$obj->link.'" >';
          }

          $objects[$key]->imgelem .= '    <img src="'
                   .$obj->imagesource.'"'
                   .$obj->css_styledimension
                   .' alt="'
                   .$obj->imgtitle.'"'
                   .' title="'
                   .$obj->imgtitle.'" />';

          if($this->getConfig('setjilink'))
          {
            $objects[$key]->imgelem .= '  </a>';
          }
          $objects[$key]->imgelem .= '</div>'."\n";

          $objecttxt=$this->showText($obj);
          if(!empty($objecttxt))
          {
            $objects[$key]->imgelem .= '<div class="'.$csstag.'txt">'."\n"
                                     .$objecttxt."\n"
                                     .'</div>'."\n";
          }
          break;
        case 4:
          //image below text
          //delete the  / from catpath
          $catpath = trim($obj->catpath, '/');
          $objecttxt=$this->showText($obj);
          if(!empty($objecttxt))
          {
            $objects[$key]->imgelem .= '<div class="'.$csstag.'txt">'."\n"
                                     .$objecttxt."\n"
                                     .'</div>'."\n";
          }
          $objects[$key]->imgelem .= '<div class="'.$csstag.'img">'."\n";
          if($this->getConfig('setjilink'))
          {
            $objects[$key]->imgelem .= '  <a title="'.$obj->imgtitle.'" href="'.$obj->link.'" >';
          }

          $objects[$key]->imgelem .= '    <img src="'
                   .$obj->imagesource.'"'
                   .$obj->css_styledimension
                   .' alt="'
                   .$obj->imgtitle.'"'
                   .' title="'
                   .$obj->imgtitle.'" />';

          if($this->getConfig('setjilink'))
          {
            $objects[$key]->imgelem .= '  </a>';
          }
          $objects[$key]->imgelem .= '</div>'."\n";
          break;
      }

      // Check for pagination
      if ($this->getConfig('pagination'))
      {
        // If slimbox/thickbox/plugin activated, remove the parts of <a> tag
        // which could trigger the box, so only the hidden elements should be
        // scanned by the box

        // Save the original code in object pagination variable
        $objects[$key]->pagelem = $objects[$key]->imgelem;
        $elemchanged = false;

        // Check for rel="lightbox"
        $firstpos = strpos($objects[$key]->imgelem, 'rel="lightbox');
        if($firstpos !== false)
        {
          $elemchanged = true;
          $lastpos = strpos($objects[$key]->imgelem, '"', $firstpos + 5);
          $objects[$key]->imgelem=substr_replace($objects[$key]->imgelem, '', $firstpos, $lastpos-$firstpos);
        }
        // Check for class="thickbox"
        $firstpos = strpos($objects[$key]->imgelem, 'class="thickbox');
        if($firstpos !== false)
        {
          $elemchanged = true;
          $lastpos = strpos($objects[$key]->imgelem, '"', $firstpos + 7);
          $objects[$key]->imgelem=substr_replace($objects[$key]->imgelem, '', $firstpos, $lastpos-$firstpos);
        }
        if(!$elemchanged)
        {
          // Nothing changed, set the reference to save memory
          $objects[$key]->pagelem = &$objects[$key]->imgelem;
        }
      }
    }
  }

  /**
   * Check the backend for activated settings according comments to choose
   * the right DB function or to add the query in getDBImages()
   *
   * @param int $only_comments 1 = check for comment sorts in DB
   *                           2 = check for comment texts
   * @return bool
   */
  function cmtTest($mode)
  {
    // Test if sorting by comments setted
    if($mode == 1)
    {
      if(!is_null($this->cmtsort))
      {
        return $this->cmtsort;
      }
      if(    strstr($this->getConfig('sorting'), 'cmtcount')
          || strstr($this->getConfig('sorting'), 'cmtdate')
          || strstr($this->getConfig('sorting'), 'commentrand')
        )
      {
        $this->cmtsort = true;
      }
      else
      {
        $this->cmtsort = false;
      }
      return $this->cmtsort;
    }

    // Test if output of one ore more comment texts active
    if(!is_null($this->cmttext))
    {
      return $this->cmttext;
    }

    if(    $this->getConfig('showcmtdate')
        || $this->getConfig('showcmttext')
        || $this->getConfig('shownumcomments')
      )
    {
      $this->cmttext = true;
    }
    else
    {
      $this->cmttext = false;
    }
    return $this->cmttext;
  }


  /**
   * Return the id of category if in category or detail view
   *
   * @return int - category id or 0 when not found
   */
  function getCurrentCat()
  {
    $database = JFactory::getDBO();
    $view = $database->escape(trim(JRequest::getVar('view', '')));

    if($view != 'detail' && $view != 'category')
    {
      return 0;
    }

    $catid     = JRequest::getInt('catid', 0);
    $id        = JRequest::getInt('id', 0);

    if($view == 'category' && $catid != 0)
    {
      return $catid;
    }
    else if($view == 'detail' && $id != 0)
    {
      $query  = ' SELECT '
              . '   catid'
              . ' FROM '
              .     _JOOM_TABLE_IMAGES
              . ' WHERE'
              . '   id = '.$id;

      $database->setQuery($query);
      return $database->loadResult();
    }
    else
    {
      return 0;
    }
  }

  /**
   * Generate CSS statements and include them at the head of document
   *
   */
  function renderCSS()
  {
    $containerwidth=floor(100/$this->getConfig('img_per_row'));
    $csstag=$this->getConfig("csstag");

    $dirhoriz='text-align:'.$this->getConfig('dir_hor').'!important;'."\n";
    $dirvert='vertical-align:'.$this->getConfig('dir_vert').'!important;'."\n";
    $csscont='float:left;'."\n";

    switch ($this->getConfig('image_position'))
    {
      case 0:
        // No image
        $cssimg ='';
        $csstxt = $dirhoriz.$dirvert;
        break;
      case 1:
        // Image above text
        $cssimg = 'display:block;'."\n";
        $cssimg .= $dirhoriz.$dirvert;
        $csstxt = 'clear:both;'.$dirhoriz.$dirvert;
        break;
      case 2:
        // Image left from text
        $cssimg = 'float:left;'."\n";
        $csstxt = 'float:left;'."\n";
        $cssimg .= $dirhoriz.$dirvert;
        $csstxt .= $dirhoriz.$dirvert;
        break;
      case 3:
        // Image right from text
        $cssimg = 'float:right;'."\n";
        $csstxt = 'float:right;'."\n".'padding-right:0.5em;'."\n";
        $cssimg .= $dirhoriz.$dirvert;
        $csstxt .= $dirhoriz.$dirvert;
        break;
      default:
        // Image below text
        $cssimg = $dirhoriz.$dirvert;
        $csstxt = $dirhoriz.$dirvert;
        break;
    }

    // CSS for border if image displayed and 'border' = yes
    if($this->getConfig('image_position') != 0 && $this->getConfig('border') ==1 )
    {
      $cssborder='border:'
        .$this->getConfig('borderwidth')
        .' '
        .$this->getConfig('borderstyle')
        .' '
        .$this->getConfig('bordercolor')
        .";\n".'padding:'.$this->getConfig('borderpadding')
        .';';
    }
    else
    {
      $cssborder='';
    }

    $css="";
    // Container
    $css .= '.'.$csstag.'imgct {'."\n"
          . 'width:'.$containerwidth.'% !important;'."\n"
          . $csscont
          .'}'."\n";

    // Image
    if(!empty($cssimg))
    {
      $css .= '.'.$csstag.'img {'."\n"
        . $cssimg
        .'}'."\n";

      // Border for image
      if(!empty($cssborder))
      {
        $css .= '.'.$csstag.'img img{'."\n"
          . $cssborder
          .'}'."\n";
      }
    }

    // Text
    if(!empty($csstxt))
    {
      $css .= '.'.$csstag.'txt {'."\n"
           . $csstxt
           .'}'."\n";
    }

    // Define height/width of images if setted
    // not when auto_resize activated
    if(    !$this->getConfig('auto_resize')
        && !$this->getConfig('crop_img')
      )
    {
      if(    $this->getConfig('imgwidth') != 0
          || $this->getConfig('imgheight')!= 0
        )
      {
        $imgcss='';
        if($this->getConfig('imgwidth') != 0)
        {
          $imgcss .="\n".'width:'.$this->getConfig('imgwidth').'px;';
        }
        if($this->getConfig('imgheight') != 0)
        {
          $imgcss .="\n".'height:'.$this->getConfig('imgheight').'px;';
        }

        $css .= '.'.$csstag.'img img {'."\n"
             . $imgcss
             .'}'."\n";
      }
    }

    // Pagination if activated
    if($this->getConfig('pagination'))
    {
      $css .= '.'.$csstag.'pagnavi{'."\n"
        . '  text-align:center;'."\n"
        .'}'."\n";

      // current site
      $css .= '.'.$csstag.'paglinkactive{'."\n"
        . '  border:solid 1px #000;'."\n"
        . '  margin-bottom:4px;'."\n"
        . '  padding:2px;'."\n"
        . '  background-color:#ddd;'."\n"
        .'}'."\n";

      // other site
      $css .= '.'.$csstag.'paglink{'."\n"
        . '  border:solid 1px #000;'."\n"
        . '  margin-bottom:4px;'."\n"
        . '  padding:2px;'."\n"
        .'}'."\n";


    }
    $document = JFactory::getDocument();
    $document->addStyleDeclaration($css);
  }

  /**
   * Decode BB-tags, replace URL and shorten text
   *
   * @param $text
   * @param $newlength
   * @param $wrap
   * @param $more
   */
  function decodetext($text, $newlength = 0, $wrap = 0, $more = 0)
  {
    // Remove whitespace at start and end of the text
    $text = trim($text);
    $newlength = ($newlength!=0) ? $newlength-1 : 0;
    $smileys = JoomHelper::getSmileys();

    // Define replace tags
    $replace1  = array('[url]','[/url]','[email]','[/email]');
    $replace21 = array('[b]','[i]','[u]');
    $replace22 = array('[/b]','[/i]','[/u]');
    $replace2  = array_merge($replace21, $replace22);
    $replace3  = array('<b>','<i>','<u>','</b>','</i>','</u>');

    // Replace url and emailtags because we do not show them in our modules
    foreach($replace1 as $replace)
    {
      $text = str_replace($replace, '', $text);
    }
    $textlength = strlen($text);
    // If text has to be in a range we abridge him
    if($newlength > 0 && $newlength < $textlength)
    {
      $add = '';

      // Replace simple html-tags with bb_code
      for($i=0;$i<count($replace3);$i++)
      {
        $text = str_replace($replace3[$i], $replace2[$i], $text);
      }

      // Replace smilies with shorttags or remove them
      if($this->getJConfig('jg_smiliesupport'))
      {
        $count=0;
        $smileshort = array();
        foreach($smileys as $i=>$sm)
        {
          $text = str_replace($i, '{'.$count.'}', $text);
          $smileshort[$count]['short'] = $i;
          $smileshort[$count]['long']  = $sm;
          $count++;
        }
      }
      else
      {
        foreach($smiley as $i=>$sm)
        {
          $text = str_replace($i, "", $text);
        }
      }
      $textlength = strlen($text);
    }
    // Remove any html because it is too complicated to handle them
    if($wrap > 0)
    {
      $text = strip_tags($text);
      $textlength = strlen($text);
      if($wrap > 0 && $textlength > $wrap)
      {
        $text = wordwrap($text,$wrap,'<br />',true);
      }
    }

    // If wrap is activated count the containing <br />
    // and add their length to $newlength
    if($wrap > 0)
    {
      $countbrstr=substr($text, 0, $newlength);
      // Count the <br />
      $countbr=substr_count($countbrstr, '<br />');
      if($countbr > 0)
      {
        $newlength = $newlength + ($countbr*6);
      }
    }
    $textlength = strlen($text);

    // Slice if needful
    if($newlength != 0 && $textlength > ($newlength+1))
    {
      // Check a sliced <br />
      if(($textlength-6) > 0 && ($newlength-6) > 0)
      {
        $strposfound = strpos($text, '<br />', $newlength-6);
      }
      else
      {
        $strposfound = 0;
      }
      if($strposfound > 0 && $strposfound < $newlength)
      {
        // Slice before the begin of the <br />
        $newlength = $strposfound;
      }
      else
      {
        // Check a sliced bbcode tag and shorten newlength
        foreach($replace2 as $replace)
        {
          $replacelength = strlen($replace);
          if(    $textlength > ($newlength-$replacelength)
              && ($newlength-$replacelength) > 0
            )
          {
            $strposfound = strpos($text, $replace, $newlength-$replacelength);
          }
          else
          {
            $strposfound = 0;
          }
          if($strposfound > 0 && $strposfound < $newlength)
          {
            $newlength = $strposfound;
            break;
          }
        }
        // Check a sliced smilie tag and shorten newlength
        if(isset($smileshort))
        {
          for($i=0; $i<count($smileshort); $i++)
          {
            $replacelength = strlen($i)+2;
            if(    $textlength > ($newlength-$replacelength)
                && ($newlength-$replacelength) > 0
              )
            {
              $strposfound = strpos($text, "{".$i."}", $newlength - $replacelength);
            }
            else
            {
              $strposfound = 0;
            }
            if($strposfound > 0 && $strposfound < $newlength)
            {
              $newlength = $strposfound;
              break;
            }
          }
        }
      }
      // Slice the text
      $text = substr($text, 0, $newlength);
    }

    // Adding mising tags at the end of the text
    if( $this->getJConfig('jg_bbcodesupport'))
    {
      $prioarr = array();
      // Build an array for the priority in replacing
      $countreplace = count($replace21);
      for($i=0; $i < $countreplace; $i++)
      {
        // Check if there is an unbalance
        // of opening and closing tags
        $countopen = substr_count($text, $replace21[$i]);
        $countclose = substr_count($text, $replace22[$i]);
        $diff = $countopen-$countclose;
        $found = -1;
        while ($diff > 0)
        {
          $found = strpos($text, $replace21[$i], $found+1);
          // Add the closing tag
          $prioarr[$found] = $replace22[$i];
          $diff--;
        }
      }
      if(count($prioarr))
      {
        // Reverse the array to begin with the last element
        arsort($prioarr);
        foreach($prioarr as $key => $value)
        {
          $add .= $value;
        }
      }
    }
    // Abridge text and add missing tags
    if(!empty($add))
    {
      $text = $text.$add;
    }
    // If text was sliced add the ellipsis
    if(    $newlength > 0
        && $textlength > $newlength
        && $more == 0
      )
    {
      $text .= "...";
    }

    // Decode bb_code or remove tags
    if($this->getJConfig('jg_bbcodesupport'))
    {
      $text = JHTML::_('joomgallery.bbdecode', $text);
    }
    else
    {
      foreach($replace2 as $replace)
      {
        $text = str_replace($replace, "", $text);
      }
    }

    // Decode smilies or remove them
    if($this->getJConfig('jg_smiliesupport'))
    {
      foreach($smileys as $i=>$sm )
      {
        $text = str_replace($i, '<img src="'.$sm.'"'.' alt="'.$i.'" />', $text);
      }
      if(isset($smileshort))
      {
        for($i=0;$i<count($smileshort);$i++)
        {
          $text = str_replace('{'.$i.'}', '<img src="'.$smileshort[$i]['long'].'" border="0" alt="'.$smileshort[$i]['short'].'" title="'.$smileshort[$i]['short'].'" />',$text);
        }
      }
    }
    else
    {
      foreach($smileys as $i=>$sm )
      {
        $text = str_replace($i, "", $text);
      }
      if(isset($smileshort))
      {
        for($i=0; $i<count($smileshort); $i++)
        {
          $text = str_replace('{'.$i.'}', '', $text);
        }
      }
    }
    return $text;
  }
}