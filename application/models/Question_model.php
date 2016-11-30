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

    //Getting all questions of a particular quiz
    public function get_questions($quiz_id)
    {
        $query = $this->db
            ->where('quiz_id', $quiz_id)
            ->order_by('question_id', 'DESC')
            ->get('question');

        return $query->result_array();
    }


    //Getting all questions of a particular quiz - customized for notification
    public function get_questions_quiz($quiz_id)
    {
        $query = $this->db
            ->select('question_id,question_text, option_one,option_two,option_three,option_four')
            ->where('quiz_id', $quiz_id)
            ->order_by('question_id', 'DESC')
            ->get('question');

        return $query->result_array();
    }

    //Getting all questions of a particular quiz
    public function get_questions_limit($quiz_id, $limit, $start)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->where('quiz_id', $quiz_id)
            ->order_by('question_id', 'DESC')
            ->get('question');

        return $query->result_array();
    }

    //Getting total count of all questions in a quiz
    public function getQuestionTotal($quiz_id)
    {
        $this->db->from('question');
        $this->db->where('quiz_id', $quiz_id);
        return $this->db->count_all_results();
    }

    //Getting correct answer of a question
    public function correct_response($question_id, $quiz_id)
    {
         $this->db
            ->select('correct_option')
            ->from('question')
            ->where('question_id', $question_id)
            ->where('quiz_id', $quiz_id);
        return $this->db->get()->row('correct_option');
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

    //--------UPDATE----------
    //Update a question by its ID
    public function updateQuestion($question_id, $question_data)
    {
        $this->db->where("question_id", $question_id);
        $this->db->update("question", $question_data);
        return true;
    }


    //---------DELETE------

    //Deleting quiz
    public function deleteQuestion($question_id)
    {
        $this->db->where('question_id', $question_id);
        $this->db->delete('question');
        return true;
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