<?php
interface IERequestPersistence {
	/**
	 * newRequest 
	 * 
	 * @param string $request_type 
	 * @param string $customer_email 
	 * @param string $attendance_email 
	 * @abstract
	 * @access protected
	 * @return array ERequest
	 */
	function newRequest($request_type, $customer_email,$attendance_email);
	/**
	 * loadRequest 
	 * 
	 *	when modality is: 'key'
	 *		args: $key (the ERequest 'key' attribute)
	 *	when modality is: 'complex'
	 *		args: array($request_type, $customer_email)
	 *
	 * @param mixed $args 
	 * @param string $modality see note
	 * @abstract
	 * @access protected
	 * @return array ERequest
	 */
	function loadRequest($args, $modality);
	/**
	 * saveRequest 
	 * 
	 * @param array $request an ERequest formatted array to be saved.
	 * @abstract
	 * @access protected
	 * @return void
	 */
	function saveRequest($request);
}
