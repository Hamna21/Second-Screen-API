<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lecture extends CI_Controller 
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
        
        $this->load->model('Category_model');
        $this->load->model('Lecture_model');
        $this->load->model('Quiz_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    //-------------------DASHBOARD-----------------------//
    //List of lectures specified within limit and total count of lectures
    public function lectures_dashboard()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $lectures = $this->Lecture_model->get_lectures_limit($limit, $start);
            if(!$lectures)
            {
                echo json_encode(array('status' => "error", "error_message" => "No lecture found!"));
                return;
            }
            $lectureTotal = $this->Lecture_model->getLectureTotal();
            if(!$lectureTotal)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array("status" => "success","lectures" => $lectures, "lectureTotal" => $lectureTotal));
            return;
        }
    }

    //Getting all lectures of a course
    public function lectures()
    {
        $course_id = $this->input->get('course_id');
        $lectures = $this->Lecture_model->get_lectures($course_id);
        if(!$lectures)
        {
            echo json_encode(array('status' => "error", "error_message" => "No lecture found!"));
            return;
        }

        echo json_encode(array("status" => "success","lectures" => $lectures));
        return;
    }

    //Getting a single lecture by ID
    public function lecture()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $lecture_id = $_REQUEST["lecture_id"];
            $lecture = $this->Lecture_model->get_lecture($lecture_id);
            if(!$lecture)
            {
                echo json_encode(array('status' => "error", "error_message" => "No lecture found!"));
                return;
            }

            $quizzes = $this->Quiz_model->get_quizzes($lecture_id);
            $lecture['quizzes'] = $quizzes;
            echo json_encode(array('status' => "success", "lecture" => $lecture));
            return;
        }
    }

    public function addLecture()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $lecture_data = array(
                'lecture_name' => $data->lecture_Name,
                'lecture_description' => $data->lecture_Description,
                'lecture_start' => $data->lecture_start,
                'lecture_end' => $data->lecture_end,
                'course_id' => $data->course_ID
            );

            $this->form_validation->set_data($lecture_data); //Setting Data
            $this->form_validation->set_rules($this->Lecture_model->getLectureRegistrationRules()); //Setting Rules

            //Reloading add lecture page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'lectureName_Error' => form_error('lecture_Name'),
                    'lectureDescription_Error' => form_error('lecture_Description'),
                    'lectureStart_Error' => form_error('lecture_start'),
                    'lectureEnd_Error' => form_error('lecture_end'),
                    'courseID_Error' => form_error('course_ID')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            if ($this->Lecture_model->insertLecture($lecture_data)) {
                echo json_encode(array('status' => "success"));
                return;
            }
            else
            {
                echo json_encode(array('status' => "error in db"));
                return;
            }
        }


    }

    public function editLecture()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $lecture_id = $data->lecture_ID;
            $lecture_data = array(
                'lecture_name' => $data->lecture_Name,
                'lecture_description' => $data->lecture_Description,
                'lecture_start' => $data->lecture_start,
                'lecture_end' => $data->lecture_end,
                'course_id' => $data->course_ID
            );

            $this->form_validation->set_data($lecture_data); //Setting Data
            $this->form_validation->set_rules($this->Lecture_model->getLectureEditRules()); //Setting Rules

            //Reloading add lecture page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'lectureName_Error' => form_error('lecture_Name'),
                    'lectureDescription_Error' => form_error('lecture_Description'),
                    'lectureStart_Error' => form_error('lecture_start'),
                    'lectureEnd_Error' => form_error('lecture_end'),
                    'courseID_Error' => form_error('course_ID')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            if ($this->Lecture_model->updateLecture($lecture_id,$lecture_data)) {
                echo json_encode(array('status' => "success"));
                return;
            }
            else
            {
                echo json_encode(array('status' => "error in db"));
                return;
            }
        }



    }

    public function deleteLecture()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $lecture_id = $_REQUEST["lecture_id"];
            if($this->Lecture_model->deleteLecture($lecture_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;

        }
    }

}