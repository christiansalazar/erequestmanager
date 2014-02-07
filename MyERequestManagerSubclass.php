<?php
/**
 * MyERequestManagerSubclass
 *
 *	IMPORTANT:
 *
 *	 this is sample subclass **do not use from this location**, 
 *	 copy it into your 'protected/components/' directory.
 *
 *	USAGE:
 *
 *	 1. in your protected/config/main.php
 *		'components'=>array(
 *			'erequest'=>array(
 *				'class'=>'application.components.MyERequestManagerSubclass'
 * 			),
 *		),
 * 
 *   2. now you can call: 
 *
 *		Yii::app()->erequest->createRequest("website","jdoe@gmail.com");
 *
 *		$erequest = Yii::app()->erequest->getRequest("website","jdoe@gmail.com");
 *
 *		list($key, $cust_email, $att_email,$request_type, $status, 
 *			$dtc, $dtp, $dtf) = $erequest;
 *
 *
 *	3. in any action:
 *
 *		$erequest = Yii::app()->erequest->findRequestByKey($key);
 *		
 *		Yii::app()->erequest->moveMachineStatus($erequest, "accept");
 *		// ERequestEnum: accept, ready, cancel. see also ERequestManagerBase.php
 * 
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
class MyERequestManagerSubclass extends ERequestManagerBase
	implements IApplicationComponent {
	private $_initcalled;

	// IApplicationComponent
	public function init(){
		if(!$this->_initcalled){
			$this->_initcalled = true;
			$this->setPersistenceModel(
				new ERequestManagerOmfPersistence(new OmfDb(),'ERequest'));
		}
	}
	// IApplicationComponent
	public function getIsInitialized(){
		return $this->_initcalled==true;
	}
	/**
	 * onStatusChange 
	 * 
	 * @param array $request ERequest
	 * @param integer $oldstatus 
	 * @param integer $newstatus 
	 * @access protected
	 * @return void
	 */
	protected function onStatusChange($request, $oldstatus, $newstatus){
		// TODO: send emails
	}
	/**
	 * findAttendanceEmail 
	 * 
	 * @param string $request_type 
	 * @access protected
	 * @return string
	 */
	protected function findAttendanceEmail($request_type){
		// TODO: find a tech guy to give attendance to this request type
	}
}
