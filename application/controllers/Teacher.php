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
            $teacher_id = $_REQUEST["teacher_id"];
            $teacher = $this->Teacher_model->get_teacher($teacher_id);
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
                'teacher_name' => $data->teacher_Name,
                'teacher_designation' => $data->teacher_Designation,
                'teacher_domain' => $data->teacher_Domain,
                'teacher_image' => $data->teacher_Image
            );

            $this->form_validation->set_data($teacher_data); //Setting Data
            $this->form_validation->set_rules($this->Teacher_model->getTeacherRegistrationRules()); //Setting Rules

            //Reloading form page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'teacherName_Error' => form_error('teacher_name'),
                    'teacherDesignation_Error' => form_error('teacher_designation'),
                    'teacherDomain_Error' => form_error('teacher_domain'),
                    'teacherImage_Error' => form_error('teacher_image')
                );

                echo json_encode(array('status' => "error in validation", 'error_messages' => $error_data));
                return;
            }

            $imageName = substr($teacher_data['teacher_image'],strrpos($teacher_data['teacher_image'],"/")+1);
            $url = $teacher_data['teacher_image'];
            $contents = file_get_contents($url);
            $save_path="./uploads/". $imageName;

            file_put_contents($save_path,$contents);
            $teacher_data['teacher_image'] =  $imageName;
            $teacher_data['teacher_thumbimage'] =  createThumbnail($imageName);

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
            $teacher_id = $data->teacher_ID;
            $teacher_data = array(
                'teacher_name' => $data->teacher_Name,
                'teacher_designation' => $data->teacher_Designation,
                'teacher_domain' => $data->teacher_Domain
            );

            $this->form_validation->set_data($teacher_data); //Setting Data
            $this->form_validation->set_rules($this->Teacher_model->getTeacherEditRules()); //Setting Rules

            //Reloading form page if validation fails
            if ($this->form_validation->run() == FALSE) {
                $error_data = array(
                    'teacherName_Error' => form_error('teacher_name'),
                    'teacherDesignation_Error' => form_error('teacher_designation'),
                    'teacherDomain_Error' => form_error('teacher_domain')
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
                $teacher_data['teacher_image'] =  $imageName;
                $teacher_data['teacher_thumbimage'] =  createThumbnail($imageName);

            }
            if ($this->Teacher_model->updateTeacher($teacher_id,$teacher_data)) {
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
            $teacher_id = $_REQUEST["teacher_id"];
            $teacher = $this->Teacher_model->get_teacher($teacher_id);

            //Delete images from API server

            unlink("uploads/".$teacher['teacher_thumbimage']);
            unlink("uploads/".$teacher['teacher_image']);
            if($this->Teacher_model->deleteTeacher($teacher_id))
            {
                echo json_encode(array('status' => "success"));
                return;
            }
            echo json_encode(array('status' => "error"));
            return;
        }
    }
}