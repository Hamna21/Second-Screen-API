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

    //Getting all quizzes of a lecture
    public function get_quizzes($lecture_id)
    {
        $query = $this->db
            ->where('lecture_id', $lecture_id)
            ->get('quiz');

        return $query->result_array();
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

    //----------Validation Rules--------------
    public function getQuizRegistrationRules()
    {
        $config = array(
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