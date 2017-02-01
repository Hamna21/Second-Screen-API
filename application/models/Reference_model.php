<?php

class Reference_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---------SELECT-------

    //Getting all references of a particular lecture - for pagination
    public function get_references_limit($lecture_id, $limit, $start)
    {
        $query = $this->db
            ->limit($limit, $start)
            ->where('lecture_id', $lecture_id)
            ->order_by('reference_id', 'DESC')
            ->get('lecture_reference');

        return $query->result_array();
    }

    //Getting total count of references of a single lecture
    public function get_referenceTotal($lecture_id)
    {
        $this->db->from('lecture_reference');
        $this->db->where('lecture_id', $lecture_id);
        return $this->db->count_all_results();
    }

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

    public function get_reference($reference_id)
    {
        $this->db->from('lecture_reference');
        $this->db->where('reference_id', $reference_id);
        $query = $this->db->get();
        return $query->row();
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

    //----------UPDATE-----------
    //Updating existing reference
    public function updateReference($reference_id,$reference_data)
    {
        $this->db->where("reference_id", $reference_id);
        $this->db->update("lecture_reference", $reference_data);
        return true;
    }

    //------------DELETE--------
    //Deleting previous reference
    public function deleteReference($reference_id)
    {
        $this->db->where("reference_id", $reference_id);
        $this->db->delete('lecture_reference');
        return true;
    }


}