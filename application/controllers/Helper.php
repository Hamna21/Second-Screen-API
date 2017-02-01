<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');

        $this->load->model('Helper_model');
        $this->load->helper(array('url'));
    }
    //Checking if Name is already in DB - AJAX Helper function
    public function categoryNameExist()
    {
        $categoryName = $_REQUEST["q"];
        $result = $this->Helper_model->getCategory_Name($categoryName);
        echo $result;
    }

    public function deleteImages()
    {
        //$images_table array will contains names on images stored in Database
        $images_table = array();
        $count = 0;

        //Getting images of each table
        $images_user = $this->Helper_model->get_images_user();
        $images_course = $this->Helper_model->get_images_course();
        $images_category = $this->Helper_model->get_images_category();
        $images_teacher = $this->Helper_model->get_images_teacher();


        $images_query = array_merge($images_user, $images_course, $images_category, $images_teacher);

       // var_dump($images_query);
        //return;

        //Storing images in $image_table - to convert array entries to strings
        foreach ($images_query as $image_query)
        {
           // $images_table[$count] = $image_query['teacher_thumbimage'];
            $images_table[$count] =array_values($image_query);
            $count += 1;

        }

        //$images_table = implode(" ", $images_table);
        var_dump($images_table);
        return;

        //----------------------------------------------------------------------------------------------------------//

        $images_server = array();
        $count = 0;

        $row = exec('ls /opt/lampp/htdocs/second_screen_api/uploads',$output,$error);
        while(list(,$row) = each($output))
        {
            $images_server[$count] = $row;
            $count += 1;
        }
        if($error)
        {
            echo "Error : $error<BR>\n";
        }

        var_dump($images_server);

        $result=array_diff($images_server,$images_table);
        echo "DIFFERENCES ARE:-> ";
        var_dump($result);
    }
}