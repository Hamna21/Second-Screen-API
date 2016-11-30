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