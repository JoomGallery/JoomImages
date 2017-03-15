<?php

defined('_JEXEC') or die('Restricted access');

// Load jQuery
JHtml::_('jquery.framework');

// Load Justified Gallery's CSS and Javascript
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'media/mod_joomimg/css/justifiedGallery'.(JFactory::getConfig()->get('debug') ? '' : '.min').'.css');
$doc->addScript(JURI::base().'media/mod_joomimg/js/jquery.justifiedGallery'.(JFactory::getConfig()->get('debug') ? '' : '.min').'.js');

$csstag       = $joomimgObj->getConfig("csstag");
$countobjects = count($imgobjects);

$maxRowHeight       = $joomimgObj->getConfig('justifiedmaxrowheight');
$maxRowHeightAdSign = (!is_numeric($maxRowHeight) && $maxRowHeight !== 'false') ? "'" : "";
?>

<div class="<?php echo $csstag; ?>main">
  <div id="<?php echo $csstag; ?>justified_gallery">
<?php
    if($countobjects > 0):
      foreach($imgobjects as $obj):
        echo $obj->imgelem;
      endforeach;
    else:
      if($joomimgObj->getConfig('show_empty_message')):
        echo JText::_('JINO_PICTURES_AVAILABLE');
      endif;
    endif;
?>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery('#<?php echo $csstag; ?>justified_gallery').justifiedGallery({ rowHeight: <?php echo $joomimgObj->getConfig('justifiedrowheight'); ?>,
                                                                          maxRowHeight: <?php echo $maxRowHeightAdSign; ?><?php echo $maxRowHeight; ?><?php echo $maxRowHeightAdSign; ?>,
                                                                          lastRow: '<?php echo $joomimgObj->getConfig('justifiedlastrow'); ?>',
                                                                          captions: <?php echo $joomimgObj->getConfig('justifiedcaptions'); ?>,
                                                                          margins: <?php echo $joomimgObj->getConfig('justifiedmargins'); ?>,
                                                                          border: <?php echo $joomimgObj->getConfig('justifiedborder'); ?>
                                                                       });
  });
</script>
