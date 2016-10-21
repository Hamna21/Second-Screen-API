<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('uploadPicture'))
{
    function uploadPicture()
    {
        $CI =& get_instance();
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|doc|txt';
        $config['max_size'] = 1024 * 8;
        $config['encrypt_name'] = TRUE;

        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload('image_Path'))
        {
            $status = 0;
            $msg = $CI->upload->display_errors('', '');
            return array($status, $msg);
        }
        else
        {
            $status = 1;
            $data = $CI->upload->data();
            $fileName = $data['full_path'];
            return array($status, $fileName);
        }
    }
}