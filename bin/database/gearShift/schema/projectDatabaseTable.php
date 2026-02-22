<?php 

namespace Epaphrodites\database\gearShift\schema;

trait projectDatabaseTable{

    /**
    * copie database table
    * copie 15/05/2024 06:09:12
    */
    public function copyDatabaseTable()
    {

        return $this->copyTables( 1, function ($db):void {

                $db->dbTarget(2);
                $db->table(
                    [
                    'usersaccount'
                ]);
        });
    }     

}