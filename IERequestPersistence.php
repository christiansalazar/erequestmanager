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
	/**
	 * deleteRequest
	 *	delete all request having request_type for a given customer_email
	 * 
	 * @param mixed $request_type 
	 * @param mixed $customer_email 
	 * @access public
	 * @return integer
	 */
	function deleteRequest($request_type, $customer_email);
	function deleteAllRequests();
	function deleteRequestByKey($key);

	/**
	 * countRequest 
	 * 	count how many request has been made for a given customer and request type.
	 *	used primary in testings.
	 *
	 * @param mixed $request_type 
	 * @param mixed $customer_email 
	 * @access public
	 * @return integer
	 */
	function countRequests($request_type, $customer_email);
}
