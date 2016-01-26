<?php
/**
 * Visit Guests (visit-guest)
 * @var $this GuestController
 * @var $model VisitGuest
 * @var $form CActiveForm
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 26 January 2016, 13:01 WIB
 * @link https://github.com/oMMuCo
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Visit Guests'=>array('manage'),
		$model->guest_id=>array('view','id'=>$model->guest_id),
		'Update',
	);
?>

<div class="form">
	<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
</div>
