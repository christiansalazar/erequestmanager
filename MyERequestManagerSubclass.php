<?php
/**
 * MyERequestManagerSubclass
 *
 *	sample subclass, do not use from this location, copy it into 
 *	your 'protected/components/' directory.
 * 
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
public class MyERequestManagerSubclass extends ERequestManagerBase
	implements IApplicationComponent {

	// IApplicationComponent
	public function init(){
	}
	// IApplicationComponent
	public function getIsInitialized(){
		return true;
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
	
	}
	/**
	 * findAttendanceEmail 
	 * 
	 * @param string $request_type 
	 * @access protected
	 * @return string
	 */
	protected function findAttendanceEmail($request_type){
		
	}
// low level protected api
	/**
	 * newRequest 
	 * 
	 * @param string $request_type 
	 * @param string $customer_email 
	 * @abstract
	 * @access protected
	 * @return array ERequest
	 */
	protected function newRequest($request_type, $customer_email){
	
	}
	/**
	 * loadRequest 
	 * 
	 *	when modality is: 'id'
	 *		args: $request_id
	 *	when modality is: 'complex'
	 *		args: array($request_type, $customer_email)
	 *
	 * @param mixed $args 
	 * @param string $modality see note
	 * @abstract
	 * @access protected
	 * @return array ERequest
	 */
	protected function loadRequest($args, $modality='id'){
		
	}
	/**
	 * saveRequest 
	 * 
	 * @param array $request an ERequest formatted array to be saved.
	 * @abstract
	 * @access protected
	 * @return void
	 */
	protected function saveRequest($request){
	
	}
}
