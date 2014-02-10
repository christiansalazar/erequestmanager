<?php
/**
 * ERequestManagerTester
 *
 * You must run it from your CApplicationConsole:
 * 
 *   public function actionTestme(){
 *      $test = new ERequestManagerTester();
 *      $test->run();
 *   }
 * 
 * Now run test by running this command in your shell:
 * 
 *   $>  ./yiic yourapp testme
 * 
 * It will perform core testings.
 *    
 * 
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
class ERequestManagerTester extends ERequestManagerBase {
	/**
	 * run 
	 * 	run tests. invoked by CApplicationConsole
	 * @access public
	 * @return void
	 */
	public function run(){
		$this->setPersistenceModel(
			new ERequestManagerOmfPersistence(new OmfDb(),'_ERequest'));
		$this->testPersistence($this->getPersistenceModel());	
		$this->testHighLevelApiCreateAndGet();
	}
	private function testPersistence($api){
		printf("%s...\n",__METHOD__);

		printf("newRequest...");
		$erequest = $api->newRequest("test","123@mail.com","456@mail.com");
		assert('is_array($erequest) && !empty($erequest)');
		list($key, $cust_email, $att_email,$request_type, 
			$status, $dtc, $dtp, $dtf) = $erequest;

		printf("loadRequest by its key...");
		$loaded = $api->loadRequest($key,'key');
		list($key2, $cust_email2, $att_email2,$request_type2, 
    		$status2, $dtc2, $dtp2, $dtf2) = $loaded;
		assert('($key == $key2 &&
		$cust_email == $cust_email2 &&
		$att_email == $att_email2 &&
		$request_type == $request_type2 &&
		$status == $status2 &&
		$dtc == $dtc2 &&
		$dtp == $dtp2 &&
		$dtf == $dtf2)');
		printf("OK\n");
	
		printf("loadRequest by its complex id (%s,%s)...",$request_type,$cust_email);
		$loaded_ = $api->loadRequest(array($request_type,$cust_email),'complex');
		assert('$loaded_ != null');
		list($key3, $cust_email3, $att_email3,$request_type3, 
    		$status3, $dtc3, $dtp3, $dtf3) = $loaded_;
		assert('($key == $key3 &&
		$cust_email == $cust_email3 &&
		$att_email == $att_email3 &&
		$request_type == $request_type3 &&
		$status == $status3 &&
		$dtc == $dtc3 &&
		$dtp == $dtp3 &&
		$dtf == $dtf3)');
		printf("OK\n");

		printf("saveRequest...");
		$loaded[2] = 'testvalue';
		$api->saveRequest($loaded);
		$loaded2 = $api->loadRequest($key,'key');
		if($loaded2[2] != $loaded[2])
			throw new Exception('error');
		printf("OK\n");

		printf("deleteRequest...");
		$api->deleteAllRequests();
		$n=$api->countRequests("test","123@mail.com");
		assert('$n == 0');
		$api->newRequest("test","123@mail.com","456@mail.com");
		$api->newRequest("test","123@mail.com","456@mail.com");
		$api->newRequest("test","123@mail.com","456@mail.com");
		$n=$api->countRequests("test","123@mail.com");
		assert('$n == 3');
		$n1=$api->deleteRequest("test","123@mail.com");
		$n2=$api->countRequests("test","123@mail.com");
		assert('($n1 == 3) && ($n2 == 0)');
		list($key1) = $api->newRequest("test","123@mail.com","456@mail.com");
		list($key2) = $api->newRequest("test","123@mail.com","456@mail.com");
		$n=$api->countRequests("test","123@mail.com");
		assert('$n == 2');
		$api->deleteRequestByKey($key1);
		$n=$api->countRequests("test","123@mail.com");
		assert('$n == 1');
		list($key_x) = $api->loadRequest(array("test","123@mail.com"),"complex");
		assert('$key_x == $key2');
		$api->deleteRequestByKey($key2);
		$n=$api->countRequests("test","123@mail.com");
		assert('$n == 0');
		printf("OK\n");
	}
	private function testHighLevelApiCreateAndGet(){
		printf("%s...\n",__METHOD__);	

		printf("test createRequest and getRequest...");
		$erequest = $this->createRequest("test","123@gmail.com");
		assert('$erequest != null');
		list($key, $cust_email, $att_email,$request_type, 
			$status, $dtc, $dtp, $dtf) = $erequest;
		$loaded = $this->getRequest("non-existing","123@gmail.com");
		assert('$loaded == null');
		$loaded = $this->getRequest("test","123@gmail.com");
		list($key2, $cust_email2, $att_email2,$request_type2, 
    		$status2, $dtc2, $dtp2, $dtf2) = $loaded;
		assert('($key == $key2 &&
		$cust_email == $cust_email2 &&
		$att_email == $att_email2 &&
		$request_type == $request_type2 &&
		$status == $status2 &&
		$dtc == $dtc2 &&
		$dtp == $dtp2 &&
		$dtf == $dtf2)');
		printf("OK\n");

		printf("test status getter and setter...");
		// status test
		$_status = $this->getStatus($erequest);
		assert('!empty($_status)');
		$test='status1';
		$this->setStatus($erequest,$test);
		$status = $this->getStatus($erequest);
		assert('$status == $test');
		$this->setStatus($erequest,$_status);
		$status = $_status;
		printf("OK\n");

		// key finder
		printf("test findRequestByKey...");
		$loaded3 = $this->findRequestByKey($key);
		assert('!empty($loaded3)');
		list($key3, $cust_email3, $att_email3,$request_type3, 
    		$status3, $dtc3, $dtp3, $dtf3) = $loaded3;
		assert('$key == $key3');
		assert('$cust_email == $cust_email3');
		assert('$att_email == $att_email3');
		assert('$request_type == $request_type3');
		assert('$status == $status3');
		assert('$dtc == $dtc3');
		assert('$dtp == $dtp3');
		assert('$dtf == $dtf3');
		printf("OK\n");

		// machine status
		printf("test moveMachineStatus bad option...");
		$status = $this->moveMachineStatus($erequest,"bad option");
		assert('$status == null');
		printf("OK\n");

		printf("test moveMachineStatus happy-day...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == parent::IN_PROGRESS');
		$status = $this->moveMachineStatus($erequest,"ready");
		assert('$status == parent::FINISHED');
		printf("OK\n");

		printf("test moveMachineStatus bad-day-1...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$status = $this->moveMachineStatus($erequest,"abort");
		assert('$status == parent::CANCELLED');
		printf("OK\n");

		printf("test moveMachineStatus bad-day-2...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == parent::IN_PROGRESS');
		$status = $this->moveMachineStatus($erequest,"abort");
		assert('$status == parent::CANCELLED');
		printf("OK\n");

		printf("test moveMachineStatus wrong-option-1...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$initial_status = $this->getStatus($erequest);
		$status = $this->moveMachineStatus($erequest,"ready");
		assert('$status == null');

		printf("test moveMachineStatus wrong-option-2...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == parent::IN_PROGRESS');
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == null');
		printf("OK\n");

		printf("test moveMachineStatus wrong-option-3...");
		$this->setStatus($erequest,parent::SETUP_REQUIRED);
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == parent::IN_PROGRESS');
		$status = $this->moveMachineStatus($erequest,"abort");
		assert('$status == parent::CANCELLED');
		$status = $this->moveMachineStatus($erequest,"accept");
		assert('$status == null');
		printf("OK\n");

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
		return "test@gmail.com";	
	}
}
