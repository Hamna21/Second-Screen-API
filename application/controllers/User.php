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
        $this->load->model('Course_model');
        $this->load->model('Category_model');
        $this->load->model('Teacher_model');
        $this->load->model('Lecture_model');
        $this->load->helper(array('form', 'url', 'image','string'));
        $this->load->library('form_validation', 'email');
    }

    //---------USER---------
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
            $user = $this->User_model->get_login_user($user_data);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "Invalid Email/Password "));
                return;
            }

            //Requested a password change - Cannot Log in
            if($user['isReset'] == 1)
            {
                echo json_encode(array('status' => "error", "error_message" => "requested a password change"));
                return;
            }


            //If user already logged in - then return user
            if($this->User_model->get_user_session($user['user_id']))
            {
                echo json_encode(array('status' => "success", "user" => $user));
                return;
            }

            //Setting SESSION_ID
            $data_session = array(
                'user_id' => $user['user_id'],
                'login_time' => date('Y-m-d H:i:s')
            );


            //SESSION SET IN
            if($this->User_model->insert_session($data_session))
            {
                echo json_encode(array('status' => "success", "user" => $user));
                return;
            }

            //ERROR IN SETTING SESSION - USER NOT LOGGED IN
            echo json_encode(array('status' => "error", "error_message" => "couldn't log in user"));
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
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'user_name' => $this->input->post('user_name'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password')
            );

            $this->form_validation->set_data($register_data); //Setting Data
            $this->form_validation->set_rules($this->User_model->getRegistrationRules()); //Setting Rules

            //Setting Image Rule - Required
            if (empty($_FILES['image_path']['name']))
            {
                $this->form_validation->set_rules('image_path', 'Image', 'required');
            }

            //Checking Validations
            if($this->form_validation->run() == FALSE)
            {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Validating image and uploading it
            $image_attributes = uploadPicture();
            $imageUploadStatus = $image_attributes[0];

            //If imageValidation fails, then exit!
            if($imageUploadStatus == 0)
            {
                $imageErr = $image_attributes[1];
                echo json_encode(array('status' => "error", "error_message" => $imageErr));
                return;
            }
            //Setting image uploaded path
            else {

                $imagePath = $image_attributes[1];
                $register_data['user_image'] = $imagePath;
            }

            //Inserting user in table
            if($this->User_model->insert_user($register_data))
            {
                //Sending back inserted user
                $user = $this->User_model->get_user($register_data['email']);

                //Setting SESSION_ID
                $data_session = array(
                    'user_id' => $user['user_id'],
                    'login_time' => date('Y-m-d h:i:sa')
                );

                //SESSION SET IN
                if($this->User_model->insert_session($data_session))
                {
                    echo json_encode(array('status' => "success", "user" => $user));
                    return;
                }
                //ERROR IN SETTING SESSION - USER NOT LOGGED IN
                echo json_encode(array('status' => "error", "error_message" => "Error in login."));
                return;

            }
            else{
                echo json_encode(array('status' => "error", 'error_message' => 'unable to register user'));
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
                'email' => $data->email
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
                echo json_encode(array('status' => "error", "error_message" => "email does not exist in db"));
                return;
            }

            //Generating reset_hash based on USER_ID
            $reset_hash = base64_encode($user['user_id']);
            $reset_data = array(
                'reset_hash' => $reset_hash,
                'isReset' => 1
            );

            //Inserting hash in DB and sending Email
            if($this->User_model->insert_reset($user['user_id'],$reset_data))
            {
                //Sending reset password link to user
                $url = 'http://localhost:8080/Dashboard-SS-v2/second-screen/resetPassword?reset_hash='. $reset_hash;
                $link = '<a href="' . $url . '">' . $url . '</a>';

                $config = Array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.googlemail.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'hamna.usmani@gmail.com', // change it to yours
                    'smtp_pass' => 'hamnacute21', // change it to yours
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
                    echo json_encode(array('status' => "success", "message" => "email sent successfully"));
                    return;
                }
                else
                {
                    echo json_encode(array('status' => "error", "message" => "error in sending email"));
                    return;
                }
            }

            echo json_encode(array('status' => "error", "message" => "error in sending email"));
            return;
        }
    }

    public function resetPassword()
    {
        //Validating new password
        if ($this->input->server('REQUEST_METHOD') == "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $reset_data = array(
                'password' => $data->password,
                'reset_hash' => $data->reset_hash
            );


            $this->form_validation->set_data($reset_data);
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[20]');

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Setting Parameters
            $reset_hash = $data->reset_hash;
            $new_password = $data->password;

            $user = $this->User_model->get_user_hash($reset_hash);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "No user found."));
                return;
            }

            if($new_password == $user['password'])
            {
                echo json_encode(array('status' => "error", "error_message" => "New password cannot be same to old password"));
                return;
            }

            $updated_password = array(
                'password' => $new_password,
                'isReset' => 0,
                'reset_hash' => " "
            );

            //Updating password - calling model function
            if($this->User_model->update_password('reset_hash',$reset_hash, $updated_password))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error", "error_message" => "Error in updating password"));
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
        if($this->input->server("REQUEST_METHOD") == "POST")
        {
            $update_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name')
            );

            $user_id = $this->input->post('user_id');

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($user_id)))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in - cannot edit profile"));
                return;
            }

            //Validating data
            $this->form_validation->set_data($update_data);
            $this->form_validation->set_rules($this->User_model->getUpdateRules());

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //If user uploaded new Image
            if(!empty($_FILES['image_path']['name'])) {
                //Validating image and uploading it
                $image_attributes = uploadPicture();
                $imageUploadStatus = $image_attributes[0];

                //If imageValidation fails, then reload add course page
                if ($imageUploadStatus == 0) {
                    echo json_encode(array('status' => "error", "error_message" => $image_attributes[1]));
                    return;
                }

                $update_data['course_image'] = $image_attributes[1];
            }

            //Updating user by matching user_id
            $user = $this->User_model->update_user($user_id, $update_data);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "Could not update User!"));
                return;
            }
            echo json_encode(array('status' => "success", "user" => $user));
            return;
        }
    }

    public function update_password()
    {
        if($this->input->server('REQUEST_METHOD') == "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $update_data = array(
                'password' => $data->password,
                'user_id' => $data->user_id
            );

            $this->form_validation->set_data($update_data);
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[20]');

            if($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error", "error_message" => validation_errors()));
                return;
            }

            //Getting user via - information will include password too
            $user = $this->User_model->get_user_password($update_data['user_id']);
            if(!$user)
            {
                echo json_encode(array('status' => "error", "error_message" => "No user found."));
                return;
            }

            if($update_data['password'] == $user['password'])
            {
                echo json_encode(array('status' => "error", "error_message" => "New password cannot be same to old password"));
                return;
            }


            $update_password = array(
                'password' => $update_data['password']
            );
            //Updating password - calling model function
            if($this->User_model->update_password('user_id',$update_data['user_id'], $update_password))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error", "error_message" => "Error in updating password"));
            return;

        }
    }

    //---------ADMIN---------------//
    public function loginAdmin()
    {
        if($this->input->server('REQUEST_METHOD')== "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $admin_data = array(
                'email' => $data->email,
                'password' => $data->password
            );

            //Validating data
            $this->form_validation->set_data($admin_data);
            $this->form_validation->set_rules($this->User_model->getLoginRules());
            if($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'email_Error' => form_error('email'),
                    'password_Error' => form_error('password')
                );
                echo json_encode(array('status' => "error in validation", "error_messages" => $error_data));
                return;
            }

            //Getting Admin
            $admin= $this->User_model->get_login_admin($admin_data);
            if(!$admin)
            {
                echo json_encode(array('status' => "error in db"));
                return;
            }
            else
            {
                echo json_encode(array('status' => "success", "admin" => $admin));
                return;
            }
        }
    }

    public function totalCount()
    {
        if($this->input->server("REQUEST_METHOD") == "GET")
        {
            $courseTotal = $this->Course_model->getCourseTotal();
            $categoryTotal = $this->Category_model->getCategoryTotal();
            $lectureTotal = $this->Lecture_model->getLectureTotal();
            $teacherTotal = $this->Teacher_model->getTeacherTotal();

            echo json_encode(array('status' => "success", "courseTotal" => $courseTotal, "categoryTotal" => $categoryTotal, "lectureTotal" => $lectureTotal, "teacherTotal" => $teacherTotal));
            return;
        }

        echo json_encode(array('status' => "error", "error_message" => "Invalid Request"));
        return;
    }

    public function isValidHash()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $reset_hash =  $this->input->get('reset_hash');
            if($this->User_model->isValidHash($reset_hash))
            {
                echo json_encode(array('status' => "success"));
                return;
            }

            echo json_encode(array('status' => "error"));
            return;

        }
    }
}