<?php

namespace Epaphrodites\database\query;

use Epaphrodites\database\config\Contracts\Builders as ContractsBuilders;

class Builders extends checkQueryChaines implements ContractsBuilders
{

  /**
   * @return mixed
   */
  public static function firstSeederGeneration()
  {

    return static::initConfig()['seeder']->SeederGenerated();
  }
}
