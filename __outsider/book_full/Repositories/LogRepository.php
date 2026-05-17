<?php
namespace Outsider\Book\Repositories;

use PDO;

class LogRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function log(string $type,int $id,string $action,?int $tutor=null,?array $data=null): void {
        $this->db->prepare("INSERT INTO tl_bk_logs (entity_type,entity_id,action,tutor_id,data) VALUES (?,?,?,?,?)")
            ->execute([$type,$id,$action,$tutor,$data?json_encode($data,JSON_UNESCAPED_UNICODE):null]);
    }
}
