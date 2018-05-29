<?php
class Swoole_Socket_Adapter_Decode implements Swoole_Socket_Decode {

    /**
     * {op:"", data:""}
     */
    public function decode($data) {
        $pack = unpack("N", $data);
        $length = $pack[1];
        $msg = substr($data, - $length);
        $clientData = json_decode($msg, true);
        $op = $clientData['op'];
        $data = $clientData['data'];
        $taskId = Swoole_Socket_Util::guid();
        $request = new Swoole_Socket_Message_Request($op, $data, $taskId);
        return $request;
    }
    
}
