<?php

namespace Epaphrodites\epaphrodites\define\config;

use Epaphrodites\epaphrodites\define\config\traits\currentStaticArray;
use Epaphrodites\epaphrodites\define\config\traits\currentSetGuardSecure;
use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;
use Epaphrodites\epaphrodites\define\config\traits\currentVariableNameSpaces;

class currentNameSpace{
    use currentFunctionNamespaces, currentVariableNameSpaces , currentStaticArray , currentSetGuardSecure;
}