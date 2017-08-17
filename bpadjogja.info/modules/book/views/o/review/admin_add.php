<?php
/**
 * Book Reviews (book-reviews)
 * @var $this ReviewController * @var $model BookReviews *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2014 Ommu Platform (ommu.co)
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Book Reviews'=>array('manage'),
		'Create',
	);
?>

<div class="form" name="post-on">
	<?php echo $this->renderPartial('_form', array(
		'model'=>$model,
		'book'=>$book,
		'author'=>$author,
	)); ?>
</div>
