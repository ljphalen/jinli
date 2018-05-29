<?php

require './Common.php';
for ($i = 0; $i < 10; $i++) {
    Common::execute('Async_Task_Adapter_SubjectStatus', 'update', array($i));
}
