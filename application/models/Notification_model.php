<?php
class Notification_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //-------SELECT-----
    //Get all notifications of a user
    public function get_notifications($user_id)
    {
        $this->db
            ->from('notification')
            ->where('user_id', $user_id)
            ->order_by('notification_id', 'DESC');

        $query = $this->db->get();
        return $query->result_array();

    }

    //Get total count of all un-read notifications
    public function countNotifications($user_id)
    {
        $this->db->from('notification');
        $this->db->where('user_id',$user_id);
        $this->db->where('notification_status',"false");
        return $this->db->count_all_results();
    }

    //------INSERT------

    //Inserting new Notification in Notification table
    public function insertNotification($notification_data)
    {
        if ($this->db->insert("notification", $notification_data))
        {
            return true;
        }
    }


    //-----------UPDATE------

    //Updating read status of notifications
    public function updateStatus($notification_id,$notification_status)
    {
        $this->db->where('notification_id', $notification_id);
        $this->db->update('notification', $notification_status);
    }
}

