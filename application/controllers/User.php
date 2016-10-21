<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $userName = "Developer";
        $password = "1234";
        $authentication = $this->input->server('PHP_AUTH_USER');
        if(empty( $authentication))
        {
            echo json_encode(array('status' => "error", "error_message" => "Authentication not found."));
            die();
        }
        if($userName!= $this->input->server('PHP_AUTH_USER') || $password != $this->input->server('PHP_AUTH_PW'))
        {
            echo json_encode(array('status' => "error", "error_message" => "Invalid API keys."));
            die();
        }

        $this->load->model('User_model');
        $this->load->helper(array('form', 'url', 'image','string'));
        $this->load->library('form_validation', 'email');
    }

    public function login()
    {
        if($this->input->server('REQUEST_METHOD')== "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $user_data = array(
                'email' => $data->email,
                'password' => $data->password
            );

            //Validating data
            $this->form_validation->set_data($user_data);
            $this->form_validation->set_rules($this->User_model->getLoginRules());

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Getting User
            $user = $this->User_model->get_user_login($user_data['email'], $user_data['password']);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "Invalid Email/Password "));
                return;
            }

            //Setting SESSION_ID
            $data_session = array(
                'user_ID' => $user['user_ID'],
                'login_time' => date('Y-m-d H:i:s')
            );

            //SESSION SET IN
            if($this->User_model->insert_session($data_session))
            {
                echo json_encode(array('status' => "success", "User" => $user));
                return;
            }
            //ERROR IN SETTING SESSION - USER NOT LOGGED IN
            echo json_encode(array('status' => "error", "error_message" => $this->db->error()));
            return;
        }

    }

    public function logout()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $user_id = $this->input->get('user_id');
            if($this->User_model->delete_session($user_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error", "error_message" => "Couldn't destroy session..!"));
            return;
        }

    }

    public function signup()
    {
        if($this->input->server('REQUEST_METHOD')== "POST") //Validating data from forms
        {
            $register_data = array(
                'first_Name' => $this->input->post('first_Name'),
                'last_Name' => $this->input->post('last_Name'),
                'user_Name' => $this->input->post('user_Name'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'image_Path' => $this->input->post('image_Path')
            );

            $this->form_validation->set_data($register_data); //Setting Data
            $this->form_validation->set_rules($this->User_model->getRegistrationRules()); //Setting Rules

            //Setting Image Rule - Required
            if (empty($_FILES['image_Path']['name']))
            {
                $this->form_validation->set_rules('image_Path', 'Image', 'required');
            }

            //Checking Validations
            if($this->form_validation->run() == FALSE)
            {
                echo json_encode(array('status' => "Error", "error_message" => validation_errors()));
                return;
            }

            //Validating image and uploading it
            $image_attributes = uploadPicture();
            $imageUploadStatus = $image_attributes[0];

            //If imageValidation fails, then exit!
            if($imageUploadStatus == 0)
            {
                $imageErr = $image_attributes[1];
                echo json_encode(array('status' => "Error", "error_message" => $imageErr));
                return;
            }
            //Setting image uploaded path
            else {

                $imagePath = $image_attributes[1];
                $register_data['image_Path'] = $imagePath;
            }

            //Inserting user in table
            if($this->User_model->insert_user($register_data))
            {
                //Sending back inserted user
                $user = $this->User_model->get_user($register_data['email']);
                echo json_encode(array('status' => "success", "User" => $user));
                return;
            }
            else{
                echo json_encode(array('status' => "Unable to register User!"));
                return;
            }

        }
    }

    public function forgotPassword()
    {
        if($this->input->server('REQUEST_METHOD')== "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $user_email = array(
                'email' => $data->email,
            );
            //Validating email
            $this->form_validation->set_data($user_email);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');


            //Exit if incorrect email address!
            if($this->form_validation->run() == FALSE)
            {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Checking whether email exists in db or not
            $user = $this->User_model->get_user($user_email['email']);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "Email does not exist in DB!"));
                return;
            }

            //Sending reset password link to user
            $url = site_url() . '/login/resetPassword';
            $link = '<a href="' . $url . '">' . $url . '</a>';
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'hamna.usmani@gmail.com', // change it to yours
                'smtp_pass' => '', // change it to yours
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE
            );

            //Sending email
            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");
            $this->email->from('hamna.usmani@gmail.com'); // change it to yours
            $this->email->to('hamna.usmani@gmail.com');// change it to yours
            $this->email->subject('Tester');
            $this->email->message($link);
            if($this->email->send())
            {
                echo json_encode(array('status' => "success", "message" => "Email sent successfully!"));
                return;
            }
            else
            {
                show_error($this->email->print_debugger());
            }

        }
    }

    public function resetPassword()
    {
        //Validating email and new password
        if ($this->input->server('REQUEST_METHOD') == "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $user_data = array(
                'email' => $data->email,
                'password' => $data->password
            );

            $this->form_validation->set_data($user_data);
            $this->form_validation->set_rules($this->User_model->getLoginRules());

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Setting Parameters
            $email_user = $user_data['email'];
            $updated_password = array(
                'password' => $user_data['password']
            );

            //Updating password - calling model function
            $user = $this->User_model->update_password($email_user, $updated_password);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "New password cannot be same to old password!"));
                return;
            }
            echo json_encode(array('status' => "success", "User" => $user));
            return;
        }
    }

    public function user_profile()
    {
        if($this->input->server("REQUEST_METHOD") == "GET")
        {
            $user_id = $this->input->get('user_id');//Getting user ID

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($user_id)))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in "));
                return;
            }

            $user = $this->User_model->get_user_id($user_id);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "No user found!"));
                return;
            }
            echo json_encode(array('status' => "success", "user" => $user));
            return;
        }
    }

    public function update_user()
    {
        if($this->input->server("REQUEST_METHOD") == "PUT")
        {
            $data = json_decode(file_get_contents("php://input"));
            $user_id = $data->user_id;

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($user_id)))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in - cannot edit profile"));
                return;
            }

            $update_data = array(
                'first_Name' => $data->first_Name,
                'last_Name' => $data->last_Name,
                'user_Name' => $data->user_Name,
                'password' => $data->password
            );

            //Validating data
            $this->form_validation->set_data($update_data);
            $this->form_validation->set_rules($this->User_model->getUpdateRules());

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Updating user by matching user_id
            $user = $this->User_model->update_user($user_id, $update_data);
            if(!user)
            {
                echo json_encode(array('status' => "error", "error_message" => "Could not update User!"));
                return;
            }
            echo json_encode(array('status' => "success", "User" => $user));
            return;
        }
    }
}