<?php
class Course_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //------SELECT-----------
    //Return all courses
    public function get_courses_old()
    {
        $query = $this->db
            ->select('course_ID, course_Name')
            ->order_by('course_ID', 'ASC')
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
        $this->db->order_by('course_ID', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Get all courses within limit for pagination
    public function get_courses_limit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('course');
        $this->db->join('category', 'course.category_ID= category.category_ID');
        $this->db->join('teacher', 'course.teacher_ID= teacher.teacher_ID');
        $this->db->order_by('course_ID', 'DESC');
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

    //Get a single course by ID - Using JOIN
    public function get_course_join($courseID)
    {
        $this->db->from('course');
        $this->db->join('category', 'course.category_ID= category.category_ID');
        $this->db->join('teacher', 'course.teacher_ID= teacher.teacher_ID');
        $this->db->where('course_ID', $courseID);
        $query = $this->db->get();
        return $query->row_array();
    }

    //Getting total count of Courses
    public function getCourseTotal()
    {
        $this->db->from('course');
        return $this->db->count_all_results();
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

    //------INSERT----------
    //Adding course of a user!
    public function insert_user_course($data)
    {
        if ($this->db->insert("user_course", $data)) {
            return true;
        }
    }

    //Inserting new Course in table
    public function insertCourse($courseData)
    {
        if($this->db->insert('course', $courseData))
        {
            return true;
        }
    }


    //Update a course by its ID
    public function updateCourse($courseID, $courseData)
    {
        $this->db->where("course_ID", $courseID);
        $this->db->update("course", $courseData);
        return true;
    }

    //-------------DELETE-------------------//

    //Delete a course by its ID
    public function deleteCourse($courseID)
    {
        $this->db->where('course_ID', $courseID);
        $this->db->delete('course');
        return true;
    }

    //----AJAX HELPER FUNCTION

    //Finding a course by its Name - checking if exists in DB
    public function getCourse_Name($q)
    {
        $exist = "Course Name already exists - Try Again!";
        $query = $this->db
            ->where('course_Name',$q)
            ->get('course');
        if($query->num_rows() > 0)
        {
            return $exist;
        }
    }

    //-------------Validation-------------------

    //Course Registration Validation rules!
    public function getCourseRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'course_Name',
                'label' => 'Course Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]|is_unique[course.course_Name]'
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
            ),
            array(
                'field' => 'course_Image',
                'label' => 'Course Image',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Course Edit Validation rules!
    public function getCourseEditRules()
    {
        $config = array(
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



}