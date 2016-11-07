<?php

class Comment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Showing comments of a course!
    public function get_comments($course_id)
    {
        $query = $this->db
            ->select('user_id, comment_text, comment_time')
            ->where('course_id', $course_id)
            ->get('comment');

        return $query->result_array();
    }

    //Get all comments of a course using join
    public function get_comments_join($course_id)
    {
        $this->db->select('comment_text, comment_time,  comment.user_id,user_name, user_image');
        $this->db->from('comment');
        $this->db->join('user', 'comment.user_id = user.user_id');
        $this->db->where('course_id', $course_id);
        $query = $this->db->get();
        return $query->result_array();

    }

    //Adding comment
    public function create_comment($comment_data)
    {
        if ($this->db->insert("comment", $comment_data)) {
            return true;
        }
    }
}