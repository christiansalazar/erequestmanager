<?php
/**
 * ERequestManagerOmfPersistence
 *
 *	this is a utility class used to provide help when persistence runs over OMF
 *
 * 
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
class ERequestManagerOmfPersistence implements IERequestPersistence {
	private $_sto;
	private $_classname;

	public function __construct($api,$classname='ERequest'){
		$this->_sto = $api;
		$this->_classname = $classname;
	}
	private function sto(){
		return $this->_sto;
	}
	/**
	 * newRequest 
	 * 
	 * @param string $request_type 
	 * @param string $customer_email 
	 * @param string $attendance_email 
	 * @abstract
	 * @return array ERequest
	 */
	public function newRequest($request_type, $customer_email,$attendance_email){
		list($id) = $this->sto()->create($this->_classname);
		$erequest = array(
			hash('crc32',$id), 
			$customer_email, 
			$attendance_email,
			$request_type, 
			"",
			time(),
			0, 
			0
		);
		list($key, $cust_email, $att_email,$req_type, 
			$status, $dtc, $dtp, $dtf) = $erequest;
 		$attributes = array('key'=>$key, 'customer_email'=>$cust_email,
			'attendance_email'=>$att_email, 'request_type'=>$request_type,
			'status'=>$status,'dtc'=>$dtc, 'dtp'=>0 ,'dtf'=>0);
 		$this->sto()->set($id, $attributes);
		return $erequest;
	}
	/**
	 * loadRequest 
	 * 
	 *	when modality is: 'key'
	 *		args: $key 
	 *	when modality is: 'complex'
	 *		args: array($request_type, $customer_email)
	 *
	 * @param mixed $args 
	 * @param string $modality see note
	 * @abstract
	 * @return array ERequest
	 */
	public function loadRequest($args, $modality){
		if($modality == 'key'){
			$obj = $this->sto()->getObject($this->_classname,
				array('key'=>$args));
		}else{
			$request_type = $args[0];
			$customer_email = $args[1];
			$objects = $this->sto()->fetch($this->_classname,
				array('customer_email'=>$customer_email),
				array('key','customer_email','attendance_email',
					'request_type','status','dtc','dtp','dtf'),
				-1,0,false
			);
			$selected=array();$last_id=null;
			foreach($objects as $obj_id=>$attributes)
				if($attributes['request_type'] == $request_type){
					$selected[$obj_id] = $attributes;
					$last_id = $obj_id;
				}
			if(!$last_id) return null;
			$obj = $selected[$last_id];
		}
		return array(
			$obj['key'],
			$obj['customer_email'],
			$obj['attendance_email'],
			$obj['request_type'],
			$obj['status'],
			$obj['dtc'],
			$obj['dtp'],
			$obj['dtf'],
		);
	}
	/**
	 * saveRequest 
	 * 
	 * @param array $request an ERequest formatted array to be saved.
	 * @abstract
	 * @access protected
	 * @return void
	 */
	public function saveRequest($erequest){
		list($key, $cust_email, $att_email,$req_type, 
			$status, $dtc, $dtp, $dtf) = $erequest;
		$attributes = array('key'=>$key, 'customer_email'=>$cust_email,
		'attendance_email'=>$att_email, 'request_type'=>$req_type,
		'status'=>$status,'dtc'=>$dtc, 'dtp'=>$dtp ,'dtf'=>$dtf);
		foreach($this->sto()->listObjectsBy($this->_classname,'key',$key,-1,0,0) 
			as $obj){
				list($id) = $this->sto()->readObject($obj);
				$this->sto()->set($id, $attributes);
		}
	}
}
