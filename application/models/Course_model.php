<?php
class Course_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //Return all courses
    public function get_courses_old()
    {
        $query = $this->db
            ->select('course_ID, course_Name')
            ->order_by('course_ID', ASC)
            ->get('course');

        return $query->result_array();
    }

    //Get all courses- along with teacher and category information
    public function get_courses()
    {
        $this->db->select('course.course_ID, course.course_Name, course.course_Description, course.course_ThumbImage, teacher.teacher_Name, category.category_Name');
        $this->db->from('course');
        $this->db->join('category', 'course.category_ID= category.category_ID');
        $this->db->join('teacher', 'course.teacher_ID= teacher.teacher_ID');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Return a course by name
    public function get_course_name($courseName)
    {
        $query = $this->db
            ->where('course_Name', $courseName)
            ->get('course');

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        } else {
            return null;
        }
    }

    //Return a course by ID
    public function get_course($course_ID)
    {
        $query = $this->db
            ->where('course_ID', $course_ID)
            ->get('course');

        $row = $query->row_array();
        return $row;

    }

    //Adding course of a user!
    public function insert_user_course($data)
    {
        if ($this->db->insert("user_course", $data)) {
            return true;
        }
    }

    //Get Registered courses of a user!
    public function get_user_courses($user_id)
    {
        $this->db
            ->select('course.course_ID, course.course_Name')
            ->from('course')
            ->join('user_course', 'course.course_ID = user_course.course_ID')
            ->where('user_ID', $user_id);

        $query = $this->db->get();
        return $query->result_array();

    }


    //-------------TESTING-------------------

    //Course Registration Validation rules!
    public function getCourseRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'course_Name',
                'label' => 'Course Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]|is_unique[Course.course_Name]'
            ),

            array(
                'field' => 'course_Description',
                'label' => 'Course Description',
                'rules' => 'required'
            ),
            array(
                'field' => 'category_ID',
                'label' => 'Category',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_ID',
                'label' => 'Teacher',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Inserting new Course in table
    public function insertCourse($courseData)
    {
        if($this->db->insert('course', $courseData))
        {
            return true;
        }
    }

}