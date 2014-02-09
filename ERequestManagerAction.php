<?php
/**
 * ERequestManagerAction
 *
 * How To Use:

	public function actions(){
		return array(
				'attendance'=>array(
					'api' => Yii::app()->erequest,
					'class'=>'ERequestManagerAction'
				),
			);
	}

  // usage:

  http://yourapp.com/index.php?r=/controller/attendance/&key=123&option=accept

 * 
 * @uses CAction
 * @author Cristian Salazar H. <christiansalazarh@gmail.com> @salazarchris74 
 * @license FreeBSD {@link http://www.freebsd.org/copyright/freebsd-license.html}
 */
class ERequestManagerAction extends CAction {
	public $api; // must be setted
	public function run($key,$option){
		if($this->api != null)
			if($erequest = $this->api->findRequestByKey($key)){
				$this->api->moveMachineStatus($erequest, $option);
				echo "Success!";
			}else{
				echo "Error. Request not found: ".$key;
			}
	}
}
