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
        

        $this->load->model('Lecture_model');
        $this->load->model('Quiz_model');
        $this->load->model('Reference_model');
        $this->load->model('Cronjob_model');

        $this->load->helper(array('form', 'url', 'crontab'));
        $this->load->library('form_validation');
    }

    //Getting all lectures
    public function lectures_reference()
    {
        $lectures = $this->Lecture_model->get_lectures_reference();
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

            //All quizzes of a lecture
            $quizzes = $this->Quiz_model->get_quizzes($lecture_id);
            if($quizzes)
            {
                $lecture['quizzes'] = $quizzes;
            }


            //All references of a lecture
            $references = $this->Reference_model->get_references_lecture($lecture_id);
            if($references)
            {
                foreach ($references as $key => $reference)
                {
                    if($reference['type'] == "lecture")
                    {
                        //For getting lecture-name of reference lecture
                        $lecture_from_reference = $this->Lecture_model->get_lecture($reference['value']);

                        //For getting course_image of reference lecture
                        $course_thumbimage = $this->Lecture_model->get_course_image($reference['value']);

                        $references[$key]['lecture_name']= $lecture_from_reference['lecture_name'];
                        $references[$key]['course_thumbimage']= $course_thumbimage;
                    }
                }
                $lecture['references'] = $references;
            }

            echo json_encode(array('status' => "success", "lecture" => $lecture));
            return;
        }
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

    //Getting a single lecture by ID ------- for dashboard
    public function lecture_dashboard()
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

            echo json_encode(array('status' => "success", "lecture" => $lecture));
            return;
        }
    }

    //Lecture which iS currently being displayed on TV
    public function currentLecture()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {

            //Getting user id
            $user_id= $this->input->get('user_id');
            //Getting current date and time
            $current_date = date('Y:m:d');
            $current_time = date('H:i:s');

            //Lecture currently on TV
            $lecture = $this->Lecture_model->get_current_lecture($current_date, $current_time, $user_id);
            if(!$lecture)
            {
                echo json_encode(array('status' => "error", 'error_message'=> 'No lecture currently on TV!'));
                return;
            }

            echo json_encode(array('status' => "success", 'lecture'=> $lecture[0]));
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
                'lecture_date' => $data->lecture_date,
                'lecture_end' => $data->lecture_end,
                'course_id' => $data->course_ID
            );

            $this->form_validation->set_data($lecture_data); //Setting Data
            $this->form_validation->set_rules($this->Lecture_model->getLectureRegistrationRules()); //Setting Rules

            //Reloading add lecture page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'lectureName_Error' => form_error('lecture_name'),
                    'lectureDescription_Error' => form_error('lecture_description'),
                    'lectureStart_Error' => form_error('lecture_date'),
                    'lectureEnd_Error' => form_error('lecture_end'),
                    'courseID_Error' => form_error('course_id')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            //Adding Lecture Link
            if(array_key_exists('lecture_video_link', $data))
            {
                $lecture_data['lecture_video'] = $data->lecture_video_link;
            }

            //12 hours and minutes will be subtracted from notification date (adjust with server)
            $notification_date = $lecture_data['lecture_date'];

            $time_date = new DateTime($data->lecture_date);
            //Storing original date and time in db
            $lecture_data['lecture_date'] =  $time_date->format('Y-m-d');
            $lecture_data['lecture_start'] = $time_date->format('H:i:s');


            //Subtracting 12 hours and 10 minutes from lecture starting time for 10 minutes prior notification
            $updated_date = strtotime($notification_date);
            $updated_date = $updated_date - (12 * 60 * 60); //Subtracting 12 hours
            $updated_date = $updated_date - (3 * 60); //Subtracting 2 minutes
            $current_lecture_time = $updated_date; //time for current lecture notification
            $updated_date = $updated_date - (10 * 60); //Subtracting 10 minutes
            $notification_date  = date("Y-m-d H:i:s", $updated_date);


            //Extracting date,month,time, hour of lecture starting time from notification_date - 10 minutes earlier
            $notification_date = new DateTime($notification_date);
            $month = $notification_date->format('m');
            $day = $notification_date->format('d');
            $hour = $notification_date->format('H');
            $minute = $notification_date->format('i');

            //Extracting date,month,time, hour of lecture starting time from notification_date - exact lecture time
            $current_lecture_time = date("Y-m-d H:i:s",  $current_lecture_time);
            $current_lecture_time = new DateTime($current_lecture_time);
            $current_month = $current_lecture_time->format('m');
            $current_day = $current_lecture_time->format('d');
            $current_hour = $current_lecture_time->format('H');
            $current_minute = $current_lecture_time->format('i');


            $lecture_id = $this->Lecture_model->insertLecture($lecture_data);
            if ($lecture_id)
            {
                //-----------Cronjob 10 minutes prior-----------------
                lectureNotification($minute, $hour, $day, $month,$lecture_data['course_id'], $lecture_data['lecture_name'],$lecture_data['lecture_start'],$lecture_id);

                //-----------Cronjob on exact lecture time-------------
                currentLectureNotification($current_minute, $current_hour, $current_day, $current_month,$lecture_data['course_id'], $lecture_data['lecture_name'],$lecture_data['lecture_start'], $lecture_id);

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
                'lecture_date' => $data->lecture_date,
                'lecture_end' => $data->lecture_end,
                'course_id' => $data->course_ID
            );

            $this->form_validation->set_data($lecture_data); //Setting Data
            $this->form_validation->set_rules($this->Lecture_model->getLectureEditRules()); //Setting Rules

            //Reloading add lecture page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'lectureName_Error' => form_error('lecture_name'),
                    'lectureDescription_Error' => form_error('lecture_description'),
                    'lectureStart_Error' => form_error('lecture_date'),
                    'lectureEnd_Error' => form_error('lecture_end'),
                    'courseID_Error' => form_error('course_id')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }


            //12 hours and minutes will be subtracted from notification date (adjust with server)
            $notification_date = $lecture_data['lecture_date'];

            $time_date = new DateTime($data->lecture_date);
            //Storing original date and time in db
            $lecture_data['lecture_date'] =  $time_date->format('Y-m-d');
            $lecture_data['lecture_start'] =  $time_date->format('H:i:s');



            //Subtracting 12 hours and 10 minutes from lecture starting time for 10 minutes prior notification
            $updated_date = strtotime($notification_date);
            $updated_date = $updated_date - (12 * 60 * 60); //Subtracting 12 hours
            $updated_date = $updated_date - (3 * 60); //Subtracting 2 minutes
            $current_lecture_time = $updated_date; //time for current lecture notification
            $updated_date = $updated_date - (10 * 60); //Subtracting 10 minutes
            $notification_date  = date("Y-m-d H:i:s", $updated_date);


            //Extracting date,month,time, hour of lecture starting time from notification_date - 10 minutes earlier
            $notification_date = new DateTime($notification_date);
            $month = $notification_date->format('m');
            $day = $notification_date->format('d');
            $hour = $notification_date->format('H');
            $minute = $notification_date->format('i');


            //Formatting string - Adding 0 in case of single digit
            $month = sprintf("%02s", $month);
            $day = sprintf("%02s", $day);
            $hour = sprintf("%02s", $hour);
            $minute = sprintf("%02s", $minute);

            //Extracting date,month,time, hour of lecture starting time from notification_date - exact lecture time
            $current_lecture_time = date("Y-m-d H:i:s",  $current_lecture_time);
            $current_lecture_time = new DateTime($current_lecture_time);
            $current_month = $current_lecture_time->format('m');
            $current_day = $current_lecture_time->format('d');
            $current_hour = $current_lecture_time->format('H');
            $current_minute = $current_lecture_time->format('i');

            //Formatting string - Adding 0 in case of single digit
            $current_month = sprintf("%02s", $current_month);
            $current_day = sprintf("%02s", $current_day);
            $current_hour = sprintf("%02s", $current_hour);
            $current_minute = sprintf("%02s", $current_minute);


            if($this->Lecture_model->updateLecture($lecture_id,$lecture_data))
            {
                //Removing previous cronjob from crontab
                removeLecturesNotification($lecture_id);

                //Removing cronjobs from table
                $this->Cronjob_model->deleteJob($lecture_id);

                //Adding new cronjobs

                //-----------Cronjob 10 minutes prior-----------------
                lectureNotification($minute, $hour, $day, $month,$lecture_data['course_id'], $lecture_data['lecture_name'],$lecture_data['lecture_start'],$lecture_id);


                //-----------Cronjob on exact lecture time-------------
                currentLectureNotification($current_minute, $current_hour, $current_day, $current_month,$lecture_data['course_id'], $lecture_data['lecture_name'],$lecture_data['lecture_start'], $lecture_id);


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