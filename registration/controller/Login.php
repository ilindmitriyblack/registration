<?php
class Login extends AController {

    public function get_body() {


        parent::get_body();

        $this->db->logout();

        if($this->isPost()) {
            $login = $this->db->clean_data($_POST['login']);
            $password = $this->db->clean_data($_POST['password']);
            $member = $this->db->clean_data($_POST['member']);

            $msg = $this->db->login($login,$password,$member);
            if($msg) {
                header("Location:index.php?option=admin");
                exit();
            }

            header("Location:index.php?option=login");
            exit();

        }

        return $this->render('login');

    }
}
?>