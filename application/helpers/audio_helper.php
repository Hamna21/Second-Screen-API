<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('uploadAudio')) {
    function uploadAudio()
    {
        $CI =& get_instance();
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'wav';
        //[$config['max_size'] = 2048 * 8;
        $config['encrypt_name'] = TRUE;

        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload('audio_path'))
        {
            $status = 0;
            $msg = $CI->upload->display_errors('', '');
            log_message('error', "Audio Error--->". $msg);
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