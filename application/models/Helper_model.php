<?php
class Helper_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    //---------------SELECT-----------------------//
    //Getting image names of all users
    public function get_images_user()
    {
        $this->db
            ->select('user_image')
            ->from('user');

        $query = $this->db->get();
        return $query->result_array();

    }

    //Getting image names of all courses
    public function get_images_course()
    {
        $this->db
            ->select('course_image,course_thumbimage')
            ->from('course');

        $query = $this->db->get();
        return $query->result_array();

    }

    //Getting image names of all categories
    public function get_images_category()
    {
        $this->db
            ->select('category_image,category_thumbimage')
            ->from('category');

        $query = $this->db->get();
        return $query->result_array();

    }

    //Getting image names of all teachers
    public function get_images_teacher()
    {
        $this->db
            ->select('teacher_image,teacher_thumbimage')
            ->from('teacher');

        $query = $this->db->get();
        return $query->result_array();

    }

    //------------AJAX HELPER FUNCTIONS-----------//

    //Finding a category by its Name - exist in DB or not
    public function getCategory_Name($categoryName)
    {
        $exist = "Category Name already in database - Try Again!";
        $query = $this->db
            ->where('category_name',$categoryName)
            ->get('category');

        if($query->num_rows() > 0)
        {
            return $exist;
        }
    }

}
