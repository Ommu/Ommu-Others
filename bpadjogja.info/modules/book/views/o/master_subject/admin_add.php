<?php
/**
 * Book Master Subjects (book-master-subjects)
 * @var $this MastersubjectController * @var $model BookMasterSubjects *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @copyright Copyright (c) 2014 Ommu Platform (ommu.co)
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */

	$this->breadcrumbs=array(
		'Book Master Subjects'=>array('manage'),
		'Create',
	);
?>

<?php echo $this->renderPartial('/o/master_subject/_form', array('model'=>$model)); ?>
