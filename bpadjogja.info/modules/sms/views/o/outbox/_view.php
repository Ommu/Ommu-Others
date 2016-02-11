<?php
/**
 * Sms Outboxes (sms-outbox)
 * @var $this OutboxController
 * @var $data SmsOutbox
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 12 February 2016, 04:07 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('outbox_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->outbox_id), array('view', 'id'=>$data->outbox_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('group_id')); ?>:</b>
	<?php echo CHtml::encode($data->group_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('smsc_source')); ?>:</b>
	<?php echo CHtml::encode($data->smsc_source); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('smsc_destination')); ?>:</b>
	<?php echo CHtml::encode($data->smsc_destination); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('destination_nomor')); ?>:</b>
	<?php echo CHtml::encode($data->destination_nomor); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('message')); ?>:</b>
	<?php echo CHtml::encode($data->message); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('creation_date')); ?>:</b>
	<?php echo CHtml::encode($data->creation_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('creation_id')); ?>:</b>
	<?php echo CHtml::encode($data->creation_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_date')); ?>:</b>
	<?php echo CHtml::encode($data->updated_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('c_timestamp')); ?>:</b>
	<?php echo CHtml::encode($data->c_timestamp); ?>
	<br />

	*/ ?>

</div>