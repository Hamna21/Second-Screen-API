<?php
class user_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

//---------------------SELECT---------------------------

    //Getting user for login
    public function get_user_login($email, $password)
    {
        $query = $this->db
            ->select('user_ID, user_Name, image_Path')
            ->where('email',  $email )
            ->where('password', $password)
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
    public function get_user($email)
    {
        $query = $this->db
            ->select('user_ID,user_Name, image_Path')
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

    //Getting user via ID - for searching profile
    public function get_user_id($id)
    {
        $query = $this->db
            ->select('user_ID,user_Name, image_Path')
            ->where('user_ID',  $id)
            ->get('user');

        if ( $query->num_rows() > 0 ) {
            $row = $query->row_array();
            return $row;
        }
        else{
            return null;
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


//---------------------UPDATE----------------------------
    //Updating a user profile via ID
    public function update_user($id, $data)
    {
        //Updating user by matching ID
        $this->db
            ->where('id', $id)
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
    public function update_password($email_user, $data)
    {
        //Updating password by matching email
        $this->db->trans_start();
        $this->db->where('email', $email_user);
        $this->db->update('user', $data);
        $this->db->trans_complete();


        //If new password != old password, this will work!
        //$this->db->affected_rows() == '1'
        if ($this->db->trans_status() === TRUE)
        {
            return $this->get_user($email_user);
        }
        else
        {
            return null;
        }

        /*if ($this->db->trans_status() === FALSE)
        {
            // generate an error... or use the log_message() function to log your error
        }*/

    }

//---------------------session Table----------------------

    //Logging out user!
    public function delete_session($user_id)
    {
        //setting session to empty on logout!
        $this->db
            ->where('user_ID', $user_id)
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
        if(!($this->db->insert('session', $data)))
        {
            return false;
        }
        // $this->db->set('login_time', 'NOW()', FALSE);
        // set should come before insert to work properly -
        return true;
    }

    //Checking whether user is logged-in
    public function get_user_session($user_id)
    {
        $query = $this->db
            ->where('user_ID', $user_id)
            ->get('session');

        if ( $query->num_rows() > 0 ) {
            return true;
        }
        else{
            return false;
        }
    }

    //---------------------Validation Rules----------------------

    //Registration Validation rules!
    public function getRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'first_Name',
                'label' => 'First Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'last_Name',
                'label' => 'Last Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'user_Name',
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
                'rules' => 'required',
            )
        );

        return $config;
    }

    //Login Validation Rules!
    public function getLoginRules()
    {
        $config = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|valid_email'
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
                'field' => 'first_Name',
                'label' => 'First Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'last_Name',
                'label' => 'Last Name',
                'rules' => 'required|regex_match[/^[A-Za-z]+$/]'
            ),

            array(
                'field' => 'user_Name',
                'label' => 'user Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required',
            )
        );

        return $config;
    }



}