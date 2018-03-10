<?php
class Admin extends AController {
	
	public function get_body()
    {

        $this->auth = TRUE;

        parent::get_body();


        return $this->render('admin', array('user' => $this->user));
    }
}
?>