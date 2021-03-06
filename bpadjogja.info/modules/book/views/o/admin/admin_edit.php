<?php
/**
 * Book Masters (book-masters)
 * @var $this AdminController * @var $model BookMasters *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2014 Ommu Platform (ommu.co)
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Book Masters'=>array('manage'),
		$model->title=>array('view','id'=>$model->book_id),
		'Update',
	);
?>

<div class="form">
	<?php echo $this->renderPartial('_form', array(
		'model'=>$model,
		'author'=>$author,
		'interpreter'=>$interpreter,
		'subject'=>$subject,
	)); ?>
</div>
