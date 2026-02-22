<?php

namespace Epaphrodites\epaphrodites\env\json;

use Epaphrodites\epaphrodites\env\json\requests\delJson;
use Epaphrodites\epaphrodites\env\json\requests\getJson;
use Epaphrodites\epaphrodites\env\json\requests\updJson;
use Epaphrodites\epaphrodites\env\json\saveLoad\loadJson;
use Epaphrodites\epaphrodites\env\json\requests\addToJson;
use Epaphrodites\epaphrodites\env\json\saveLoad\saveJsonDatas;

final class Json{

    use loadJson, saveJsonDatas,addToJson, delJson, updJson, getJson;
}