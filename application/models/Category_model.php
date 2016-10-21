<?php

class Category_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Return all categories
    public function get_categories()
    {
        $query = $this->db
            ->order_by('category_ID', ASC)
            ->get('category');
        return $query->result_array();
    }

    //Return all courses in a specific category
    public function get_courses_category($category_id)
    {
        $query = $this->db
            ->select('course_ID, course_Name')
            ->where('category_ID', $category_id)
            ->get('course');

        return $query->result_array();
    }

}