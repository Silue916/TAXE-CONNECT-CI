<?php

namespace Epaphrodites\database\requests\mainRequest\select;

use Epaphrodites\database\requests\typeRequest\sqlRequest\select\general as GeneralGeneral;

final class general extends GeneralGeneral
{

  /**
   * Seed TaxeConnect MVP demo data when available on SQL drivers.
   */
  public function ensureTaxeConnectMvpData(): void
  {
    match (_FIRST_DRIVER_) {
      'mongodb' => null,
      'redis' => null,
      default => parent::ensureTaxeConnectMvpData(),
    };
  }

  /**
   * Get TaxeConnect dashboard KPIs and recent payments.
   *
   * @return array
   */
  public function taxeConnectSummary(): array
  {
    return match (_FIRST_DRIVER_) {

      'mongodb' => [
        'total_contributors' => 0,
        'active_contributors' => 0,
        'pending_contributors' => 0,
        'total_notices' => 0,
        'unpaid_notices' => 0,
        'overdue_notices' => 0,
        'assessed_amount' => 0,
        'paid_amount' => 0,
        'outstanding_amount' => 0,
        'collection_rate' => 0,
        'recent_payments' => [],
      ],
      'redis' => [
        'total_contributors' => 0,
        'active_contributors' => 0,
        'pending_contributors' => 0,
        'total_notices' => 0,
        'unpaid_notices' => 0,
        'overdue_notices' => 0,
        'assessed_amount' => 0,
        'paid_amount' => 0,
        'outstanding_amount' => 0,
        'collection_rate' => 0,
        'recent_payments' => [],
      ],

      default => parent::taxeConnectSummary(),
    };
  }

  /**
   * Get all users history actions
   * @return array
   */
  public function usersHistory(): array
  {
    return match (_FIRST_DRIVER_) {

      'mongodb' => $this->mongodbUsersHistoryActions(),
      'redis' => $this->redisUsersHistoryActions(),
      'oracle' => $this->oracleHistoryRequest(),
      'sqlserver' => $this->sqlSeverHistoryRequest(),

      default => $this->defaultSqlHistoryRequest(),
    };
  }
}
