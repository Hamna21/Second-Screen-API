<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $userName = "Developer";
        $password = "1234";
        $authentication = $this->input->server('PHP_AUTH_USER');
        if (empty($authentication)) {
            echo json_encode(array('status' => "error", "error_message" => "Authentication not found."));
            die();
        }
        if ($userName != $this->input->server('PHP_AUTH_USER') || $password != $this->input->server('PHP_AUTH_PW')) {
            echo json_encode(array('status' => "error", "error_message" => "Invalid API keys."));
            die();
        }

        $this->load->model('Quiz_model');
        $this->load->model('Lecture_model');
        $this->load->model('Question_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    //All quizzes of a particular lecture + lecture name
    public function quizzes()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $lecture_id = $this->input->get('lecture_id');
            $quizzes = $this->Quiz_model->get_quizzes($lecture_id);
            if(!$quizzes)
            {
                echo json_encode(array('status' => "error", "error_message" => "No quiz found"));
                return;
            }

            //Getting lecture name of quizzes
            $lecture = $this->Lecture_model->get_lecture($lecture_id);

            echo json_encode(array('status' => "success", 'lecture' => $lecture ,"quizzes" => $quizzes));
            return;
        }
    }


    //All quizzes of a particular lecture + lecture name - for pagination purposes
    public function quizzes_pagination()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $lecture_id = $this->input->get('lecture_id');
            $quizzes = $this->Quiz_model->get_quizzes_limit($lecture_id, $limit, $start);

            if(!$quizzes)
            {
                echo json_encode(array('status' => "error", "error_message" => "No quiz found"));
                return;
            }

            //Getting lecture name of quizzes
            $lecture = $this->Lecture_model->get_lecture($lecture_id);
            $quizTotal = $this->Quiz_model->get_quizTotal($lecture_id);

            echo json_encode(array('status' => "success", 'lecture' => $lecture ,"quizzes" => $quizzes, 'quizTotal' => $quizTotal));
            return;
        }
    }
    
    //Adding new Quiz to Database
    public function addQuiz()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $quiz_data = array(
                'lecture_id' => $data->lecture_id,
                'quiz_title' => $data->quiz_title,
                'quiz_time' => $data->quiz_time,
                'quiz_duration' => $data->quiz_duration
            );

            $this->form_validation->set_data($quiz_data); //Setting Data
            $this->form_validation->set_rules($this->Quiz_model->getQuizRegistrationRules()); //Setting Rules

            //Reloading add quiz page if validation fails
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error"));
                return;
            }

            if ($this->Quiz_model->insertQuiz($quiz_data)) {
                echo json_encode(array('status' => "success"));
                return;
            } else {
                echo json_encode(array('status' => "error"));
                return;
            }

        }
    }

    //Editing Quiz
    public function editQuiz()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $quiz_id = $data->quiz_id;
            $quiz_data = array(
                'quiz_title' => $data->quiz_title,
                'quiz_time' => $data->quiz_time,
                'quiz_duration' => $data->quiz_duration
            );

            $this->form_validation->set_data($quiz_data); //Setting Data
            $this->form_validation->set_rules($this->Quiz_model->getQuizRegistrationRules()); //Setting Rules

            //Reloading add quiz page if validation fails
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error"));
                return;
            }

            if ($this->Quiz_model->updateQuiz($quiz_id,$quiz_data)) {
                echo json_encode(array('status' => "success"));
                return;
            } else {
                echo json_encode(array('status' => "error"));
                return;
            }
        }
    }
    
    //Deleting quiz
    public function deleteQuiz()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $quiz_id = $_REQUEST["quiz_id"];

            if($this->Quiz_model->deleteQuiz($quiz_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;
        }
    }
}
