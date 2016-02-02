<?php
/**
 * AdminController
 * @var $this AdminController
 * @var $model Visits
 * @var $form CActiveForm
 * version: 0.0.1
 * Reference start
 *
 * TOC :
 *	Index
 *	Manage
 *	Import
 *	Add
 *	Edit
 *	View
 *	RunAction
 *	Delete
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 26 January 2016, 13:00 WIB
 * @link https://github.com/oMMuCo
 * @contect (+62)856-299-4114
 *
 *----------------------------------------------------------------------------------------------------------
 */

class AdminController extends Controller
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
			if(in_array(Yii::app()->user->level, array(1,2))) {
				$arrThemes = Utility::getCurrentTemplate('admin');
				Yii::app()->theme = $arrThemes['folder'];
				$this->layout = $arrThemes['layout'];
			} else {
				$this->redirect(Yii::app()->createUrl('site/login'));
			}
		} else {
			$this->redirect(Yii::app()->createUrl('site/login'));
		}
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
				'actions'=>array('index'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level)',
				//'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level != 1)',
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('manage','import','add','edit','view','runaction','delete'),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level) && in_array(Yii::app()->user->level, array(1,2))',
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
		$this->redirect(array('manage'));
	}	

	/**
	 * Manages all models.
	 */
	public function actionManage() 
	{
		$model=new Visits('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Visits'])) {
			$model->attributes=$_GET['Visits'];
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

		$this->pageTitle = 'Visits Manage';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_manage',array(
			'model'=>$model,
			'columns' => $columns,
		));
	}	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionImport() 
	{
		ini_set('max_execution_time', 0);
		ob_start();
		
		$path = 'public/visit/';
		$error = [];
		
		if(isset($_FILES['visitExcel'])) {
			$fileName = CUploadedFile::getInstanceByName('visitExcel');
			if(in_array(strtolower($fileName->extensionName), array('xls','xlsx'))) {
				$file = time().'_'.Utility::getUrlTitle(date('d-m-Y H:i:s')).'_'.Utility::getUrlTitle(Yii::app()->user->displayname).'.'.strtolower($fileName->extensionName);
				if($fileName->saveAs($path.'/'.$file)) {
					Yii::import('ext.excel_reader.OExcelReader');
					$xls = new OExcelReader($path.'/'.$file);
					
					for ($row = 3; $row <= $xls->sheets[0]['numRows']; $row++) {
						$no						= trim($xls->sheets[0]['cells'][$row][1]);
						$author_name			= ucwords(strtolower(trim($xls->sheets[0]['cells'][$row][2])));
						$author_email			= strtolower(trim($xls->sheets[0]['cells'][$row][3]));
						$author_phone			= strtolower(trim($xls->sheets[0]['cells'][$row][4]));
						$organization_name 		= ucfirst(strtolower(trim($xls->sheets[0]['cells'][$row][5])));
						$organization_address	= ucwords(strtolower(trim($xls->sheets[0]['cells'][$row][6])));
						$organization_phone		= strtolower(trim($xls->sheets[0]['cells'][$row][7]));
						$visitor				= trim($xls->sheets[0]['cells'][$row][8]);
						$start_date				= date('Y-m-d', strtotime(trim($xls->sheets[0]['cells'][$row][9])));
						$finish_date			= date('Y-m-d', strtotime(trim($xls->sheets[0]['cells'][$row][10])));
						$status					= trim($xls->sheets[0]['cells'][$row][11]);
						$message				= ucfirst(strtolower(trim($xls->sheets[0]['cells'][$row][12])));
						$message_reply			= ucfirst(strtolower(trim($xls->sheets[0]['cells'][$row][13])));
						
						$author=new OmmuAuthors;
						$visit=new Visits;
						$guest=new VisitGuest;
						
						if($author_email != '' || $organization_name != '') {			
							if($author_email != '') {
								$authorModel = OmmuAuthors::model()->find(array(
									'select' => 'author_id, email',
									'condition' => 'publish = 1 AND email = :email',
									'params' => array(
										':email' => $author_email,
									),
								));
								if($authorModel != null) {
									$guest->author_id = $authorModel->author_id;
								} else {
									$author->name = $author_name;
									$author->email = $author_email;
									if($author_phone != '')
										$author->author_phone = $author_phone;
									if($author->save())
										$guest->author_id = $author->author_id;
								}
							} else
								$guest->author_id = 0;
							
							if($organization_name != '') {
								$guest->organization = 1;
								$guest->organization_name = $organization_name;
								$guest->organization_address = $organization_address != '' ? $organization_address : '-';
								$guest->organization_phone = $organization_phone !='' ? $organization_phone : '-';							
							} else
								$guest->organization = 0;
							
							$guest->status = $status;
							$guest->start_date = $start_date;
							$guest->finish_date = $finish_date;
							$guest->visitor = $visitor;
							$guest->messages = in_array($status, array('1','2')) && $message != '' ? $message : '-';
							$guest->message_reply = in_array($status, array('1','2')) && $message_reply != '' ? $message_reply : '-';	
							
							if($guest->save())
								$visit->guest_id = $guest->guest_id;
							
							if($guest->status == '1') {
								$visit->status = $guest->status;
								$visit->start_date = $guest->start_date;
								$visit->finish_date = $guest->finish_date;
								$visit->save();								
							}							
						}			
					}
					
					Yii::app()->user->setFlash('success', 'Import Excell Success.');
					$this->redirect(array('manage'));
					
				} else {
					Yii::app()->user->setFlash('error', 'Gagal menyimpan file.');
				}
			} else {
				Yii::app()->user->setFlash('error', 'Hanya file .xls dan .xlsx yang dibolehkan.');
			}
		}

		ob_end_flush();
		
		$this->dialogDetail = true;
		$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
		$this->dialogWidth = 600;

		$this->pageTitle = 'Upload Visit';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_import',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAdd() 
	{
		$model=new Visits;
		$author=new OmmuAuthors;
		$guest=new VisitGuest;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Visits'], $_POST['OmmuAuthors'], $_POST['VisitGuest'])) {
			$model->attributes=$_POST['Visits'];
			$author->attributes=$_POST['OmmuAuthors'];
			$author->scenario='phone';
			$guest->attributes=$_POST['VisitGuest'];
			
			$authorModel = OmmuAuthors::model()->find(array(
				'select' => 'author_id, email',
				'condition' => 'publish = 1 AND email = :email',
				'params' => array(
					':email' => strtolower($author->email),
				),
			));
			if($authorModel != null) {
				$guest->author_id = $authorModel->author_id;
			} else {
				if($author->save())
					$guest->author_id = $author->author_id;
			}
			
			$guest->start_date = $model->start_date;
			$guest->finish_date = $model->finish_date;
			if(in_array($guest->status, array(1,2)) && $guest->messages == "")
				$guest->messages = "-";
			if(in_array($guest->status, array(1,2)) && $guest->message_reply == "")
				$guest->message_reply = "-";
			if($guest->save())
				$model->guest_id = $guest->guest_id;
			
			if($model->validate()) {
				if($guest->status == 1) {
					$model->status = $guest->status;
					if($model->save()) {
						Yii::app()->user->setFlash('success', 'Visits success created.');
						$this->redirect(array('manage'));
					}					
				} else if(in_array($guest->status, array(1,2))) {
					Yii::app()->user->setFlash('success', 'Visits success created.');
					$this->redirect(array('manage'));
				}
			}
		}

		$this->pageTitle = 'Create Visits';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_add',array(
			'model'=>$model,
			'author'=>$author,
			'guest'=>$guest,
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

		if(isset($_POST['Visits'])) {
			$model->attributes=$_POST['Visits'];
			
			if($model->save()) {
				Yii::app()->user->setFlash('success', 'Visits success updated.');
				$this->redirect(array('manage'));
			}
		}

		$this->pageTitle = 'Update Visits';
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
	public function actionView($id) 
	{
		$model=$this->loadModel($id);

		$this->pageTitle = 'View Visits';
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_view',array(
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
				Visits::model()->updateAll(array(
					'publish' => 1,
				),$criteria);
			} elseif($actions == 'unpublish') {
				Visits::model()->updateAll(array(
					'publish' => 0,
				),$criteria);
			} elseif($actions == 'trash') {
				Visits::model()->updateAll(array(
					'publish' => 2,
				),$criteria);
			} elseif($actions == 'delete') {
				Visits::model()->deleteAll($criteria);
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
						'id' => 'partial-visits',
						'msg' => '<div class="errorSummary success"><strong>Visits success deleted.</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = 'Visits Delete.';
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_delete');
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = Visits::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='visits-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
