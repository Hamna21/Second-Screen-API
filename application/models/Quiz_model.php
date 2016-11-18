<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quiz_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-------SELECT------

    //Getting all quizzes of a particular lecture
    public function get_quizzes($lecture_id)
    {
        $query = $this->db
            ->where('lecture_id', $lecture_id)
            ->order_by('quiz_id', 'DESC')
            ->get('quiz');

        return $query->result_array();
    }

    //Getting all quizzes of a particular lecture - for pagination
    public function get_quizzes_limit($lecture_id, $limit, $start)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->where('lecture_id', $lecture_id)
            ->order_by('quiz_id', 'DESC')
            ->get('quiz');

        return $query->result_array();
    }

    public function get_quizTotal($lecture_id)
    {
        $this->db->from('quiz');
        $this->db->where('lecture_id', $lecture_id);
        return $this->db->count_all_results();
    }

    //---------INSERT-------

    //Inserting new Quiz
    public function insertQuiz($quiz_data)
    {
        if($this->db->insert('quiz', $quiz_data))
        {
            return true;
        }
    }


    //--------UPDATE----------
    //Update a quiz by its ID
    public function updateQuiz($quiz_id, $quiz_data)
    {
        $this->db->where("quiz_id", $quiz_id);
        $this->db->update("quiz", $quiz_data);
        return true;
    }



    //---------DELETE------

    //Deleting quiz
    public function deleteQuiz($quiz_id)
    {
        $this->db->where('quiz_id', $quiz_id);
        $this->db->delete('quiz');
        return true;
    }

    //----------Validation Rules--------------
    public function getQuizRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'quiz_title',
                'label' => 'Quiz Title',
                'rules' => 'required'
            ),
            array(
                'field' => 'quiz_time',
                'label' => 'Quiz Time',
                'rules' => 'required'
            ),
            array(
                'field' => 'quiz_duration',
                'label' => 'Quiz Duration',
                'rules' => 'required'
            )
        );

        return $config;
    }

}