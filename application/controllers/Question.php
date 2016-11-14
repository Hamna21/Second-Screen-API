<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Controller
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

        $this->load->model('Question_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }

    //All questions of a quiz
    public function questions()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $quiz_id = $this->input->get('quiz_id');
            $questions = $this->Question_model->get_questions($quiz_id);
            if(!$questions)
            {
                echo json_encode(array('status' => "error", "error_message" => "No questions found"));
                return;
            }
            echo json_encode(array('status' => "success", "questions" => $questions));
            return;
        }
    }

    public function addQuestion()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $question_data = array(
                'quiz_id' => $data->quiz_id,
                'question_text' => $data->question_text,
                'option_one' => $data->option_one,
                'option_two' => $data->option_two,
                'option_three' => $data->option_three,
                'option_four' => $data->option_four,
                'correct_option' => $data->correct_option
            );

            $this->form_validation->set_data($question_data); //Setting Data
            $this->form_validation->set_rules($this->Question_model->getQuestionRegistrationRules()); //Setting Rules

            //Reloading page if validation fails
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => "error"));
                return;
            }

            if ($this->Question_model->insertQuestion($question_data)) {
                echo json_encode(array('status' => "success"));
                return;
            } else {
                echo json_encode(array('status' => "error"));
                return;
            }
        }
    }
}