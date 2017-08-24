<?php
class Server{
    private $server = null;

    private $clientList = [];

    private $clientCount = 0;

    const SERVER_TYPE_JOIN = 'join';
    const SERVER_TYPE_EXIT = 'exit';
    const SERVER_TYPE_SELF = 'self';
    const SERVER_TYPE_OTHER = 'other';

    public function __construct($server){
        $this->server = $server;
        $this->server->count = 4;//线程数量
    }

    public function onConnect($connection){
        $this->clientCount++;
        $this->clientList[$connection->id] = $connection;

        $this->sendPublicMessage($connection,[
            'type'=>self::SERVER_TYPE_JOIN,
            'count'=>$this->clientCount,
            'name'=>$connection->id
        ]);

    }
    public function onClose($connection){
        $this->clientCount--;
        unset($this->players[$connection->id]);
        unset($this->clientList[$connection->id]);

        $this->sendPublicMessage($connection,[
            'type'=>self::SERVER_TYPE_EXIT,
            'count'=>$this->clientCount,
            'name'=>$connection->id
        ]);

    }
    public function onMessage($connection,$data){
        if($data['type']=='public'){

            $msg = filter($data['msg']);

            //所有
            foreach($connection->worker->connections as $con) {
                if($connection->id!=$con->id){
                    $this->sendMessage($con,[
                        'type'=>self::SERVER_TYPE_OTHER,
                        'msg'=>$msg
                    ]);
                }
            }

            //该用户自己发送
            $this->sendMessage($connection,[
                'type'=>self::SERVER_TYPE_SELF,
                'msg'=>$msg
            ]);
        }
    }


    public function sendMessage($connection,$data){
        $connection->send(json_encode($data));
    }

    public function sendPublicMessage($connection,$data){
        foreach($connection->worker->connections as $con) {
            $this->sendMessage($con,$data);
        }
    }

}

?>
