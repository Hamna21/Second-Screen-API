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
            ->order_by('comment_time', 'DESC')
            ->get('comment');

        return $query->result_array();
    }

    //Showing comments of a course within limit for pagination
    public function get_comments_dashboard($limit,$start,$course_id)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->select('user_id, comment_text, comment_time')
            ->where('course_id', $course_id)
            ->order_by('comment_time', 'DESC')
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

    public function get_commentsTotal($course_id)
    {
        $this->db->from('comment');
        $this->db->where('course_id', $course_id);
        return $this->db->count_all_results();
    }

    //Adding comment
    public function create_comment($comment_data)
    {
        if ($this->db->insert("comment", $comment_data)) {
            return true;
        }
    }


    //-------------------LECTURE---------------------------//
    //Showing comments of a lecture
    public function get_comments_lecture($lecture_id)
    {
        $query = $this->db
            ->select('user_id, comment_text, comment_time')
            ->where('lecture_id', $lecture_id)
            ->order_by('comment_time', 'DESC')
            ->get('comment_lecture');

        return $query->result_array();
    }

    //Showing comments of a lecture withing limit for pagination
    public function get_commentsLecture_dashboard($limit, $start, $lecture_id)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->select('user_id, comment_text, comment_time')
            ->where('lecture_id', $lecture_id)
            ->order_by('comment_time', 'DESC')
            ->get('comment_lecture');

        return $query->result_array();
    }

    public function get_lectureComment_total($lecture_id)
    {
        $this->db->from('comment_lecture');
        $this->db->where('lecture_id', $lecture_id);
        return $this->db->count_all_results();
    }

    //Adding comment of lecture
    public function create_comment_lecture($comment_data)
    {
        if ($this->db->insert("comment_lecture", $comment_data)) {
            return true;
        }
    }

}