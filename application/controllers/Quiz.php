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
        $this->load->model('Course_model');

        $this->load->helper(array('form', 'url', 'crontab'));
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

    //A quiz with all questions - NOTIFICATION PURPOSES
    public function quiz_questions_old()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $quiz_id = $this->input->get('quiz_id');
            $quiz = $this->Quiz_model->get_quiz($quiz_id);

            $questions = $this->Question_model->get_questions_quiz($quiz_id);
            if(!$questions)
            {
                echo json_encode(array('status' => "error", "error_message" => "No questions found"));
                return;
            }

            $quiz['questions'] = $questions;
            echo json_encode(array('status' => "success", "quiz" => $quiz));
            return;
        }
    }


    //A quiz with all questions - NOTIFICATION PURPOSES
    //If user already attempted the quiz, then return the result
    public function quiz_questions()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $quiz_id = $this->input->get('quiz_id');
            $user_id = $this->input->get('user_id');

            //Checking whether quiz attempted or not
            //If attempted then returning result
            $quiz_result = $this->Quiz_model->get_quiz_result($quiz_id, $user_id);
            //Getting quiz information alongside quiz_result to include quiz title and duration in result

            $quiz = $this->Quiz_model->get_quiz($quiz_id);
            //If quiz_result entry not found, means user is not listed for the quiz
            //only return quiz - not questions
            if(!$quiz_result)
            {
                echo json_encode(array('status' => "success", "message" => "User not listed in course",  "quiz" => $quiz));
                return;
            }

            //If quiz attempted by user, then return result of quiz
            if($quiz_result['status'] == "true")
            {
                $quiz_result['quiz_title'] = $quiz['quiz_title'];
                $quiz_result['quiz_duration'] = $quiz['quiz_duration'];
                echo json_encode(array('status' => "success", "quiz" => $quiz_result));
                return;
            }


            //If quiz not attempted by user, then return all questions
            $questions = $this->Question_model->get_questions_quiz($quiz_id);
            if(!$questions)
            {
                echo json_encode(array('status' => "error", "error_message" => "No questions found"));
                return;
            }

            $quiz['questions'] = $questions;
            echo json_encode(array('status' => "success", "quiz" => $quiz));
            return;
        }
    }


    //Response of quiz after user submits
    public function quiz_response()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $quiz_id = $data->quiz_id;
            $user_id = $data->user_id;
            $answers = $data->answers;
            $total = sizeof($answers);
            $result =0;

           foreach ($answers as $answer)
           {
               //If user skipped the question, then do not check correct response
               if($answer->answer != "undefined")
               {
                   $correct_response = $this->Question_model->correct_response($answer->question_id, $quiz_id);
                   if(!$correct_response )
                   {
                       echo json_encode(array('status' => "error", "error_message" => "Invalid question ID."));
                       return;
                   }

                   if($correct_response  == $answer->answer)
                   {
                       $result = $result +1;
                   }
               }

           }

           //After quiz submission, changing quiz status to attempted and storing total and result
            $quiz_data = array(
                "total_questions" => $total,
                "correct" => $result,
                "status" => "true"
            );
            $this->Quiz_model->updateQuizResult($quiz_id, $user_id, $quiz_data);


            echo json_encode(array('status' => "success", "total" => $total, "correct" => $result));
            return;
        }
    }





    //-------------------DASHBOARD-----------

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


            //Subtracting 12 hours from time - TO adjust it according to server
            $updated_date = strtotime($quiz_data['quiz_time']);
            $updated_date = $updated_date - (12 * 60 * 60); //Subtracting 12 hours
            $notification_date  = date("Y-m-d H:i:s", $updated_date);

            //Extracting date,month,time, hour of quiz time
            $notification_date = new DateTime($notification_date);
            $month = $notification_date->format('m');
            $day = $notification_date->format('d');
            $hour = $notification_date->format('H');
            $minute = $notification_date->format('i');


            $quiz_id = $this->Quiz_model->insertQuiz($quiz_data);
            if(!$quiz_id)
            {
                echo json_encode(array('status' => "error"));
                return;
            }

            quizNotification($minute, $hour, $day, $month, $quiz_id,$quiz_data['quiz_title'],$quiz_data['lecture_id']);
            //Setting quiz for all the users in course
            $this->user_quiz($quiz_data['lecture_id'], $quiz_id);

            echo json_encode(array('status' => "success"));
            return;


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

    //As soon as quiz is created, populate quiz_result with all users of that quiz (Course) -
    // with attempted status to false
    public function user_quiz($lecture_id, $quiz_id)
    {
        //Getting all users registered in a course
        $course_id = $this->Lecture_model->get_course_id($lecture_id);
        $users = $this->Course_model->get_course_users($course_id);

        $quiz_data = array(
            "quiz_id" => $quiz_id,
            "status" => "false"
        );

        foreach ($users as $user)
        {
            $quiz_data["user_id"] = $user['user_id'];
            $this->Quiz_model->insert_quizResult($quiz_data);
        }

        return;
    }

}
