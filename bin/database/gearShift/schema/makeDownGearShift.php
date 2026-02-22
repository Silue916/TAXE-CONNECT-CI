<?php 

namespace Epaphrodites\database\gearShift\schema;

trait makeDownGearShift{

    /**
     * Drop Column ip
     * create 25/01/2024 23:07:14
     */
    public function dropUsersAccountColumn()
    {
        return $this->dropTable('usersaccount', function ($table) {
            $table->dropColumn('ip');
            $table->db(1);
        });
    }

    /**
     * Drop communes table.
     */
    public function dropCommunesTable()
    {
        return $this->dropTable('communes', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop TaxeConnect users table.
     */
    public function dropTaxeConnectUsersTable()
    {
        return $this->dropTable('users', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop mairies table.
     */
    public function dropMairiesTable()
    {
        return $this->dropTable('mairies', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop agents table.
     */
    public function dropAgentsTable()
    {
        return $this->dropTable('agents', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop merchants table.
     */
    public function dropMerchantsTable()
    {
        return $this->dropTable('merchants', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop contributors table.
     */
    public function dropContributorsTable()
    {
        return $this->dropTable('contributors', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop tax_notices table.
     */
    public function dropTaxNoticesTable()
    {
        return $this->dropTable('tax_notices', function ($table) {
            $table->db(1);
        });
    }

    /**
     * Drop tax_payments table.
     */
    public function dropTaxPaymentsTable()
    {
        return $this->dropTable('tax_payments', function ($table) {
            $table->db(1);
        });
    }
}
