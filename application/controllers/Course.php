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
        $this->load->model('Category_model');
        $this->load->model('Lecture_model');

        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
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
            echo json_encode(array('status' => "success", "courses" => $courses));
            return;
        }
    }

    //Search a course by name
    public function search_course()
    {
        if($this->input->server("REQUEST_METHOD") == "POST")
        {
            $data = json_decode(file_get_contents("php://input"));
            $course_name = $data->course_name;

            //Searching a course by Name
            $course = $this->Course_model->get_course_name($course_name);
            if(!$course)
            {
                echo json_encode(array('status' => "error", "error_message" => "No course found with this name"));
                return;
            }

            //Displaying complete information of course
            $teacher = $this->Teacher_model->get_teacher($course['teacher_id']);
            $myArray = array();
            $object = new stdClass();
            $object->Course = array('course_id' => $course['course_id'],'course_name' => $course['course_name'], 'course_description' => $course['course_description'], 'course_image' => $course['course_image']   );
            $object->Teacher = $teacher;
            $myArray[] = $object;
            echo json_encode(array('status' => "success", "course" => $myArray));
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

            $teacher = $this->Teacher_model->get_teacher($course['teacher_id']);

            $myArray = array();
            $object = new stdClass();
            $object->Course = array('course_id' => $course['course_id'],'course_name' => $course['course_name'], 'course_description' => $course['course_description'], 'course_image' => $course['course_image']   );
            $object->Teacher = $teacher;
            $myArray[] = $object;
            echo json_encode(array('status' => "success", "course" => $myArray));
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
            echo json_encode(array('status' => "success", "lectures" => $lectures));
            return;
        }
    }

    //Getting total count of courses stored
    public function course_total()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $courseTotal = $this->Course_model->getCourseTotal();
            if(!$courseTotal)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array('status' => "success", "courseTotal" => $courseTotal));
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
            echo json_encode(array('status' => "success", "courses" => $courses));
            return;
        }
    }

    public function add_user_course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $data = array(
                'user_id' => $this->input->get('user_id'),
                'course_id' => $this->input->get('course_id')

            );

            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($data['user_id'])))
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


    public function delete_user_course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $data = array(
                'user_id' => $this->input->get('user_id'),
                'course_id' => $this->input->get('course_id')
            );


            //Checking whether user is logged-in or not!
            if(!($this->User_model->get_user_session($data['user_id'])))
            {
                echo json_encode(array('status' => "error", "error_message" => "User not logged in - cannot enroll a course"));
                return;
            }

            //Enrolling a user in a course
            if(!($this->Course_model->delete_user_course($data)))
            {
                echo json_encode(array('status' => "error", "error_message" => "Couldn't delete course!"));
                return;
            }
            echo json_encode(array('status' => "success"));
            return;
        }
    }


    //-------------------DASHBOARD-----------------------//

    //List of courses specified within limit and total count of courses
    public function courses_dashboard()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;

            $courses = $this->Course_model->get_courses_limit($limit, $start);
            if(!$courses)
            {
                echo json_encode(array('status' => "error", "error_message" => "No course found!"));
                return;
            }
            $courseTotal = $this->Course_model->getCourseTotal();
            if(!$courseTotal)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array("status" => "success","courses" => $courses, "courseTotal" => $courseTotal));
            return;
        }
    }

    //Returns list of all categories and teachers - used in add and edit course page
    public function categories_teachers_Course()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $categories = $this->Category_model->get_categories();
            $teachers = $this->Teacher_model->get_teachers();

            if(!$categories || !$teachers)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array('status' => "success", "categories" => $categories, "teachers" => $teachers));
            return;
        }
    }

    //Course with complete information of teacher and category
    public function course_join()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $course_id = $_REQUEST["course_id"];
            $course = $this->Course_model->get_course_join($course_id);
            if(!$course)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array('status' => "success", "course" => $course));
            return;
        }
    }

    public function addCourse()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $course_data = array(
                'course_name' => $data->course_Name,
                'course_description' => $data->course_Description,
                'category_id' => $data->category_ID,
                'teacher_id' => $data->teacher_ID,
                'course_image' => $data->course_Image
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

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            $imageName = substr($course_data['course_image'],strrpos($course_data['course_image'],"/")+1);
            $url = $course_data['course_image'];
            $contents = file_get_contents($url);
            $save_path="./uploads/". $imageName;

            file_put_contents($save_path,$contents);
            $course_data['course_image'] =  $imageName;
            $course_data['course_thumbimage'] =  createThumbnail($imageName);

            if ($this->Course_model->insertCourse($course_data)) {
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

    public function editCourse()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $course_id = $data->course_ID;
            $course_data = array(
                'course_description' => $data->course_Description,
                'category_id' => $data->category_ID,
                'teacher_id' => $data->teacher_ID
            );


            $this->form_validation->set_data($course_data); //Setting Data
            $this->form_validation->set_rules($this->Course_model->getCourseEditRules()); //Setting Rules

            //Reloading add course page with same fields if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'courseDescription_Error' => form_error('course_Description'),
                    'categoryID_Error' => form_error('category_ID'),
                    'teacherID_Error' => form_error('teacher_ID')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            if(array_key_exists('course_Image', $data))
            {
                $courseImage = $data->course_Image;

                //Deleting previous images from API server
                $course_PrevImage = $data->course_PrevImage;
                $course_PrevThumbImage = $data->course_PrevThumbImage;
                unlink("uploads/".$course_PrevImage);
                unlink("uploads/".$course_PrevThumbImage);

                $imageName = substr($courseImage,strrpos(($courseImage),"/")+1);
                $url = $courseImage;
                $contents = file_get_contents($url);
                $save_path="./uploads/". $imageName;

                file_put_contents($save_path,$contents);
                $course_data['course_image'] =  $imageName;
                $course_data['course_thumbimage'] =  createThumbnail($imageName);

            }
            if ($this->Course_model->updateCourse($course_id,$course_data)) {
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

    public function deleteCourse()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $course_id = $_REQUEST["course_id"];
            $course = $this->Course_model->get_course_join($course_id);

            //Delete images from API server

            unlink("uploads/".$course['course_ThumbImage']);
            unlink("uploads/".$course['course_Image']);
            if($this->Course_model->deleteCourse($course_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;

        }

    }
}