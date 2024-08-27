<?php

function get_user_id($dbh, $email){
    $stmt = $dbh->prepare('SELECT id FROM User WHERE email = ?');
    $stmt->execute(array($email));
    $user = $stmt->fetch();

    return ($user) ? $user['id'] : null;
}

class User{
    private $dbh;
    private $user_id;
    private $user_data;

    public function __construct($dbh, $user_id) {
        $this->dbh = $dbh;
        $this->user_id = $user_id;
        $this->fetchUserData();
    }

    private function fetchUserData() {
        $stmt = $this->dbh->prepare('SELECT * FROM User WHERE id = ?');
        $stmt->execute(array($this->user_id));
        $this->user_data = $stmt->fetch();
    }

    function is_user_valid() {
        return !empty($this->user_data);
    }

    function is_user_admin(){
        return ($this->user_data['isAdmin'] == 1);
    }

    function getID() {
        return $this->user_data['id'];
    }

    function getFirstName() {
        return $this->user_data['firstName'];
    }

    function getLastName() {
        return $this->user_data['lastName'];
    }

    function getPhone() {
        return $this->user_data['phoneNumber'];
    }

    function getPic() {
        return $this->user_data['profilePic'];
    }

    function getPersonalInfo() {
        return $this->user_data['personalInfo'];
    }

    function getEmail() {
        return $this->user_data['email'];
    }
}
?>