<?php
/**
 * OutboxController
 * @var $this OutboxController
 * @var $model SmsOutbox
 * @var $form CActiveForm
 * version: 0.0.1
 * Reference start
 *
 * TOC :
 *	Index
 *	View
 *	Manage
 *	Add
 *	Edit
 *	RunAction
 *	Delete
 *	Publish
 *	Headline
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 12 February 2016, 04:07 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 *----------------------------------------------------------------------------------------------------------
 */

class OutboxController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	public $defaultAction = 'index';

	/**
	 * Initialize admin page theme
	 */
	public function init() 
	{
		if(!Yii::app()->user->isGuest) {
			if(Yii::app()->user->level == 1) {
				$arrThemes = Utility::getCurrentTemplate('admin');
				Yii::app()->theme = $arrThemes['folder'];
				$this->layout = $arrThemes['layout'];
			} else {
				$this->redirect(Yii::app()->createUrl('site/login'));
			}
		} else {
			$this->redirect(Yii::app()->createUrl('site/login'));
		}
		/*
		$arrThemes = Utility::getCurrentTemplate('public');
		Yii::app()->theme = $arrThemes['folder'];
		$this->layout = $arrThemes['layout'];
		*/
	}

	/**
	 * @return array action filters
	 */
	public function filters() 
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() 
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level)',
				//'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level != 1)',
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('manage','add','edit','runaction','delete','publish','headline'),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level == 1)',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() 
	{
		$arrThemes = Utility::getCurrentTemplate('public');
		Yii::app()->theme = $arrThemes['folder'];
		$this->layout = $arrThemes['layout'];
		Utility::applyCurrentTheme($this->module);
		
		$setting = SmsOutbox::model()->findByPk(1,array(
			'select' => 'meta_description, meta_keyword',
		));

		$criteria=new CDbCriteria;
		$criteria->condition = 'publish = :publish';
		$criteria->params = array(':publish'=>1);
		$criteria->order = 'creation_date DESC';

		$dataProvider = new CActiveDataProvider('SmsOutbox', array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>10,
			),
		));

		$this->pageTitle = 'Sms Outboxes';
		$this->pageDescription = $setting->meta_description;
		$this->pageMeta = $setting->meta_keyword;
		$this->render('front_index',array(
			'dataProvider'=>$dataProvider,
		));
		//$this->redirect(array('manage'));
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) 
	{
		$arrThemes = Utility::getCurrentTemplate('public');
		Yii::app()->theme = $arrThemes['folder'];
		$this->layout = $arrThemes['layout'];
		Utility::applyCurrentTheme($this->module);
		
		$setting = VideoSetting::model()->findByPk(1,array(
			'select' => 'meta_keyword',
		));

		$model=$this->loadModel($id);

		$this->pageTitle = 'View Sms Outboxes';
		$this->pageDescription = '';
		$this->pageMeta = $setting->meta_keyword;
		$this->render('front_view',array(
			'model'=>$model,
		));
		/*
		$this->render('admin_view',array(
			'model'=>$model,
		));
		*/
	}	

	/**
	 * Manages all models.
	 */
	public function actionManage() 
	{
		$model=new SmsOutbox('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SmsOutbox'])) {
			$model->attributes=$_GET['SmsOutbox'];
		}

		$columnTemp = array();
		if(isset($_GET['GridColumn'])) {
			foreach($_GET['GridColumn'] as $key => $val) {
				if($_GET['GridColumn'][$key] == 1) {
					$columnTemp[] = $key;
				}
			}
		}
		$columns = $model->getGridColumn($columnTemp);

		$this->pageTitle = 'Sms Outboxes Manage';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_manage',array(
			'model'=>$model,
			'columns' => $columns,
		));
	}	
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAdd() 
	{
		$model=new SmsOutbox;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['SmsOutbox'])) {
			$model->attributes=$_POST['SmsOutbox'];

			/* 
			$jsonError = CActiveForm::validate($model);
			if(strlen($jsonError) > 2) {
				//echo $jsonError;
				$errors = $model->getErrors();
				$summary['msg'] = "<div class='errorSummary'><strong>Please fix the following input errors:</strong>";
				$summary['msg'] .= "<ul>";
				foreach($errors as $key => $value) {
					$summary['msg'] .= "<li>{$value[0]}</li>";
				}
				$summary['msg'] .= "</ul></div>";

				$message = json_decode($jsonError, true);
				$merge = array_merge_recursive($summary, $message);
				$encode = json_encode($merge);
				echo $encode;

			} else {
				if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
					if($model->save()) {
						echo CJSON::encode(array(
							'type' => 5,
							'get' => Yii::app()->controller->createUrl('manage'),
							'id' => 'partial-sms-outbox',
							'msg' => '<div class="errorSummary success"><strong>SmsOutbox success created.</strong></div>',
						));
					} else {
						print_r($model->getErrors());
					}
				}
			}
			Yii::app()->end();
			*/

			if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
				if($model->save()) {
					Yii::app()->user->setFlash('success', 'SmsOutbox success created.');
					//$this->redirect(array('view','id'=>$model->outbox_id));
					$this->redirect(array('manage'));
				}
			}
		}

		$this->pageTitle = 'Create Sms Outboxes';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_add',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEdit($id) 
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['SmsOutbox'])) {
			$model->attributes=$_POST['SmsOutbox'];

			/* 
			$jsonError = CActiveForm::validate($model);
			if(strlen($jsonError) > 2) {
				//echo $jsonError;
				$errors = $model->getErrors();
				$summary['msg'] = "<div class='errorSummary'><strong>Please fix the following input errors:</strong>";
				$summary['msg'] .= "<ul>";
				foreach($errors as $key => $value) {
					$summary['msg'] .= "<li>{$value[0]}</li>";
				}
				$summary['msg'] .= "</ul></div>";

				$message = json_decode($jsonError, true);
				$merge = array_merge_recursive($summary, $message);
				$encode = json_encode($merge);
				echo $encode;

			} else {
				if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
					if($model->save()) {
						echo CJSON::encode(array(
							'type' => 5,
							'get' => Yii::app()->controller->createUrl('manage'),
							'id' => 'partial-sms-outbox',
							'msg' => '<div class="errorSummary success"><strong>SmsOutbox success updated.</strong></div>',
						));
					} else {
						print_r($model->getErrors());
					}
				}
			}
			Yii::app()->end();
			*/

			if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
				if($model->save()) {
					Yii::app()->user->setFlash('success', 'SmsOutbox success updated.');
					//$this->redirect(array('view','id'=>$model->outbox_id));
					$this->redirect(array('manage'));
				}
			}
		}

		$this->pageTitle = 'Update Sms Outboxes';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_edit',array(
			'model'=>$model,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionRunAction() {
		$id       = $_POST['trash_id'];
		$criteria = null;
		$actions  = $_GET['action'];

		if(count($id) > 0) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id', $id);

			if($actions == 'publish') {
				SmsOutbox::model()->updateAll(array(
					'publish' => 1,
				),$criteria);
			} elseif($actions == 'unpublish') {
				SmsOutbox::model()->updateAll(array(
					'publish' => 0,
				),$criteria);
			} elseif($actions == 'trash') {
				SmsOutbox::model()->updateAll(array(
					'publish' => 2,
				),$criteria);
			} elseif($actions == 'delete') {
				SmsOutbox::model()->deleteAll($criteria);
			}
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('manage'));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id) 
	{
		$model=$this->loadModel($id);
		
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				if($model->delete()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-sms-outbox',
						'msg' => '<div class="errorSummary success"><strong>SmsOutbox success deleted.</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = 'SmsOutbox Delete.';
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_delete');
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionPublish($id) 
	{
		$model=$this->loadModel($id);
		
		if($model->publish == 1) {
		//if($model->actived == 1) {
		//if($model->enabled == 1) {
		//if($model->status == 1) {
			$title = Phrase::trans(276,0);
			//$title = Phrase::trans(278,0);
			//$title = Phrase::trans(284,0);
			//$title = Phrase::trans(292,0);
			$replace = 0;
		} else {
			$title = Phrase::trans(275,0);
			//$title = Phrase::trans(277,0);
			//$title = Phrase::trans(283,0);
			//$title = Phrase::trans(291,0);
			$replace = 1;
		}

		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				//change value active or publish
				$model->publish = $replace;
				//$model->actived = $replace;
				//$model->enabled = $replace;
				//$model->status = $replace;

				if($model->update()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-sms-outbox',
						'msg' => '<div class="errorSummary success"><strong>SmsOutbox success published.</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = $title;
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_publish',array(
				'title'=>$title,
				'model'=>$model,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionHeadline($id) 
	{
		$model=$this->loadModel($id);

		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				//change value active or publish
				$model->headline = 1;
				$model->publish = 1;

				if($model->update()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-sms-outbox',
						'msg' => '<div class="errorSummary success"><strong>SmsOutbox success updated.</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = Phrase::trans(338,0);
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_headline');
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = SmsOutbox::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Phrase::trans(193,0));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) 
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sms-outbox-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
