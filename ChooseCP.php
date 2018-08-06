<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('RedirectRootInc.php');
include'ConfigInc.php';
include 'Warehouse.php';

if ($_REQUEST['table_name'] != '' && $_REQUEST['table_name'] == 'course_periods') {

    $sql = "SELECT COURSE_PERIOD_ID AS CHECKBOX,COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_ID='$_REQUEST[id]'AND (marking_period_id IS NOT NULL AND marking_period_id IN(" . GetAllMP(GetMPTable(GetMP(UserMP(), 'TABLE')), UserMP()) . ") OR marking_period_id IS NULL AND '" . date('Y-m-d') . "' <= end_date) ORDER BY TITLE";
    $QI = DBQuery($sql);

    $coursePeriods_RET = DBGet($QI);
    $html = 'cp_modal_cp||';

    $html.= '<h6>' . count($coursePeriods_RET) . ((count($coursePeriods_RET) == 1) ? ' Period was' : ' Periods were') . ' found.</h6>';
    $html.= '<FORM name="courses" method="post" action="Modules.php?modname=scheduling/Schedule.php?modfunc=cp_insert">';
    $html.= '<table class="table table-bordered"><thead><tr class="alpha-grey"><th>&nbsp;</th><th>Course Periods</th><th>Available Seats</th></tr></thead>';
    $html.= '<tbody>';
    foreach ($coursePeriods_RET as $val) {
        $html.= '<tr><td><input type="checkbox" id="course_' . $val['COURSE_PERIOD_ID'] . '" name="course_periods[' . $val['COURSE_PERIOD_ID'] . ']" value=' . $val['COURSE_PERIOD_ID'] . ' onchange="verify_schedule(this);"></td><td><a href=javascript:void(0); onclick="grab_coursePeriod(' . $val['COURSE_PERIOD_ID'] . ',\'course_periods\',\'subject_id\')">' . $val['TITLE'] . '</a></td><td>' . $val['AVAILABLE_SEATS'] . '</td></tr>';
//           $html.= '<tr><td><input type="checkbox" id="course_'.$val['COURSE_PERIOD_ID'].'" name="course_periods['.$val['COURSE_PERIOD_ID'].']" value='.$val['COURSE_PERIOD_ID'].'></td><td><a href=javascript:void(0); onclick="scheduleCP('.$val['COURSE_PERIOD_ID'].')">'.$val['TITLE'].'</a></td><td>'.$val['AVAILABLE_SEATS'].'</td></tr>';
    }
    $html.='</tbody>';
    $html.='</table>';
    $html.= '<table id="selected_course1" style="display: none;"><tr><td></td></tr></table>';
    if (count($coursePeriods_RET)) {

        $html.='<div class="text-center p-t-20">' . SubmitButtonModal('Done', 'done', 'class="btn btn-primary" ') . '&nbsp;&nbsp;' . SubmitButtonModal('Close', 'exit', 'class="btn btn-white" data-dismiss="modal"') . '</div>';
    }
    $html.='</FORM>';
}

if ($_REQUEST['table_name'] != '' && $_REQUEST['table_name'] == 'courses') {

    $sql = "SELECT COURSE_ID,c.TITLE, CONCAT_WS(' - ',c.title,sg.title) AS GRADE_COURSE FROM courses c LEFT JOIN school_gradelevels sg ON c.grade_level=sg.id WHERE SUBJECT_ID='$_REQUEST[id]' ORDER BY c.TITLE";
    $QI = DBQuery($sql);
    $courses_RET = DBGet($QI);
    $html = 'course_modal_cp||';
    $html.= '<h6>' . count($courses_RET) . ((count($courses_RET) == 1) ? ' Course was' : ' Courses were') . ' found.</h6>';
    $html.= '<table  class="table table-bordered"><thead><tr class="alpha-grey"><th>Course</th></tr></thead>';
    $html.= '<tbody>';
    foreach ($courses_RET as $val) {

        $html.= '<tr><td><a href=javascript:void(0); onclick="grab_coursePeriod(' . $val['COURSE_ID'] . ',\'course_periods\',\'subject_id\')">' . $val['TITLE'] . '</a></td></tr>';
    }
    $html.= '</tbody>';
    $html.= '</table>';
}

echo $html;
?>
