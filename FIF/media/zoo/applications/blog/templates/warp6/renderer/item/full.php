<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$params = $item->getParams('site');

/* set media alignment */
$align = ($this->checkPosition('media')) ? $view->params->get('template.item_media_alignment') : '';

?>

<?php if ($this->checkPosition('top')) : ?>
<div class="pos-top clearfix"><?php echo $this->renderPosition('top', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($align == "above") : ?>
<div class="pos-media media-top"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('title') || $this->checkPosition('meta')) : ?>
<header>

	<?php if ($date = $params->get('template.date') == 'created' ? $item->created : $params->get('template.date') == 'publishup' ? $item->publish_up : null) : ?>
	<time datetime="<?php echo substr($date, 0,10); ?>" pubdate>
		<?php foreach (explode('||', $params->get('template.date_format', array())) as $format) : ?>
			<span class="<?php echo preg_match('/%a|%A|%d|%e|%j|%u|%w/', $format) ? 'day' : (preg_match('/%b|%B|%h|%m/', $format) ? 'month' : 'year'); ?>"><?php echo $this->app->html->_('date', $date, $this->app->date->format($format), $this->app->date->getOffset()); ?></span>
		<?php endforeach; ?>
	</time>
	<?php endif; ?>

	<?php if ($this->checkPosition('title')) : ?>
	<h1 class="title"><?php echo $this->renderPosition('title'); ?></h1>
	<?php endif; ?>

	<?php if ($this->checkPosition('meta')) : ?>
	<p class="meta"><?php echo $this->renderPosition('meta'); ?></p>
	<?php endif; ?>

</header>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<p class="pos-subtitle"><?php echo $this->renderPosition('subtitle'); ?></p>
<?php endif; ?>

<?php if ($align == "top") : ?>
<div class="pos-media media-top"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<div class="content clearfix">

	<?php if ($align == "left" || $align == "right") : ?>
	<div class="pos-media align-<?php echo $align; ?>"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
	<?php endif; ?>

	<?php if ($this->checkPosition('content')) : ?>
	<div class="pos-content"><?php echo $this->renderPosition('content', array('style' => 'block')); ?></div>
	<?php endif; ?>

</div>

<?php if ($align == "bottom") : ?>
<div class="pos-media media-bottom"><?php echo $this->renderPosition('media', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('taxonomy')) : ?>
<ul class="pos-taxonomy blank"><?php echo $this->renderPosition('taxonomy', array('style' => 'list')); ?></ul>
<?php endif; ?>

<?php if ($this->checkPosition('bottom')) : ?>
<div class="pos-bottom clearfix"><?php echo $this->renderPosition('bottom', array('style' => 'block')); ?></div>
<?php endif; ?>

<?php if ($this->checkPosition('related')) : ?>
<div class="pos-related">
	<h3><?php echo JText::_('Related Articles'); ?></h3>
	<ul class="blank"><?php echo $this->renderPosition('related'); ?></ul>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('author')) : ?>
<section class="pos-author author-box clearfix"><?php echo $this->renderPosition('author', array('style' => 'block')); ?></section>
<?php endif;