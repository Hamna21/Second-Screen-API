<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $this->load->model('Category_model');
        $this->load->helper(array('form', 'url'));
    }
    //Checking if Name is already in DB - AJAX Helper function
    public function categoryNameExist()
    {
        $categoryName = $_REQUEST["q"];
        $result = $this->Category_model->getCategory_Name($categoryName);
        echo $result;
    }
}