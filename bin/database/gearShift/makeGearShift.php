<?php 

namespace Epaphrodites\database\gearShift;

use Epaphrodites\database\gearShift\schema\makeUpGearShift as Up;
use Epaphrodites\database\gearShift\schema\makeDownGearShift as Down;
use Epaphrodites\database\gearShift\schema\projectDatabaseTable as Copy;
use Epaphrodites\database\query\buildQuery\buildGearShift as Build;

final class makeGearShift extends Build{

    use Down, Up, Copy;

    /**
     * All up migration
     * @return array
    */
    public final function up():array{
        return [
            $this->createUsersAccountTable(),
            $this->createCommunesTable(),
            $this->createTaxeConnectUsersTable(),
            $this->createMairiesTable(),
            $this->createAgentsTable(),
            $this->createMerchantsTable(),
            $this->createContributorsTable(),
            $this->createTaxNoticesTable(),
            $this->createTaxPaymentsTable()
        ];
    }

    /**
     * All down migration
     * @return array
    */
    public final function down():array{
		return [
            $this->dropUsersAccountColumn(),
            $this->dropCommunesTable(),
            $this->dropTaxeConnectUsersTable(),
            $this->dropMairiesTable(),
            $this->dropAgentsTable(),
            $this->dropMerchantsTable(),
            $this->dropContributorsTable(),
            $this->dropTaxNoticesTable(),
            $this->dropTaxPaymentsTable()
        ];
    }  
    
    /**
     * All copy database table
     * 
     * @return array
     */
    public function copy():array{

        return [
            $this->copyDatabaseTable()
        ];
    }    
}
