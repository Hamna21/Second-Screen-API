<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Crontab\Crontab;
use Crontab\Job;

//--------------------------------ADDING CRONJOB----------------------------------------------------

//Setting cronjob for lecture notification - 10 minutes earlier
if ( ! function_exists('lectureNotification'))
{
    //Helper function for setting cronjob at specified time
    function lectureNotification($minute, $hour, $day, $month, $course_id, $lecture_name, $lecture_time, $lecture_id)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendLectureRequest?lecture_name='
            .$lecture_name
            .'&lecture_id='
            .$lecture_id
            .'&lecture_time='
            .$lecture_time
            .'&course_id='
            .$course_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();



        //----------------Adding job in table----------------------

        $cronjob_data = array(
            'id' => $lecture_id,
            'type' => 'lecture',
            'name' => $lecture_name,
            'time' => $lecture_time,
            'flag' => 'earlier',
            'parent_id' => $course_id,
            'minute' => $minute,
            'hour' => $hour,
            'date' => $day,
            'month' => $month,
            'day' => '*'
        );

        //Getting reference to the controller object
        $CI = get_instance();

        //Loading model
        $CI->load->model('Cronjob_model');

        //Inserting cronjob in table
        $CI->Cronjob_model->insertJob($cronjob_data);
    }
}

//Setting cronjob for quiz notification
if ( ! function_exists('quizNotification'))
{
    //Helper function for setting cronjob at specified time
    function quizNotification($minute, $hour, $day, $month, $quiz_id, $quiz_title, $lecture_id)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendQuizRequest?quiz_id='
            .$quiz_id
            .'&quiz_title='
            .$quiz_title
            .'&lecture_id='
            .$lecture_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();

        //----------------Adding job in table----------------------

        $cronjob_data = array(
            'id' => $quiz_id,
            'type' => 'quiz',
            'name' => $quiz_title,
            'parent_id' => $lecture_id,
            'minute' => $minute,
            'hour' => $hour,
            'date' => $day,
            'month' => $month,
            'day' => '*'
        );

        //Getting reference to the controller object
        $CI = get_instance();

        //Loading model
        $CI->load->model('Cronjob_model');

        //Inserting cronjob in table
        $CI->Cronjob_model->insertJob($cronjob_data);
    }
}

//Setting cronjob for lecture notification - exact lecture time
if ( ! function_exists('currentLectureNotification'))
{
    //Helper function for setting cronjob at specified time
    function currentLectureNotification($minute, $hour, $day, $month, $course_id, $lecture_name, $lecture_time, $lecture_id)
    {
        //Setting url - path to controller/function which is to be executed
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/send_currentLectureRequest?lecture_name='
            .$lecture_name
            .'&lecture_time='
            .$lecture_time
            .'&course_id='
            .$course_id
            .'&lecture_id='
            .$lecture_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($minute)
            ->setHour($hour)
            ->setDayOfMonth($day)
            ->setMonth($month)
            ->setDayOfWeek('*')
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->addJob($job);
        $crontab->write();

        //----------------Adding job in table----------------------

        $cronjob_data = array(
            'id' => $lecture_id,
            'type' => 'lecture',
            'name' => $lecture_name,
            'time' => $lecture_time,
            'flag' => 'now',
            'parent_id' => $course_id,
            'minute' => $minute,
            'hour' => $hour,
            'date' => $day,
            'month' => $month,
            'day' => '*'
        );

        //Getting reference to controller object
        $CI = get_instance();

        // Loading model
        $CI->load->model('Cronjob_model');

        //Inserting cronjob in table
        $CI->Cronjob_model->insertJob($cronjob_data);
    }
}



//--------------------------------DELETING CRONJOB----------------------------------------------------
if ( ! function_exists('removeLecturesNotification')) {
    //Helper function for setting cronjob at specified time
    function removeLecturesNotification($lecture_id)
    {
        //Getting reference to the controller object
        $CI = get_instance();
        //Loading model
        $CI->load->model('Cronjob_model');
        //Getting cronjob for 10 minutes earlier notification
        $cron_lecture_earlier =  $CI->Cronjob_model->get_cron_lecture($lecture_id, 'earlier');

        //----------------------DELETING CRONJOB - NOTIFICATION 10 MINUTES EARLIER------------------
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendLectureRequest?lecture_name='
            .$cron_lecture_earlier->name
            .'&lecture_id='
            .$cron_lecture_earlier->id
            .'&lecture_time='
            .$cron_lecture_earlier->time
            .'&course_id='
            .$cron_lecture_earlier->parent_id
            .'"';
        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($cron_lecture_earlier->minute)
            ->setHour($cron_lecture_earlier->hour)
            ->setDayOfMonth($cron_lecture_earlier->date)
            ->setMonth($cron_lecture_earlier->month)
            ->setDayOfWeek($cron_lecture_earlier->day)
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->removeJob($job);
        $crontab->write();

        //-----------------------------DELETING CRONJOB - NOTIFICATION EXACT TIME---------------------------------------

        //Getting cronjob for exact notification time
        $cron_lecture_now =  $CI->Cronjob_model->get_cron_lecture($lecture_id, 'now');

        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/send_currentLectureRequest?lecture_name='
            .$cron_lecture_now->name
            .'&lecture_time='
            .$cron_lecture_now->time
            .'&course_id='
            .$cron_lecture_now->parent_id
            .'&lecture_id='
            .$cron_lecture_now->id
            .'"';

        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($cron_lecture_now->minute)
            ->setHour($cron_lecture_now->hour)
            ->setDayOfMonth($cron_lecture_now->date)
            ->setMonth($cron_lecture_now->month)
            ->setDayOfWeek($cron_lecture_now->day)
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->removeJob($job);
        $crontab->write();
    }
}

if ( ! function_exists('removeQuizNotification')) {
    //Helper function for setting cronjob at specified time
    function removeQuizNotification($quiz_id)
    {
        //Getting reference to the controller object
        $CI = get_instance();
        //Loading model
        $CI->load->model('Cronjob_model');
        //Getting cronjob for 10 minutes earlier notification
        $cron_quiz =  $CI->Cronjob_model->get_cron_quiz($quiz_id);

        //----------------------DELETING CRONJOB - NOTIFICATION 10 MINUTES EARLIER------------------
        $command = '/usr/bin/wget ';
        $url = '"http://107.180.106.216/second_screen_api/Notification/sendQuizRequest?quiz_id='
            .$cron_quiz->id
            .'&quiz_title='
            .$cron_quiz->name
            .'&lecture_id='
            .$cron_quiz->parent_id
            .'"';

        $commandURL = $command.$url;

        $job = new Job();
        $job
            ->setMinute($cron_quiz->minute)
            ->setHour($cron_quiz->hour)
            ->setDayOfMonth($cron_quiz->date)
            ->setMonth($cron_quiz->month)
            ->setDayOfWeek($cron_quiz->day)
            ->setCommand($commandURL);

        $crontab = new Crontab();
        $crontab->removeJob($job);
        $crontab->write();

    }
}