<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Teacher extends CI_Controller 
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

        $this->load->model('teacher_model');
        $this->load->model('Teacher_model');

        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
    }

    //-------------------DASHBOARD-----------------------//
    //List of teachers specified within limit and total count of teachers
    public function teachers_dashboard()
    {
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $data = json_decode(file_get_contents("php://input"));
            $limit = $data->limit;
            $start= $data->start;
            $teachers = $this->Teacher_model->get_teachers_limit($limit, $start);
            if(!$teachers)
            {
                echo json_encode(array('status' => "error", "error_message" => "No teacher found!"));
                return;
            }
            $teacherTotal = $this->Teacher_model->getTeacherTotal();
            if(!$teacherTotal)
            {
                echo json_encode(array('status' => "error"));
                return;
            }
            echo json_encode(array("status" => "success","teachers" => $teachers, "teacherTotal" => $teacherTotal));
            return;
        }
    }

    //Getting a single teacher by ID
    public function teacher()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $teacherID = $_REQUEST["teacherID"];
            $teacher = $this->Teacher_model->get_teacher($teacherID);
            if(!$teacher)
            {
                echo json_encode(array('status' => "error", "error_message" => "No teacher found!"));
                return;
            }
            echo json_encode(array('status' => "success", "teacher" => $teacher));
            return;
        }
    }

    public function addTeacher()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $teacher_data = array(
                'teacher_Name' => $data->teacher_Name,
                'teacher_Designation' => $data->teacher_Designation,
                'teacher_Domain' => $data->teacher_Domain,
                'teacher_Image' => $data->teacher_Image
            );

            $this->form_validation->set_data($teacher_data); //Setting Data
            $this->form_validation->set_rules($this->Teacher_model->getTeacherRegistrationRules()); //Setting Rules

            //Reloading form page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'teacherName_Error' => form_error('teacher_Name'),
                    'teacherDesignation_Error' => form_error('teacher_Designation'),
                    'teacherDomain_Error' => form_error('teacher_Domain'),
                    'teacherImage_Error' => form_error('image_Path')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            $imageName = substr($teacher_data['teacher_Image'],strrpos($teacher_data['teacher_Image'],"/")+1);
            $url = $teacher_data['teacher_Image'];
            $contents = file_get_contents($url);
            $save_path="./uploads/". $imageName;

            file_put_contents($save_path,$contents);
            $teacher_data['teacher_Image'] =  $imageName;
            $teacher_data['teacher_ThumbImage'] =  createThumbnail($imageName);

            if ($this->Teacher_model->insertTeacher($teacher_data)) {
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

    public function editTeacher()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data = json_decode(file_get_contents("php://input"));
            $teacherID = $data->teacher_ID;
            $teacher_data = array(
                'teacher_Name' => $data->teacher_Name,
                'teacher_Designation' => $data->teacher_Designation,
                'teacher_Domain' => $data->teacher_Domain
            );

            $this->form_validation->set_data($teacher_data); //Setting Data
            $this->form_validation->set_rules($this->Teacher_model->getTeacherEditRules()); //Setting Rules

            //Reloading form page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'teacherName_Error' => form_error('teacher_Name'),
                    'teacherDesignation_Error' => form_error('teacher_Designation'),
                    'teacherDomain_Error' => form_error('teacher_Domain')
                );

                echo json_encode(array('status' => "Error in Validation", 'error_messages' => $error_data));
                return;
            }

            if(array_key_exists('teacher_Image', $data))
            {
                $teacherImage = $data->teacher_Image;

                //Deleting previous images from API server
                $teacher_PrevImage = $data->teacher_PrevImage;
                $teacher_PrevThumbImage = $data->teacher_PrevThumbImage;
                unlink("uploads/".$teacher_PrevImage);
                unlink("uploads/".$teacher_PrevThumbImage);

                $imageName = substr($teacherImage,strrpos(($teacherImage),"/")+1);
                $url = $teacherImage;
                $contents = file_get_contents($url);
                $save_path="./uploads/". $imageName;

                file_put_contents($save_path,$contents);
                $teacher_data['teacher_Image'] =  $imageName;
                $teacher_data['teacher_ThumbImage'] =  createThumbnail($imageName);

            }
            if ($this->Teacher_model->updateTeacher($teacherID,$teacher_data)) {
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

    public function deleteTeacher()
    {
        if($this->input->server('REQUEST_METHOD') == "GET") {
            $teacherID = $_REQUEST["teacherID"];
            $teacher = $this->Teacher_model->get_teacher($teacherID);

            //Delete images from API server

            unlink("uploads/".$teacher['teacher_ThumbImage']);
            unlink("uploads/".$teacher['teacher_Image']);
            if($this->Teacher_model->deleteTeacher($teacherID))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;

        }
    }
}