<?php

namespace Epaphrodites\epaphrodites\env\toml;

use Epaphrodites\epaphrodites\env\toml\requests\updToml;
use Epaphrodites\epaphrodites\env\toml\requests\delToml;
use Epaphrodites\epaphrodites\env\toml\requests\getToml;
use Epaphrodites\epaphrodites\env\toml\saveLoad\loadToml;
use Epaphrodites\epaphrodites\env\toml\requests\addToToml;
use Epaphrodites\epaphrodites\env\toml\requests\iterrateDatas;
use Epaphrodites\epaphrodites\env\toml\saveLoad\saveTomlDatas;

final class Toml{

   use loadToml, saveTomlDatas, iterrateDatas, addToToml, getToml, delToml, updToml;
}