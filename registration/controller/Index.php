<?php
class Index extends AController {

	public function get_body() {
		parent::get_body();

		return $this->render('index',array('user'=>$this->user));
	}
}
?>