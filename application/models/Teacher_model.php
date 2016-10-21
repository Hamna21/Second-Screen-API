<?php
class Teacher_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //teacher Information
    public function get_teacher($teacher_id)
    {
        $query = $this->db
            ->where('teacher_ID', $teacher_id)
            ->get('teacher');

        $row = $query->result_array();
        return $row;
    }

}