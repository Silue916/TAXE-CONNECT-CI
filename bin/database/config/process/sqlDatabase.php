<?php

declare(strict_types=1);

namespace Epaphrodites\database\config\process;

use PDO;
use Database\Epaphrodites\config\SwitchDatabase;
use Epaphrodites\database\config\Contracts\DatabaseRequest;

class sqlDatabase extends SwitchDatabase implements DatabaseRequest
{

    /**
     * Disconnection from the database
     * 
     * @param int $bd
     * @return int|null
     */
    private function closeConnection(int $db, bool $state = false):void
    {
         $this->dbConnect($db, $state);
    }

    /**
     * SQL request to select data
     * 
     * @param string|null $sqlChaine The SQL query
     * @param array|null $datas The data for query parameters
     * @param bool|null $param Flag to indicate if query parameters are set
     * @param bool|false $closeConnection Flag to indicate if connection should be closed after execution
     * @param int|1 $bd The database reference
     * @param bool|false $except Don't display error
     * @param bool|false $terminal If request is in terminal
     * @return array|null The fetched data
     */
    public function select(
        string $sqlChaine,
        array $datas = [],
        bool $param = false,
        bool $closeConnection = false,
        int $db = 1,
        bool $except = false,
        bool $terminal = false
    ): ?array{
       
        try {
            $connection = $this->dbConnect($db);
            $request = $connection->prepare($sqlChaine);

            if ($param) {
                foreach ($datas as $k => $v) {
                    $request->bindValue(is_int($k) ? $k + 1 : $k, $v, PDO::PARAM_STR);
                }
            }

            $request->execute();

            $result = $request->fetchAll();

            if ($closeConnection) {
                $this->closeConnection($db, true);
            }

            return $result;

        } catch (\Throwable $e) {

            // Detailed error handling in no-production environments (without terminal)
            if (!_PRODUCTION_&&!$except&&!$terminal) {
                $errorType = get_class($e);
                $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES);
                $errorFile = $e->getFile();
                $errorLine = $e->getLine();

                // Output styled error message with details
                $this->displayErrors( $errorType, $errorMessage, $errorFile, $errorLine);
            }

            // Error handling in no-production environments (with terminal)
            if (!_PRODUCTION_&&!$except&&$terminal){
                error_log("Database error: " . $e->getMessage());
            }

            return [];
        }       
    }

    /**
     * SQL request execution
     * 
     * @param string $sqlChaine The SQL query
     * @param array|[] $datas The data for query parameters
     * @param bool|false $param Flag to indicate if query parameters are set
     * @param bool|false $closeConnection Flag to indicate if connection should be closed after execution
     * @param int|1 $bd The database reference
     * @param bool|false $except Don't display error
     * @param bool|false $terminal If request is in terminal
     * @return bool|null True if the execution is successful, otherwise false
     */
    public function runRequest(
        string $sqlChaine, 
        array $datas = [], 
        bool $param = false, 
        bool $closeConnection = false, 
        int $db = 1,
        bool $except = false,
        bool $terminal = false
    ):bool{
        $connection = $this->dbConnect($db);
        $transactionStarted = false;
        
        try {
            if (!$connection->inTransaction()) {
                $transactionStarted = $connection->beginTransaction();
            }

            $request = $connection->prepare($sqlChaine);

            if ($param) {
                foreach ($datas as $k => $v) {
                    $request->bindValue(is_int($k) ? $k + 1 : $k, $v, PDO::PARAM_STR);
                }
            }

            $result = $request->execute();

            if ($closeConnection) {
                $this->closeConnection($db, true);
            }

            if ($transactionStarted && $connection->inTransaction()) {
                $connection->commit();
            }

            return $result;
            
        } catch (\Throwable $e) {

             // Rollback transaction if an error occurs
            if ($transactionStarted && $connection->inTransaction()) {
                $connection->rollBack();
            }

            // Error handling in no-production environments (without terminal)
            if (!_PRODUCTION_&&!$except&&!$terminal) {
                $errorType = get_class($e);
                $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES);
                $errorFile = $e->getFile();
                $errorLine = $e->getLine();

                // Output styled error message with details
                $this->displayErrors( $errorType, $errorMessage, $errorFile, $errorLine);
            }

            // Error handling in no-production environments (with terminal)
            if (!_PRODUCTION_&&!$except&&$terminal){
               $result = error_log("Database error: " . $e->getMessage());
            }

            return false;
        }
    }

    /**
     * Display styled error message
     * 
     * @param string $errorType
     * @param string $errorMessage
     * @param string $errorFile
     * @param int $errorLine
     * @return void
     */    
    private function displayErrors( 
        string $errorType, 
        string $errorMessage, 
        string $errorFile, 
        int $errorLine
    ):void{

        echo <<<HTML
        <div style='background-color: #ffe6e6; border: 1px solid #ff4d4d; padding: 20px; margin: 70px;font-family: monospace;color: #990000;border-radius: 8px;box-shadow: 2px 2px 6px rgba(0,0,0,0.1);'>
        <strong>Database Exception:</strong> {$errorType}<br>
        <strong>Message:</strong> {$errorMessage}<br>
        <strong>File:</strong> {$errorFile}<br>
        <strong>Line:</strong> {$errorLine}<br><br>
        </div>
        HTML;        
    }
}
