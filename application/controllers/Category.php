<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller
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
        $this->load->model('Category_model');
        $this->load->helper(array('form', 'url', 'image','string'));
        $this->load->library('form_validation');
    }

    //List of all categories
    public function categories()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $categories = $this->Category_model->get_categories();
            if(!$categories)
            {
                echo json_encode(array('status' => "error", "error_message" => "No category found!"));
                return;
            }
            echo json_encode(array('status' => "success", "Categories" => $categories));
            return;
        }
    }

    //Return all courses in a category
    public function courses_category()
    {
        if($this->input->server('REQUEST_METHOD') == "GET")
        {
            $category_id = $this->input->get('category_id');
            $courses = $this->Category_model->get_courses_category($category_id);

            if(!$courses)
            {
                echo json_encode(array('status' => "error", "error_message" => "No courses found in this category!"));
                return;
            }

            echo json_encode(array('status' => "success", "Courses" => $courses));
            return;
        }
    }


}