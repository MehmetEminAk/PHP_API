<?php

namespace API;
require "../vendor/autoload.php";
use PDO;
use Dotenv;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Database {
    private static $instance;
    public ?PDO $db = null;


    private function __construct() {
        
        $host = $_ENV["DB_HOST"];
        $dbname = $_ENV["DB_DBNAME"];
        $username = $_ENV["DB_USERNAME"];

        

        $this->db = new PDO("mysql:host=$host;dbname=$dbname",$username,"");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    }
    public static function getInstance() {
        if (!isset(self::$instance)) {
            
            try {
                self::$instance = new Database();
                
                return self::$instance;

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            
        }
        return self::$instance;
    }

    public function performQuery(string $sqlString,?array $formatvalues) : array {

        $status = "error";
        $message = "";

        $this->db->beginTransaction();

        if (isset($this->db) && isset($formatvalues)) {
            $stmt = $this->db->prepare($sqlString);
            
            
            try {

                $stmt->execute($formatvalues);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $message = "Query performed successfully";
                $status = "success";
                $this->db->commit();
                
            }catch (PDOException $e) {

                $message =  $e->getMessage();
                $status = "error";
                $this->db->rollBack();

            }

            return ["status" => $status, "message" => $message, "result" => $result];
            
        } else if (isset($this->db) && !isset($formatvalues)) {

            try {
                $stmt = $this->db->prepare($sqlString);
                
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $message = "Query performed successfully";
                $status = "success";
                $this->db->commit();
            
            }catch (PDOException $e) {
                $message =  $e->getMessage();
                $this->db->rollBack();
                $status = "error";
            }
            return ["status" => $status, "message" => $message, "result" => $result];
            
        }else {
            die("DB is not initialized");
        }
        
    }
}