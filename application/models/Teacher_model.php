<?php
class Teacher_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---------SELECT-------
    //teacher Information
    public function get_teacher($teacher_id)
    {
        $query = $this->db
            ->where('teacher_id', $teacher_id)
            ->get('teacher');

        return $query->row_array();
    }

    //Return all teachers
    public function get_teachers()
    {
        $query = $this->db
            ->order_by('teacher_id', "DESC")
            ->get('teacher');
        return $query->result_array();
    }


    //Getting total count of Teachers
    public function getTeacherTotal()
    {
        $this->db->from('teacher');
        return $this->db->count_all_results();
    }

    //Get all teachers within limit - for pagination purposes
    public function get_teachers_limit($limit, $start)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->order_by('teacher_id', "DESC")
            ->get('teacher');

        return $query->result_array();
    }

    //---------INSERT-------

    //Insert new Teacher
    public function insertTeacher($teacherData)
    {
        if($this->db->insert('teacher', $teacherData))
        {
            return true;
        }
    }

    //---------UPDATE-------
    //Update a teacher by its ID
    public function updateTeacher($teacherID, $teacherData)
    {
        $this->db->where("teacher_id", $teacherID);
        $this->db->update("teacher", $teacherData);
        return true;
    }

    //---------DELETE-------

    //Delete a teacher by its ID
    public function deleteTeacher($teacherID)
    {
        $this->db->where('teacher_id', $teacherID);
        $this->db->delete('teacher');
        return true;
    }

    //-------Validation Rules-------//

    //Teacher Registration Validation rules!
    public function getTeacherRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'teacher_name',
                'label' => 'Teacher Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_designation',
                'label' => 'Teacher Designation',
                'rules' => 'required|regex_match[/^[A-Za-z_ -]+$/]'
            ),
            array(
                'field' => 'teacher_domain',
                'label' => 'Teacher Domain',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_image',
                'label' => 'Teacher Image',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Teacher Edit Validation rules!
    public function getTeacherEditRules()
    {
        $config = array(
            array(
                'field' => 'teacher_name',
                'label' => 'Teacher Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_designation',
                'label' => 'Teacher Designation',
                'rules' => 'required|regex_match[/^[A-Za-z_ -]+$/]'
            ),
            array(
                'field' => 'teacher_domain',
                'label' => 'Teacher Domain',
                'rules' => 'required'
            )
        );

        return $config;
    }

}