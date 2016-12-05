<?php

class Reference_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---------SELECT-------

    //Getting all reference of a lecture
    public function get_references_lecture($lecture_id)
    {
        $this->db->from('lecture_reference');
        $this->db->where('lecture_id', $lecture_id);
        $this->db->order_by('reference_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();

    }


    //Getting all reference of a lecture - including name of reference lecture name
    public function get_references_lecture2($lecture_id)
    {
        $this->db->select('lecture_reference.reference_id,lecture_reference.lecture_id,lecture_reference.time,lecture_reference.value, lecture.lecture_name,
        lecture_reference.image,lecture_reference.thumbImage');
        $this->db->from('lecture');
        $this->db->join('lecture_reference', 'lecture.lecture_id= lecture_reference.value');
        $this->db->where('lecture_reference.lecture_id', $lecture_id);
        $this->db->order_by('reference_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();

    }

    //---------INSERT-------
    //Inserting new Reference
    public function insertReference($referenceData)
    {
        if($this->db->insert('lecture_reference', $referenceData))
        {
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }


}