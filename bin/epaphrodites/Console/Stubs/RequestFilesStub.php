<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class RequestFilesStub extends SqlStub{

public static function generate($FilesNames, $requestFileName , $typeRequest)
{


$stub = 
"<?php

namespace Epaphrodites\\database\\requests\\mainRequest\\$typeRequest;

use Epaphrodites\\database\\query\\Builders;

class {$requestFileName} extends Builders
{

    //Add your request here

}";

    file_put_contents($FilesNames, $stub);
    }
}