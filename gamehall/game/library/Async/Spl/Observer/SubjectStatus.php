<?php

class Async_Spl_Observer_SubjectStatus extends Async_Spl_Observer {

    public $subjectList = array(Async_Spl_Center::SUBJECT_STATUS);
    
    public function doUpdate(SplSubject $subject, Async_Task_Message_Spl $spl) {
    }
    
    public function getSubjectList() {
        return $this->subjectList;
    }
    
}

