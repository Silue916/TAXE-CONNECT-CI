<?php 

namespace Epaphrodites\database\gearShift\schema;

trait makeUpGearShift{

    /**
     * Create table useraccount
     * create 25/01/2024 23:07:14
     */
    public function createUsersAccountTable()
    {
        return $this->createTable('usersaccount', function ($table) {
                $table->addColumn('_id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('login', 'TEXT' , ['NOT NULL']);
                $table->addColumn('password', 'TEXT' , ['NOT NULL']);
                $table->addColumn('namesurname', 'TEXT' , ['DEFAULT NULL']);
                $table->addColumn('contact', 'TEXT' , ['DEFAULT NULL']);
                $table->addColumn('email', 'TEXT' , ['DEFAULT NULL']);
                $table->addColumn('ip', 'TEXT' , ['DEFAULT NULL']);
                $table->addColumn('usersgroup', 'INTEGER' , ['NOT NULL', 'DEFAULT 1']);
                $table->addColumn('state', 'INTEGER' , ['NOT NULL', 'DEFAULT 1']);
                $table->addIndex('login');
                $table->db(1);
        });
    }

    /**
     * Create communes table for TaxeConnect governance.
     */
    public function createCommunesTable()
    {
        return $this->createTable('communes', function ($table) {
                $table->addColumn('id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('code', 'TEXT', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('name', 'TEXT', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('department', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('region', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('code');
                $table->addIndex('name');
                $table->db(1);
        });
    }

    /**
     * Create TaxeConnect users table (role-based governance).
     */
    public function createTaxeConnectUsersTable()
    {
        return $this->createTable('users', function ($table) {
                $table->addColumn('id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('login', 'TEXT', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('password_hash', 'TEXT', ['NOT NULL']);
                $table->addColumn('full_name', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('email', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('phone', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('role', 'TEXT', ['NOT NULL']);
                $table->addColumn('status', 'TEXT', ['NOT NULL', "DEFAULT 'ACTIVE'"]);
                $table->addColumn('commune_id', 'INTEGER', ['DEFAULT NULL']);
                $table->addColumn('created_by', 'INTEGER', ['DEFAULT NULL']);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('updated_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('login');
                $table->addIndex('role');
                $table->addIndex('commune_id');
                $table->db(1);
        });
    }

    /**
     * Create mairies assignment table.
     */
    public function createMairiesTable()
    {
        return $this->createTable('mairies', function ($table) {
                $table->addColumn('id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('commune_id', 'INTEGER', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('mairie_admin_user_id', 'INTEGER', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('created_by_super_admin', 'INTEGER', ['NOT NULL']);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('commune_id');
                $table->addIndex('mairie_admin_user_id');
                $table->db(1);
        });
    }

    /**
     * Create agents assignment table.
     */
    public function createAgentsTable()
    {
        return $this->createTable('agents', function ($table) {
                $table->addColumn('id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('commune_id', 'INTEGER', ['NOT NULL']);
                $table->addColumn('agent_user_id', 'INTEGER', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('created_by_mairie_admin', 'INTEGER', ['NOT NULL']);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('commune_id');
                $table->addIndex('agent_user_id');
                $table->db(1);
        });
    }

    /**
     * Create merchants table for contributor user binding.
     */
    public function createMerchantsTable()
    {
        return $this->createTable('merchants', function ($table) {
                $table->addColumn('id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('merchant_ref', 'TEXT', ['NOT NULL', 'UNIQUE']);
                $table->addColumn('user_id', 'INTEGER', ['DEFAULT NULL', 'UNIQUE']);
                $table->addColumn('commune_id', 'INTEGER', ['NOT NULL']);
                $table->addColumn('created_by_agent_user_id', 'INTEGER', ['NOT NULL']);
                $table->addColumn('business_name', 'TEXT', ['NOT NULL']);
                $table->addColumn('owner_name', 'TEXT', ['NOT NULL']);
                $table->addColumn('phone', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('email', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('activity', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('address', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('status', 'TEXT', ['NOT NULL', "DEFAULT 'ACTIVE'"]);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('updated_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('merchant_ref');
                $table->addIndex('commune_id');
                $table->db(1);
        });
    }

    /**
     * Create contributors table for TaxeConnect CI MVP
     */
    public function createContributorsTable()
    {
        return $this->createTable('contributors', function ($table) {
                $table->addColumn('merchant_id', 'TEXT', ['PRIMARY KEY']);
                $table->addColumn('name', 'TEXT', ['NOT NULL']);
                $table->addColumn('business', 'TEXT', ['NOT NULL']);
                $table->addColumn('commune', 'TEXT', ['NOT NULL']);
                $table->addColumn('contact', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('status', 'TEXT', ['NOT NULL', "DEFAULT 'Actif'"]);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('commune');
                $table->addIndex('status');
                $table->db(1);
        });
    }

    /**
     * Create tax notices table for TaxeConnect CI MVP
     */
    public function createTaxNoticesTable()
    {
        return $this->createTable('tax_notices', function ($table) {
                $table->addColumn('_id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('notice_ref', 'TEXT', ['NOT NULL']);
                $table->addColumn('merchant_id', 'TEXT', ['NOT NULL']);
                $table->addColumn('fiscal_period', 'TEXT', ['NOT NULL']);
                $table->addColumn('amount_due', 'REAL', ['NOT NULL', 'DEFAULT 0']);
                $table->addColumn('status', 'TEXT', ['NOT NULL', "DEFAULT 'unpaid'"]);
                $table->addColumn('due_date', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('created_at', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('notice_ref');
                $table->addIndex('merchant_id');
                $table->addIndex('status');
                $table->db(1);
        });
    }

    /**
     * Create tax payments table for TaxeConnect CI MVP
     */
    public function createTaxPaymentsTable()
    {
        return $this->createTable('tax_payments', function ($table) {
                $table->addColumn('_id', 'INTEGER', ['PRIMARY KEY', 'AUTOINCREMENT']);
                $table->addColumn('receipt_ref', 'TEXT', ['NOT NULL']);
                $table->addColumn('notice_ref', 'TEXT', ['NOT NULL']);
                $table->addColumn('merchant_id', 'TEXT', ['NOT NULL']);
                $table->addColumn('amount', 'REAL', ['NOT NULL', 'DEFAULT 0']);
                $table->addColumn('channel', 'TEXT', ['NOT NULL', "DEFAULT 'mobile_money'"]);
                $table->addColumn('status', 'TEXT', ['NOT NULL', "DEFAULT 'success'"]);
                $table->addColumn('paid_at', 'TEXT', ['DEFAULT NULL']);
                $table->addColumn('qr_token', 'TEXT', ['DEFAULT NULL']);
                $table->addIndex('receipt_ref');
                $table->addIndex('notice_ref');
                $table->addIndex('merchant_id');
                $table->db(1);
        });
    }
}
