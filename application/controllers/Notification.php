<?php

class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
	    $this->load->model('User_model');
	    $this->load->model('Lecture_model');
	    $this->load->model('Quiz_model');
	    $this->load->model('Notification_model');
    }


    //------------------NOTIFICATIONS-----------------

    //Getting all notifications of a user
    public function notifications()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $user_id = $this->input->get('user_id');

            //Getting all notifications of a user
            $notifications = $this->Notification_model->get_notifications($user_id);

            if(!$notifications)
            {
                echo json_encode(array('status' => "error", "error_message" => "No notifications found for this user!"));
                return;
            }

            //If notification is of a quiz, then getting it's status
            foreach ($notifications as $key => $notification)
            {
                if($notification['type'] == "quiz")
                {
                    $quiz_id = $notification['id'];
                    $quiz_result = $this->Quiz_model->get_quiz_result_duration($quiz_id, $user_id);
                    $notifications[$key]['flag']= $quiz_result['status'];
                    $notifications[$key]['quiz_duration']= $quiz_result['quiz_duration'];
                }

                //When API hit 1st time, status returned will be false (not read) but changed to true (read) on back end
                //On next API hit, true status will be returned
                //Checking status of notification - if it is not-read then change to read, otherwise don't change
                if($notification['notification_status'] == "false")
                {
                    $notification_status = array('notification_status'=> 'true');
                    $this->Notification_model->updateStatus($notification['notification_id'],$notification_status);
                }

            }

            echo json_encode(array('status' => "success", "notifications" => $notifications));
            return;
        }
    }

    //Getting total count of un-read (false)notifications
    public function count_notifications()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $user_id = $this->input->get('user_id');
            $notificationTotal = $this->Notification_model->countNotifications($user_id);
            if(!$notificationTotal)
            {
                echo json_encode(array('status' => "success", "message" => "No un-read notifications."));
                return;
            }
            echo json_encode(array('status' => "success", "notification_count" => "$notificationTotal"));
            return;
        }
    }


    //Lecture Notification - 10 minutes prior
    public function sendLectureRequest()
    {
        $lecture_id = $this->input->get('lecture_id');
        $lecture_name = $this->input->get('lecture_name');
        $lecture_time = $this->input->get('lecture_time');
        $course_id= $this->input->get('course_id');

        //Converting 24 hour time to am/pm format
        $date = new DateTime($lecture_time);
        $lecture_time = $date->format('h:i:s a');

        $users= $this->User_model->get_tokens($course_id);
        if(!$users)
        {
            echo json_encode(array('status' => "error", 'users' => "No users found registered for this lecture."));
            return;
        }

        //Sending notification to each user
        foreach ($users as $user)
        {
            //Checking whether user is logged-in or not
            //If user is not logged-in, then store notification in table and don't send

            //User not logged in
            if(!($this->User_model->get_user_session($user['user_id'])))
            {
                $notification_data = array(
                    "user_id" => $user['user_id'],
                    "notification_time" => date('Y:m:d H:i:s'),
                    "notification_status" => "false",
                    "type" => "lecture",
                    "id" => $lecture_id,
                    "title" => $lecture_name,
                    "flag" => "10 minutes"
                );

                $this->Notification_model->insertNotification($notification_data);
            }

            //User logged in
            else
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
                $result = json_decode($response->body);
                $status = $result->success;
                //If Notification sent successfully, then storing it in DB
                if($status == 1)
                {
                    $notification_data = array(
                        "user_id" => $user['user_id'],
                        "notification_time" => date('Y:m:d H:i:s'),
                        "notification_status" => "false",
                        "type" => "lecture",
                        "id" => $lecture_id,
                        "title" => $lecture_name,
                        "flag" => "10 minutes"
                    );

                    $this->Notification_model->insertNotification($notification_data);
                }

                //Removing cronjob for this specific notification

            }

        }
    }

    //Quiz Notification - on starting time of quiz
    public function sendQuizRequest()
    {
        $quiz_id = $this->input->get('quiz_id');
        $quiz_title = $this->input->get('quiz_title');

        //Getting users registered in lecture - course
        $lecture_id = $this->input->get('lecture_id');
        $course_id = $this->Lecture_model->get_course_id($lecture_id);
        $users =  $this->User_model->get_tokens($course_id);

        //Sending notification to each user
         foreach ($users as $user)
         {
             //Checking whether user is logged-in or not
             //If user is not logged-in, then store notification in table and don't send

             //User not logged in
             if(!($this->User_model->get_user_session($user['user_id'])))
             {
                 $notification_data = array(
                     "user_id" => $user['user_id'],
                     "notification_time" => date('Y:m:d H:i:s'),
                     "notification_status" => "false",
                     "type" => "quiz",
                     "id" => $quiz_id,
                     "title" => $quiz_title
                 );

                 $this->Notification_model->insertNotification($notification_data);

             }
             else
             {
                 //User logged in
                 $url = 'https://fcm.googleapis.com/fcm/send';
                 $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

                 $fields = array (
                     'notification' => array (
                         "title"=> "Quiz Notification",
                         "body" => $quiz_title . " is about to start.",
                         "icon" => "myicon"
                     ),
                     'data' => array(
                         "quiz_id"=>  $quiz_id
                     ),

                     'to' =>  $user['user_token']

                 );

                 $response = (Requests::post($url, $headers, json_encode($fields)));
                 $result = json_decode($response->body);
                 $status = $result->success;
                 //If Notification sent successfully, then storing it in DB
                 if($status == 1)
                 {
                     $notification_data = array(
                         "user_id" => $user['user_id'],
                         "notification_time" => date('Y:m:d H:i:s'),
                         "notification_status" => "false",
                         "type" => "quiz",
                         "id" => $quiz_id,
                         "title" => $quiz_title
                     );

                     $this->Notification_model->insertNotification($notification_data);
                 }

             }

         }
    }

	//current Lecture Notification - on exact time of lecture
    public function send_currentLectureRequest()
    {
        $lecture_name = $this->input->get('lecture_name');
        $lecture_time = $this->input->get('lecture_time');
        $course_id= $this->input->get('course_id');
        $lecture_id= $this->input->get('lecture_id');

        //Getting tokens of all registered user
        $users= $this->User_model->get_tokens($course_id);
        if(!$users)
        {
            echo json_encode(array('status' => "error", 'users' => "No users found registered for this lecture."));
            return;
        }

        foreach ($users as $user)
        {
            //Checking whether user is logged-in or not
            //If user is not logged-in, then store notification in table and don't send

            //User not logged in
            if(!($this->User_model->get_user_session($user['user_id'])))
            {
                $notification_data = array(
                    "user_id" => $user['user_id'],
                    "notification_time" => date('Y:m:d H:i:s'),
                    "notification_status" => "false",
                    "type" => "lecture",
                    "id" => $lecture_id,
                    "title" => $lecture_name,
                    "flag" => "now"
                );

                $this->Notification_model->insertNotification($notification_data);
            }
            //User logged in
            else
            {
                $url = 'https://fcm.googleapis.com/fcm/send';
                $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDVBrdFCenf2iJli4b-jYYxcsReBctV7YI');

                $fields = array (
                    'notification' => array (
                        "title"=> "Lecture Notification",
                        "body" => $lecture_name . " is about to start.",
                        "icon" => "myicon"
                    ),
                    'data' => array(
                        "lecture_id"=>  $lecture_id
                    ),
                    'to' => $user['user_token']
                );

                $response = (Requests::post($url, $headers, json_encode($fields)));
                $result = json_decode($response->body);
                $status = $result->success;
                //If Notification sent successfully, then storing it in DB
                if($status == 1)
                {
                    $notification_data = array(
                        "user_id" => $user['user_id'],
                        "notification_time" => date('Y:m:d H:i:s'),
                        "notification_status" => "false",
                        "type" => "lecture",
                        "id" => $lecture_id,
                        "title" => $lecture_name,
                        "flag" => "now"
                    );

                    $this->Notification_model->insertNotification($notification_data);
                }
            }

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