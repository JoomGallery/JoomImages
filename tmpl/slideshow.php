<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Modules/JoomImages/trunk/tmpl/slideshow.php $
// $Id: slideshow.php 4411 2014-07-12 08:45:56Z erftralle $
defined('_JEXEC') or die('Restricted access');

$csstag=$joomimgObj->getConfig("csstag");

$imageType  = $joomimgObj->getConfig('type');

$doc =JURI::base( true );
switch ($imageType):
  case 'img':
    $imagePath=$doc."/".$joomimgObj->getJConfig('jg_pathimages');
    break;
  case 'orig':
    $imagePath=$doc."/".$joomimgObj->getJConfig('jg_pathoriginalimages');
    break;
  default:
    $imagePath=$doc."/".$joomimgObj->getJConfig('jg_paththumbs');
    break;
endswitch;

$showLink         = $joomimgObj->getConfig('piclinkslideshow');
$showCaption      = $joomimgObj->getConfig('showCaption');
$showTitleCaption = $joomimgObj->getConfig('showTitleCaption');
//$heightCaption    = $joomimgObj->getConfig('heightCaption');
$width            = $joomimgObj->getConfig('width');
$height           = $joomimgObj->getConfig('height');
$imageDuration    = $joomimgObj->getConfig('imageDuration');
$transDuration    = $joomimgObj->getConfig('transDuration');
$transType        = $joomimgObj->getConfig('transType');
switch($transType):
  case 'flash':
    $transtypejs = 'Slideshow.Flash';
    break;
  case 'fold':
    $transtypejs = 'Slideshow.Fold';
    break;
  case 'kenburns':
    $transtypejs = 'Slideshow.KenBurns';
    break;
  case 'push':
    $transtypejs = 'Slideshow.Push';
    break;
  default:
    $transtypejs = 'Slideshow';
    break;
endswitch;
$transition       = $joomimgObj->getConfig('transition');
$pan              = $joomimgObj->getConfig('pan');
$zoom             = $joomimgObj->getConfig('zoom');
$loadingDiv       = $joomimgObj->getConfig('loadingDiv');
$imageResize      = $joomimgObj->getConfig('imageResize');
$titleSize        = $joomimgObj->getConfig('titleSize');
$titleColor       = $joomimgObj->getConfig('titleColor');
$descSize         = $joomimgObj->getConfig('descSize');
$descColor        = $joomimgObj->getConfig('descColor');

$strip_arr= array("'","\r\n", "\n", "\r");
$firstimg = true;
?>
  <script type="text/javascript">
    window.addEvent('domready', function(){
    var imgs =
    {
<?php
    foreach ($imgobjects as $img):
      if($firstimg):
        $firstimg = false;?>
<?php
      else: ?>
      ,
<?php
      endif;
?>
      '<?php echo $imagePath.$img->catpath."/".$img->imgthumbname; ?>':
      {
<?php
      $caption = '';
      if($showTitleCaption == 1):
        $caption = '<b>' . strip_tags(str_replace($strip_arr, "", $img->imgtitle)) . '</b>';
      endif;
      if($showCaption == 1 && !empty($img->imgtext)):
        if(!empty($caption)):
          $caption .= ' ';
        endif;
        $caption .= strip_tags(str_replace($strip_arr,"",$img->imgtext));;
      endif;
?>
        caption: '<?php echo (empty($caption) ? '-' : $caption); ?>',
<?php
      switch($showLink):
        case 1: //Category
?>
        href: '<?php echo $joomimgObj->route('index.php?view=category&catid='.$img->catid, false);?>'
<?php
        break;
        case 2: //Detail
?>
        href: '<?php echo $joomimgObj->route('index.php?view=detail&id='.$img->id, false);?>'
<?php
        break;
        default:
?>
        href: ''
<?php
        break;
      endswitch;
?>
      }
<?php
    endforeach;
?>
    };
    new <?php echo $transtypejs?>('slideshow<?php echo $moduleid;?>',
      imgs,
      {
        duration: <?php echo $transDuration; ?>,
        delay: <?php echo $imageDuration; ?>,
        width: <?php echo $width; ?>,
        height: <?php echo $height; ?>,
        resize: <?php echo($imageResize=="false"?'false':"'".$imageResize."'"); ?>,
        captions: <?php echo (($showCaption == 1 || $showTitleCaption == 1) ? 'true' : 'false'); ?>,
        loader: <?php echo ($loadingDiv==1?'true':'false') ?>,
        pan: <?php echo $pan; ?>,
        zoom: <?php echo $zoom; ?>,
        transition: '<?php echo $transition; ?>',
        color: '#FFF',
        titles: true,
        center: true,
        controller:false,
        fast:false,
        loop:true,
        overlap:true,
        paused:false,
        random:false,
        thumbnails:false
      }
    );
  });
  </script>
<div class="<?php echo $csstag; ?>main">
    <div id="slideshow<?php echo $moduleid;?>" class="slideshow">
    </div>
</div>