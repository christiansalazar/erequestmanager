<?php
/**
 * ERequestManagerBase 
 *
 *	ERequest <<class>>
 *	--------------------
 *	key
 *  customer_email
 *  attendance_email
 *  request_type
 *  status
 *  datetime_created
 *  datetime_progress
 *  datetime_finished
 *
 *	defined in an array form:

 *	list($key, $cust_email, $att_email,$request_type, $status, $dtc, $dtp, $dtf) = $erequest;
 *		
 *
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
abstract class ERequestManagerBase {
	const SETUP_REQUIRED = 'setup-required';
	const IN_PROGRESS = 'in-progress';
	const CANCELLED = 'cancelled';
	const FINISHED = 'finished';

	private $persistence_model;

	public function getEOptionEnum(){
		return array("accept","ready","abort");
	}

	/**
	 * setPersistenceModel
	 *	tell this class which persistence model will be used.
	 *	must implements: IERequestPersistence.
	 *	
	 * @param instance $model instance implementing IERequestPersistence
	 * @return void
	 */
	public function setPersistenceModel($model){
		$this->persistence_model = $model;
	}
	public function getPersistenceModel(){
		return $this->persistence_model;
	}
// high level public API
	/**
	 * createRequest 
	 * 
	 * @param string $request_type 
	 * @param string $customer_email 
	 * @access public
	 * @return array ERequest or null
	 */
	public function createRequest($request_type, $customer_email){
		$attendance_email = $this->findAttendanceEmail($request_type);
		if(null == $attendance_email){ 
			Yii::log(__METHOD__." findAttendanceEmail returns no email","error");
			throw new Exception("findAttendanceEmail must return an email when request is: ".$request_type);
			return null;
		}
		$erequest = $this->getPersistenceModel()->newRequest($request_type,
			$customer_email, $attendance_email);
		$this->setStatus($erequest, self::SETUP_REQUIRED);
		$this->onStatusChange($erequest, "", $this->getStatus($erequest));
		return $erequest;
	}
	/**
	 * deleteRequest
	 *	remove the existing request.
	 * 
	 * @param mixed $request_type 
	 * @param mixed $customer_email 
	 * @access public
	 * @return void
	 */
	public function deleteRequest($request_type, $customer_email){
		return $this->getPersistenceModel()
			->deleteRequest($request_type, $customer_email);
	}
	/**
	 * getRequest 
	 * 
	 * @param string $request_type 
	 * @param string $customer_email 
	 * @access public
	 * @return array ERequest
	 */
	public function getRequest($request_type, $customer_email){
		return $this->getPersistenceModel()->loadRequest(
			array($request_type,$customer_email),'complex');
	}
	/**
	 * findRequestByKey 
	 * 	finds a ERequest using its key number. high level api.
	 * @param string $key 
	 * @access public
	 * @return array ERequest
	 */
	public function findRequestByKey($key){
		return $this->getPersistenceModel()->loadRequest($key,'key');
	}
	/**
	 * moveMachineStatus 
	 *	implements a finite-status-machine for handling the status.
	 * 
	 *	A) setup-required---accept--->in_progress--ready--->finished
	 *	B) setup-required---accept--->in_progress--abort--->cancelled
	 *	C) setup-required---abort--->cancelled
	 *
	 * @param array ERequest
	 * @param string $option "accept","cancel","ready"
	 * @access public
	 * @return string the new status when change, else null
	 */
	public function moveMachineStatus(&$erequest, $option){
		if(!in_array($option,$this->getEOptionEnum()))
			return null;
		$oldstatus = $this->getStatus($erequest);
		//printf("[%s-->%s]",$oldstatus,$option);
		$newstatus = null;
		$case="";
		if($oldstatus == self::SETUP_REQUIRED){
			$case="1";
			switch($option){
				case "accept": 
					$newstatus = self::IN_PROGRESS;
					break;
				case "abort":
					$newstatus = self::CANCELLED;
					break;
				default:
					return null;
			}
		}elseif($oldstatus == self::IN_PROGRESS){
			$case="2";
			switch($option){
				case "ready": 
					$newstatus = self::FINISHED;
					break;
				case "abort":
					$newstatus = self::CANCELLED;
					break;
				default:
					return null;
			}
		}elseif($oldstatus == self::FINISHED){
			$case="3";
			return null;
		}elseif($oldstatus == self::CANCELLED){
			$case="4";
			return null;
		}	
		if($oldstatus != $newstatus){
			//printf("[A, CASE:%s,op:%s,nst:%s]",$case,$option,$newstatus);
			$this->setStatus($erequest, $newstatus,true);
			$this->onStatusChange($erequest, $oldstatus,$newstatus);
			return $newstatus;
		}else{
			//printf("[B, CASE:%s,op:%s,nst:%s]",$case,$option,$newstatus);
			return null;
		}
	}
	protected function setStatus(&$erequest, $status,$boolstampdate=false){
		$erequest[4] = $status;
		if($boolstampdate===true){
			if($status == self::IN_PROGRESS) $erequest[6] = time();
			if($status == self::FINISHED) $erequest[7] = time();
			if($status == self::CANCELLED) $erequest[7] = time();
		}
		$this->getPersistenceModel()->saveRequest($erequest);
	}
	protected function getStatus($erequest){
		list($key, $cust_email, $att_email,$request_type, 
			$status, $dtc, $dtp, $dtf) = $erequest;
		return $status;
	}
// high level protected api
	abstract protected function onStatusChange($request, $oldstatus, $newstatus);
	abstract protected function findAttendanceEmail($request_type);
}
