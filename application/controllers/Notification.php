<?php

class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
	    $this->load->model('User_model');
    }


    //------------------NOTIFICATIONS-----------------

    //Lecture Notification - 10 minutes prior
    public function sendLectureRequest()
    {
        $lecture_name = $this->input->get('lecture_name');
        $lecture_time = $this->input->get('lecture_time');
        $course_id= $this->input->get('course_id');

        $date = new DateTime($lecture_time);
        $lecture_time = $date->format('h:i:s a');

        $users= $this->User_model->get_tokens($course_id);
        if(!$users)
        {
            echo json_encode(array('status' => "error", 'users' => "No users found registered for this lecture."));
            return;
        }

        foreach ($users as $user)
        {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

            $fields = array (
                'notification' => array (
                    "title"=> "Lecture Notification",
                    "body" => $lecture_name . " will start at ". $lecture_time,
                    "icon" => "myicon"
                ),
	
                'to' => $user['user_token']
            );

            $response = (Requests::post($url, $headers, json_encode($fields)));

        }
     }

    //Quiz Notification
    public function sendQuizRequest()
    {
        $quiz_id = $this->input->get('quiz_id');
        $quiz_title = $this->input->get('quiz_title');

        //Getting users registered in lecture - course
        $lecture_id = $this->input->get('lecture_id');
        $result = $this->User_model->get_courseID($lecture_id);
        $users =  $this->User_model->get_tokens($result['course_id']);

         foreach ($users as $user)
         {
            $url = 'https://fcm.googleapis.com/fcm/send';
             $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

             $fields = array (
                'notification' => array (
                    "title"=> "Quiz Notification",
                    "body" => $quiz_title . "is starting now.",
                    "icon" => "myicon"
                ),
                'data' => array(
                    "quiz_id"=>  $quiz_id
                ),

                 'to' =>  $user['user_token']

                );
                $response = (Requests::post($url, $headers, json_encode($fields)));
         }
    }

	//current Lecture Notification - on exact time of lecture
    public function send_currentLectureRequest()
    {
        $lecture_name = $this->input->get('lecture_name');
        $lecture_time = $this->input->get('lecture_time');
        $course_id= $this->input->get('course_id');
        $lecture_id= $this->input->get('lecture_id');

        $users= $this->User_model->get_tokens($course_id);
        if(!$users)
        {
            echo json_encode(array('status' => "error", 'users' => "No users found registered for this lecture."));
            return;
        }

        foreach ($users as $user)
        {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

            $fields = array (
                'notification' => array (
                    "title"=> "Lecture Notification",
                    "body" => $lecture_name . " is starting now.",
                    "icon" => "myicon"
                ),
                'data' => array(
                    "lecture_id"=>  $lecture_id
                ),
                'to' => $user['user_token']
            );

            $response = (Requests::post($url, $headers, json_encode($fields)));

        }

    }


    //--------------------------TESTER-------------------------------------

    //SAMPLE Notification Sample for Quiz
    public function testerNotification()
    {
        $quiz_id = $this->input->get('quiz_id');
        $quiz_title = $this->input->get('quiz_title');
	$token = $this->input->get('token');

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

        $fields = array (
            'notification' => array (
                "title"=> "Quiz Tester Notification",
                "body" => $quiz_title . " had ID". $quiz_id,
                "icon" => "myicon"
            ),
            'data' => array(
                "quiz_id"=>  $quiz_id
            ),
            'to' => $token
        );


        $response = (Requests::post($url, $headers, json_encode($fields)));
     }

     //Sample notification for Lecture
    public function testerNotificationLecture()
    {
        $lecture_name = $this->input->get('lecture_name');
        $lecture_time = $this->input->get('lecture_time');
        $course_id= $this->input->get('course_id');
        $lecture_id= $this->input->get('lecture_id');
	$token = $this->input->get('token');

            $url = 'https://fcm.googleapis.com/fcm/send';
            $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

            $fields = array (
                'notification' => array (
                    "title"=> "Lecture Notification",
                    "body" => $lecture_name . " is starting now",
                    "icon" => "myicon"
                ),
                'data' => array(
                    "lecture_id"=>  $lecture_id
                ),
                'to' => $token
            );

            $response = (Requests::post($url, $headers, json_encode($fields)));
            var_dump($response);

    }

	
   
}