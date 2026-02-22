<?php

namespace Epaphrodites\epaphrodites\env;

use Epaphrodites\epaphrodites\env\pyEnv\pyEnv;
use Epaphrodites\epaphrodites\env\phpEnv\phpEnv;
use Epaphrodites\epaphrodites\env\config\GeneralConfig;
use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;

class env extends GeneralConfig
{

    use currentFunctionNamespaces, phpEnv, pyEnv;
}
