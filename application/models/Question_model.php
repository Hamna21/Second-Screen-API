<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Question_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-------SELECT------

    //Getting all questions of a quiz
    public function get_questions($quiz_id)
    {
        $query = $this->db
            ->where('quiz_id', $quiz_id)
            ->get('question');

        return $query->result_array();
    }

    //Getting total count of all questions in a quiz
    public function getQuestionTotal($quiz_id)
    {
        $this->db->from('course');
        return $this->db->count_all_results();
    }

    //---------INSERT-------

    //Inserting new Question
    public function insertQuestion($question_data)
    {
        if($this->db->insert('question', $question_data))
        {
            return true;
        }
    }

    //---Validation Functions
    public function getQuestionRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'question_text',
                'label' => 'Question Text',
                'rules' => 'required'
            ),
            array(
                'field' => 'option_one',
                'label' => 'Option One',
                'rules' => 'required'
            ),
            array(
                'field' => 'option_two',
                'label' => 'Option Two',
                'rules' => 'required'
            ),
            array(
                'field' => 'option_three',
                'label' => 'Option Three',
                'rules' => 'required'
            ),
            array(
                'field' => 'correct_option',
                'label' => 'Correct Option',
                'rules' => 'required'
            )
        );

        return $config;
    }

}