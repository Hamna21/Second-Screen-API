<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $this->load->model('Category_model');
        $this->load->model('Lecture_model');
        $this->load->model('Course_model');
        $this->load->helper(array('form', 'url', 'image'));
        $this->load->library('form_validation');
    }
    //Checking if Name is already in DB - AJAX Helper function
    public function categoryNameExist()
    {
        $categoryName = $_REQUEST["q"];
        $result = $this->Category_model->getCategory_Name($categoryName);
        echo $result;
    }

    //Checking if Name is already in DB
    public function lectureNameExist()
    {
        $lectureName = $_REQUEST["q"];
        $result = $this->Lecture_model->getLecture_Name($lectureName);
        echo $result;
    }

    //Checking if Name is already in DB
    public function courseNameExist()
    {
        $courseName = $_REQUEST["q"];
        $result = $this->Course_model->getCourse_Name($courseName);
        echo $result;
    }

}