<?php

namespace Epaphrodites\database\query;

use Epaphrodites\database\query\buildQuery\buildQuery;
use Epaphrodites\database\query\buildChaines\queryChaines;
use Epaphrodites\database\query\buildChaines\initQueryChaine;
use Epaphrodites\database\query\buildChaines\buildQueryChaines;
use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;

class checkQueryChaines{
  
    use queryChaines, buildQuery, buildQueryChaines, initQueryChaine, currentFunctionNamespaces;
}