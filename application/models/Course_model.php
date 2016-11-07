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
            ->select('course_id, course_name')
            ->order_by('course_id', 'ASC')
            ->get('course');

        return $query->result_array();
    }

    //Get all courses- along with teacher and category information
    public function get_courses()
    {
        $this->db->select('course.course_id, course.course_name, course.course_description, course.course_thumbimage, teacher.teacher_name, category.category_name');
        $this->db->from('course');
        $this->db->join('category', 'course.category_id= category.category_id');
        $this->db->join('teacher', 'course.teacher_id = teacher.teacher_id');
        $this->db->order_by('course_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Get all courses within limit for pagination
    public function get_courses_limit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('course');
        $this->db->join('category', 'course.category_id= category.category_id');
        $this->db->join('teacher', 'course.teacher_id= teacher.teacher_id');
        $this->db->order_by('course_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Return a course by name
    public function get_course_name($courseName)
    {
        $query = $this->db
            ->where('course_name', $courseName)
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
            ->where('course_id', $course_ID)
            ->get('course');

        $row = $query->row_array();
        return $row;

    }

    //Get a single course by ID - Using JOIN
    public function get_course_join($courseID)
    {
        $this->db->from('course');
        $this->db->join('category', 'course.category_id= category.category_id');
        $this->db->join('teacher', 'course.teacher_id= teacher.teacher_id');
        $this->db->where('course_id', $courseID);
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
            ->select('course.course_id, course.course_name, course.course_description, course.course_image, course.course_thumbimage')
            ->from('course')
            ->join('user_course', 'course.course_id = user_course.course_id')
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
        $this->db->where("course_id", $courseID);
        $this->db->update("course", $courseData);
        return true;
    }

    //-------------DELETE-------------------//

    //Delete a course by its ID
    public function deleteCourse($courseID)
    {
        $this->db->where('course_id', $courseID);
        $this->db->delete('course');
        return true;
    }

    //Adding course of a user!
    public function delete_user_course($data)
    {
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('course_id', $data['course_id']);
        $this->db->delete('user_course');
        return true;
    }


    //----AJAX HELPER FUNCTION

    //Finding a course by its Name - checking if exists in DB
    public function getCourse_Name($q)
    {
        $exist = "Course Name already exists - Try Again!";
        $query = $this->db
            ->where('course_name',$q)
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
                'field' => 'course_name',
                'label' => 'Course Name',
                'rules' => 'required|regex_match[/^[A-Za-z0-9_ -]+$/]|is_unique[course.course_Name]'
            ),

            array(
                'field' => 'course_description',
                'label' => 'Course Description',
                'rules' => 'required'
            ),
            array(
                'field' => 'category_id',
                'label' => 'Category',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_id',
                'label' => 'Teacher',
                'rules' => 'required'
            ),
            array(
                'field' => 'course_image',
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
                'field' => 'course_description',
                'label' => 'Course Description',
                'rules' => 'required'
            ),
            array(
                'field' => 'category_id',
                'label' => 'Category',
                'rules' => 'required'
            ),
            array(
                'field' => 'teacher_id',
                'label' => 'Teacher',
                'rules' => 'required'
            )
        );

        return $config;
    }



}