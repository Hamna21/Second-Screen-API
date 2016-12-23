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

    //Getting a single quiz by its id
    public function get_quiz($quiz_id)
    {
        $query = $this->db
            ->select('quiz_title, quiz_time, quiz_duration')
            ->where('quiz_id', $quiz_id)
            ->get('quiz');

        return $query->row_array();
    }

    //Getting a single quiz by its id - result will include ID too
    public function get_quiz_withID($quiz_id)
    {
        $query = $this->db
            ->select('quiz_id,quiz_title, quiz_time, quiz_duration')
            ->where('quiz_id', $quiz_id)
            ->get('quiz');

        return $query->row_array();
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


    //----------------QUIZ_RESULT-----------------------

    //Getting quiz result - also checking whether quiz has been attempted or not
    public function get_quiz_result($quiz_id, $user_id)
    {
        $query = $this->db
            ->where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->get('quiz_result');

        return $query->row_array();

    }

    public function get_quiz_result_duration($quiz_id, $user_id)
    {
        $this->db->from('quiz_result');
        $this->db->join('quiz', 'quiz.quiz_id = quiz_result.quiz_id');;
        $this->db->where('quiz_result.quiz_id', $quiz_id);
        $this->db->where('quiz_result.user_id', $user_id);
        $query = $this->db->get();
        return $query->row_array();

    }


    //---------INSERT-------

    //Inserting new Quiz
    public function insertQuiz($quiz_data)
    {
        if($this->db->insert('quiz', $quiz_data))
        {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }

    //Inserting quiz status for all users in quiz_result
    public function insert_quizResult($quiz_data)
    {
        $this->db->insert('quiz_result', $quiz_data);
    }


    //--------UPDATE----------
    //Update a quiz by its ID
    public function updateQuiz($quiz_id, $quiz_data)
    {
        $this->db->where("quiz_id", $quiz_id);
        $this->db->update("quiz", $quiz_data);
        return true;
    }

    //Updating status, total and result of quiz
    public function updateQuizResult($quiz_id, $user_id, $quiz_data)
    {
        $this->db->where("quiz_id", $quiz_id);
        $this->db->where("user_id", $user_id);
        $this->db->update("quiz_result", $quiz_data);
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