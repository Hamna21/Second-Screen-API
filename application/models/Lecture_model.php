<?php

class Lecture_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Viewing lectures of a course!
    public function get_course_lectures($course_id)
    {
        $query = $this->db
           // ->select('lecture_ID,lecture_Name, lecture_Description, lecture_start','lecture_end')
            ->where('course_ID', $course_id)
            ->get('lecture');

        return $query->result_array();
    }

}