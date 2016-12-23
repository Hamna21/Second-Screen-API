<?php

class Cronjob_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-------SELECT---------

    //Getting all cronjob of lecture
    public function get_cron_lecture($lecture_id, $flag)
    {
        $query = $this->db
            ->where('type', 'lecture')
            ->where('flag', $flag)
            ->where('id', $lecture_id)
            ->get('cronjob');

        return $query->row();
    }

    //Getting cronjob of quiz
    public function get_cron_quiz($quiz_id)
    {
        $query = $this->db
            ->where('type', 'quiz')
            ->where('id', $quiz_id)
            ->get('cronjob');

        return $query->row();
    }

    //------INSERT-----------
    public function insertJob($data)
    {
        $this->db->insert('cronjob', $data);
    }

    //-----DELETE-----------
    public function deleteJob($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('cronjob');
    }

}