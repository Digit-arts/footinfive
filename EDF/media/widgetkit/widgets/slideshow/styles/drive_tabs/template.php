<?php 
/**
* @package   Widgetkit Bonus Styles
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

	$widget_id = $widget->id.'-'.uniqid();
	$settings  = $widget->settings;
	$content   = array();
	$nav       = ($settings['navigation']) ? 'nav-'.$settings['navigation'] : '';



	$item_width = floor(100 / count($widget->items));

?>

<div id="slideshow-<?php echo $widget_id; ?>" class="wk-slideshow wk-slideshow-tabs-drive" data-widgetkit="slideshow" data-options='<?php echo json_encode($settings); ?>'>
	
	<div class="slides-container">

		<ul class="slides">
			<?php foreach ($widget->items as $key => $item) : ?>
			<?php $items[] = '<li class="width'. $item_width .'"><span>'.$item['title'].'</span></li>'; ?>
			<li>
				<article class="wk-content clearfix"><?php echo $item['content']; ?></article>
			</li>
			<?php endforeach; ?>
		</ul>

		<?php echo (count($items)) ? '<div class="nav-container '.$nav.' clearfix"><ul class="nav">'.implode('', $items).'</ul></div>' : '';?>

	</div>

</div>