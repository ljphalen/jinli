<?php
class Swoole_Socket_Adapter_Encode implements Swoole_Socket_Encode {
    
    public function encode($response) {
        if(! $response instanceof Swoole_Socket_Message_Response) {
            return "";
        }
        $data = array(
            'op' => $response->getOp(),
            'data' => $response->getData(),
        );
        $message = json_encode($data);
        return pack("N" , strlen($message) ). $message;
    }
    
}

