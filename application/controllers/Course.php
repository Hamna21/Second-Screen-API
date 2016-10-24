<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Course extends CI_Controller
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
        $this->load->model('Teacher_model');
        $this->load->model('Lecture_model');
        $this->load->helper(array('form', 'url', 'image','string'));
        $this->load->library('form_validation', 'email');
    }

    //List of all courses
    public function courses()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            $courses = $this->Course_model->get_courses();
            if(!$courses)
            {
                echo json_encode(array('status' => "error", "error_message" => "No course found!"));
                return;
            }
            echo json_encode(array('status' => "success", "Courses" => $courses));
            return;
        }
    }

    //Search a course by name
    public function search_course()
    {
        if($this->input->server("REQUEST_METHOD") == "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $course_Name = $data->course_Name;

            //Searching a course by Name
            $course = $this->Course_model->get_course_name($course_Name);
            if(!$course)
            {
                echo json_encode(array('status' => "error", "error_message" => "No Course found with this name"));
                return;
            }

            //Displaying complete information of course
            $teacher = $this->Teacher_model->get_teacher($course['teacher_ID']);
            $myArray = array();
            $object = new stdClass();
            $object->Course = array('course_ID' => $course['course_ID'],'course_Name' => $course['course_Name'], 'course_Description' => $course['course_Description'], 'course_Image' => $course['course_Image']   );
            $object->Teacher = $teacher;
            $myArray[] = $object;
            echo json_encode(array('status' => "success", "Course" => $myArray));
            return;
        }
    }

    //Get a course by ID
    public function course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $course_id = $this->input->get('course_id');
            $course= $this->Course_model->get_course($course_id);
            if(!$course)
            {
                echo json_encode(array('status' => "error", "error_message" => "No course exists with this id!"));
                return;
            }

            $teacher = $this->Teacher_model->get_teacher($course['teacher_ID']);

            $myArray = array();
            $object = new stdClass();
            $object->Course = array('course_ID' => $course['course_ID'],'course_Name' => $course['course_Name'], 'course_Description' => $course['course_Description'], 'course_Image' => $course['course_Image']   );
            $object->Teacher = $teacher;
            $myArray[] = $object;
            echo json_encode(array('status' => "success", "Course" => $myArray));
            return;

        }
    }

    //Return all lectures in a course
    public function course_lectures()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $course_id = $this->input->get('course_id');
            $lectures = $this->Lecture_model->get_course_lectures($course_id);
            if(!$lectures)
            {
                echo json_encode(array('status' => "error", "error_message" => "No lectures found in this course!"));
                return;
            }
            echo json_encode(array('status' => "success", "Lectures" => $lectures));
            return;
        }
    }

    //---------------------USER/COURSES------------------//
    public function user_courses()
    {
        if($this->input->server('REQUEST_METHOD')== "GET")
        {
            $user_id = $this->input->get('user_id');

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($user_id)))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in - cannot view registered courses"));
                return;
            }

            $courses = $this->Course_model->get_user_courses($user_id);
            if(!$courses)
            {
                echo json_encode(array('status' => "error", "error_message" => "No registered courses for this user"));
                return;
            }
            echo json_encode(array('status' => "success", "Courses" => $courses));
            return;
        }
    }

    public function add_user_course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $data = array(
                'user_ID' => $this->input->get('user_id'),
                'course_ID' => $this->input->get('course_id')

            );

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($data['user_ID'])))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in - cannot enroll a course"));
                return;
            }

            //Enrolling a user in a course
            if(!($this->Course_model->insert_user_course($data)))
            {
                echo json_encode(array('status' => "error", "error_message" => "Couldn't add course!"));
                return;
            }
            echo json_encode(array('status' => "success"));
            return;

        }
    }

    //----------------CHECKING-------------------------//
    public function check()
    {
        if($this->input->server('REQUEST_METHOD')== "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $tester_data = array(
                'api_name' => $data->name
            );

            echo json_encode(array('status' => "bla bla", "dashboard_name" => $tester_data['api_name']));
            return;
        }
    }

    public function checkCourse()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $course_data = array(
                'course_Name' => $data->course_Name,
                'course_Description' => $data->course_Description,
                'category_ID' => $data->category_ID,
                'teacher_ID' => $data->teacher_ID,
                'course_Image' => $data->course_Image
            );


            $this->form_validation->set_data($course_data); //Setting Data
            $this->form_validation->set_rules($this->Course_model->getCourseRegistrationRules()); //Setting Rules

            //Reloading add course page with same fields if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'courseName_Error' => form_error('course_Name'),
                    'courseDescription_Error' => form_error('course_Description'),
                    'categoryID_Error' => form_error('category_ID'),
                    'teacherID_Error' => form_error('teacher_ID')
                );

                echo json_encode(array('status' => "Error in Validation", 'error_messages' => $error_data));
                return;
            }


            $url = $course_data['course_Image'];
            $contents = file_get_contents($url);
            $save_path="./uploads/2.jpg";
            file_put_contents($save_path,$contents);

            $course_data['course_Image'] = 1;
            $course_data['course_ThumbImage'] = 2;

            if ($this->Course_model->insertCourse($course_data)) {
                echo json_encode(array('status' => "Success"));
                return;
            }
            else
            {
                echo json_encode(array('status' => "Error in DB"));
                return;
            }
        }
    }
}