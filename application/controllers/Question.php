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
        $this->load->model('Quiz_model');

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }


    //-----------DASHBOARD--------


    //All questions of a particular quiz
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

    //All questions of a particular quiz - pagination purposes
    public function questions_pagination()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $quiz_id = $this->input->get('quiz_id');
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;

            $questions = $this->Question_model->get_questions_limit($quiz_id, $limit, $start);
            if(!$questions)
            {
                echo json_encode(array('status' => "error", "error_message" => "No questions found"));
                return;
            }

            //Sending quiz information along with questions
            $quiz = $this->Quiz_model->get_quiz_withID($quiz_id);
            $questionTotal = $this->Question_model->getQuestionTotal($quiz_id);
            echo json_encode(array('status' => "success", "questions" => $questions, "questionTotal" => $questionTotal, "quiz" => $quiz));
            return;
        }
    }

    //Adding question to database
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

    //Editing Question
    public function editQuestion()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $question_id = $data->question_id;
            $question_data = array(
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

            if ($this->Question_model->updateQuestion($question_id,$question_data)) {
                echo json_encode(array('status' => "success"));
                return;
            } else {
                echo json_encode(array('status' => "error"));
                return;
            }
        }
    }

    //Deleting question
    public function deleteQuestion()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $question_id = $_REQUEST["question_id"];

            if($this->Question_model->deleteQuestion($question_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;
        }
    }
}