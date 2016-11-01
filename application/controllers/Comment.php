<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Comment extends CI_Controller
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
        $this->load->model('Comment_model');
        $this->load->helper(array('form', 'url','string'));
        $this->load->library('form_validation');
    }

    public function comments_course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            //Getting comments of a course!
            $course_id = $this->input->get('course_id');
            $comments = $this->Comment_model->get_comments($course_id);
            if(!$comments)
            {
                echo json_encode(array('status' => "error", "error_message" => "No comments found in this course!"));
                return;
            }

            $myArray = array();
            foreach($comments as $comment)
            {
                //Getting user of each comment
                $user = $this->User_model->get_user_id($comment['user_ID']);

                //Storing each comment and user in Array as object!
                $object = new stdClass();
                $object->comment = $comment['comment_text'];
                $object->user = array('user_ID' => $user['user_ID'], 'user_Name' => $user['user_Name'], 'user_Image' => $user['image_Path']  );
                $myArray[] = $object;

            }

            echo json_encode(array('status' => "success", "comments" => $myArray));
            return;
        }
    }

    public function create_comment()
    {
        if($this->input->server('REQUEST_METHOD')=="POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $comment_data = array(
                'user_ID' => $data->user_ID,
                'course_ID' => $data->course_ID,
                'comment_text' => $data->comment
            );

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($comment_data['user_id'])))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in"));
                return;
            }


            if($this->Comment_model->create_comment($comment_data))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error", "error_message" => "Couldn't insert comment"));
            return;

        }
    }

}