<?php
class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

//---------------------SELECT---------------------------

    //Getting user via email
    public function get_user($email)
    {
        $query = $this->db
            ->select('user_id, first_name, last_name, user_name, email, user_image')
            ->where('email',  $email )
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
        }
    }

    //Getting user via email
    public function get_user_email($email)
    {
        $query = $this->db
            ->where('email',  $email )
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
        }
    }
    //Getting user via hash
    public function get_user_hash($reset_hash)
    {
        $query = $this->db
            ->where('reset_hash', $reset_hash)
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
        }
    }

    //Getting user via ID - for searching profile
    public function get_user_id($id)
    {
        $query = $this->db
            ->select('user_id,user_name, first_name, last_name, email, user_image')
            ->where('user_id',  $id)
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
        }
    }

    //Getting user via ID - for checking password
    public function get_user_password($id)
    {
        $query = $this->db
            ->where('user_id',  $id)
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
        }
    }

    //Getting USER for login
    public function get_login_user($data)
    {
        $query = $this->db
            ->select('user_id, first_name, last_name, user_name, email, user_image, isReset')
            ->where('email',  $data['email'] )
            ->where('password', $data['password'])
            ->get('user');

        if ( $query->num_rows() > 0 )
        {
            $row = $query->row_array();
            return $row;
        }
        else
        {
            return false;
        }
    }

    //Checking if Hash is valid for password reset
    public function isValidHash($hash)
    {
        $query = $this->db
            ->where('reset_hash',  $hash)
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            return true;
        }
        else{
            return false;
        }
    }

    //Get Tokens of user registered in a course for lecture Notification
    public function get_tokens($course_id)
    {
        $this->db
            ->select('user.user_id,user.user_token')
            ->from('user')
            ->join('user_course', 'user.user_id = user_course.user_id')
            ->where('course_id', $course_id);

        $query = $this->db->get();
        return $query->result_array();
    }


    //--------------------------ADMIN------------------------
    //Getting USER/ADMIN for login
    public function get_login_admin($data)
    {
        $query = $this->db
           ->where('email',  $data['email'] )
           ->where('password', $data['password'])
            ->get('user_dashboard');


        if ( $query->num_rows() > 0 )
        {
            $row = $query->row_array();
            return $row;
        }
        else
        {
            return false;
        }
    }


//---------------------INSERT---------------------------

    //Sign up process - inserting new user!
    public function insert_user($data)
    {
        if ($this->db->insert("user", $data))
        {
            return true;
        }
    }

    public function insert_reset($user_id,$data)
    {
        $this->db->trans_start();
        $this->db->where('user_id', $user_id);
        $this->db->update('user', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

//---------------------UPDATE----------------------------
    //Updating a user profile via ID
    public function update_user($id, $data)
    {
        //Updating user by matching ID
        $this->db
            ->where('user_id', $id)
            ->update('user', $data);

        if ($this->db->affected_rows()) {
            return $this->get_user_id($id);
        }
        else
        {
            return null;
        }

    }

    //Updating password by matching email
    public function update_password($column,$value, $data)
    {
        //Updating password by matching hash
        $this->db->trans_start();
        $this->db->where($column, $value);
        $this->db->update('user', $data);
        $this->db->trans_complete();


        //If new password != old password, this will work!
        //$this->db->affected_rows() == '1'
        if ($this->db->trans_status() === TRUE)
        {
            return true;
        }
        else
        {
            return false;
        }

        /*if ($this->db->trans_status() === FALSE)
        {
            // generate an error... or use the log_message() function to log your error
        }*/

    }

    //Updating token of a user
    public function update_token($user_id,$user_token)
    {
        //Updating token
        $this->db->where('user_id', $user_id);
        $this->db->update('user', array('user_token'=> $user_token));


        if ($this->db->affected_rows())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

//---------------------Session Table----------------------

    //Logging out user!
    public function delete_session($user_id)
    {
        //setting session to empty on logout!
        $this->db
            ->where('user_id', $user_id)
            ->delete('session');

        if ($this->db->affected_rows()) {
            return true;
        }
        else
        {
            return false;
        }
    }

    //Setting session table
    public function insert_session($data)
    {
        if($this->db->insert('session', $data))
        {
            return true;
        }

        // $this->db->set('login_time', 'NOW()', FALSE);
        // set should come before insert to work properly -
    }

    //Checking whether user is logged-in
    public function get_user_session($user_id)
    {
        $query = $this->db
            ->where('user_id', $user_id)
            ->get('session');

        if ( $query->num_rows() > 0 ) {
            return true;
        }
        else{
            return false;
        }
    }

    //Update login time
    public function update_loginTime($user_id)
    {
        $this->db
            ->where('user_id', $user_id)
            ->update('session', array('login_time' => date('Y-m-d H:i:s')));

    }

    //---------------------Validation Rules----------------------

    //Registration Validation rules!
    public function getRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'first_name',
                'label' => 'First Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'last_name',
                'label' => 'Last Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'user_name',
                'label' => 'user Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|is_unique[user.email]|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[20]',
            )
        );

        return $config;
    }

    //Login Validation Rules for USER/ADMIN
    public function getLoginRules()
    {
        $config = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Update Validation rules!
    public function getUpdateRules()
    {
        $config = array(
            array(
                'field' => 'first_name',
                'label' => 'First Name',
                'rules' => 'regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'last_name',
                'label' => 'Last Name',
                'rules' => 'regex_match[/^[A-Za-z]+$/]'
            )
        );

        return $config;
    }

}