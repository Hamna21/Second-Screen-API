<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('uploadVideo')) {
    function uploadVideo()
    {
        $CI =& get_instance();
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|mp3|mp4|wma|jpeg|avi|wav';
        //[$config['max_size'] = 2048 * 8;
        $config['encrypt_name'] = TRUE;

        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload('video_path'))
        {
            $status = 0;
            $msg = $CI->upload->display_errors('', '');
            log_message('error', "Video Error--->". $msg);
            return array($status, $msg);
        }
        else
        {
            $status = 1;
            $data = $CI->upload->data();
            $fileName = $data['file_name'];
            return array($status, $fileName);
        }
    }
}
