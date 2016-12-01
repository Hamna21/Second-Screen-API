<?php

class Lecture_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //--------SELECT-------
    //Viewing lectures of a course!
    public function get_course_lectures($course_id)
    {
        $query = $this->db
           // ->select('lecture_ID,lecture_Name, lecture_Description, lecture_start','lecture_end')
            ->where('course_id', $course_id)
            ->get('lecture');

        return $query->result_array();
    }

    //Getting total count of Lectures
    public function getLectureTotal()
    {
        $this->db->from('lecture');
        return $this->db->count_all_results();
    }

    //Getting lectures in limit - for pagination purposes
    public function get_lectures_limit($limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->from('lecture');
        $this->db->join('course', 'lecture.course_id = course.course_id');
        $this->db->order_by('lecture_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting all lectures of a course
    public function get_lectures($course_id)
    {
        $this->db->from('lecture');
        $this->db->where('course_id', $course_id);
        $this->db->order_by('lecture_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting all lectures from lecture table
    public function get_lectures_reference()
    {
        $this->db->from('lecture');
        $this->db->order_by('lecture_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Get a single lecture by it's id
    public function get_lecture($lecture_id)
    {
        $query = $this->db
            ->where('lecture_id', $lecture_id)
            ->get('lecture');


        return $query->row_array();
    }

    //Getting current lecture on tv
    public function get_current_lecture($current_date, $current_time, $user_id)
    {
        $query = $this->db
            ->select('*')
            ->from('lecture')
            ->join('user_course', 'lecture.course_id = user_course.course_id')
            ->where('user_course.user_id', $user_id)
            ->where('lecture_date', $current_date)
            ->where('lecture_start <=', $current_time)
            ->where('lecture_end >=', $current_time);

        return $query->get()->result();
    }


    //Getting course ID via Lecture ID
    public function get_course_id($lecture_id)
    {
        $this->db
            ->select('course_id')
            ->from('lecture')
            ->where('lecture_id', $lecture_id);
        return $this->db->get()->row('course_id');
    }


    //---------INSERT-------
    //Inserting new Lecture
    public function insertLecture($lectureData)
    {
        if($this->db->insert('lecture', $lectureData))
        {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }



    //---------UPDATE-------
    //Update a lecture by its ID
    public function updateLecture($lectureID, $lectureData)
    {
        $this->db->where("lecture_id", $lectureID);
        $this->db->update("lecture", $lectureData);
        return true;
    }

    //---------DELETE-------
    //Delete a course by its ID
    public function deleteLecture($lectureID)
    {
        //$lecture_Image = $this->db->select('lecture_Image');
        //$lecture_ThumbImage = $this->db->select('lecture_ThumbImage');
        $this->db->where('lecture_id', $lectureID);
        // unlink("uploads/".$lecture_Image);
        //unlink("uploads/".$lecture_ThumbImage);
        $this->db->delete('lecture');
        return true;
    }

    //-----AJAX HELPER FUNCTIONS-----//

    //Finding a lecture by its Name
    public function getLecture_Name($q)
    {
        $exist = "Lecture Name already exists - Try Again!";
        $query = $this->db
            ->where('lecture_name',$q)
            ->get('lecture');
        if($query->num_rows() > 0)
        {
            return $exist;
        }
    }

    //-----Validation rules-----//

    //Lecture Registration Validation rules!
    public function getLectureRegistrationRules()
    {
        $config = array(
            array(
                'field' => 'lecture_name',
                'label' => 'Lecture Name',
                'rules' => 'required|is_unique[lecture.lecture_Name]'
            ),

            array(
                'field' => 'lecture_description',
                'label' => 'Lecture Description',
                'rules' => 'required'
            ),
            array(
                'field' => 'lecture_date',
                'label' => 'Lecture Date',
                'rules' => 'required'
            ),
            array(
                'field' => 'lecture_end',
                'label' => 'Lecture Ending Time',
                'rules' => 'required'
            ),
            array(
                'field' => 'course_id',
                'label' => 'Course',
                'rules' => 'required'
            )
        );

        return $config;
    }

    //Lecture Edit Validation rules!
    public function getLectureEditRules()
    {
        $config = array(
            array(
                'field' => 'lecture_name',
                'label' => 'Lecture Name',
                'rules' => 'required'
            ),

            array(
                'field' => 'lecture_description',
                'label' => 'Lecture Description',
                'rules' => 'required'
            ),
            array(
                'field' => 'lecture_start',
                'label' => 'Lecture Starting Time',
                'rules' => 'required'
            ),
            array(
                'field' => 'lecture_end',
                'label' => 'Lecture Ending Time',
                'rules' => 'required'
            ),
            array(
                'field' => 'course_id',
                'label' => 'Course',
                'rules' => 'required'
            )
        );

        return $config;
    }
}