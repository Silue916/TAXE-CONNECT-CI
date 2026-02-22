<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class newCommandSubs
{

    public static function Generate($commadFile , $modelFile , $settingFile ,$command , $fileName)
    {

        file_put_contents($modelFile, static::modelStubs($fileName));
        file_put_contents($settingFile, static::settingArguments($fileName));
        file_put_contents($commadFile, static::commandStubs($command , $fileName));
    }  
    
    private static function commandStubs($command , $fileName){
        

        return 
        "<?php

namespace Epaphrodites\\epaphrodites\\Console\commands;

use Epaphrodites\\epaphrodites\\Console\\Models\\model{$fileName};

class command{$fileName} extends model{$fileName}{

    protected static \$defaultName = '{$command}';
}
        ";

    }

    private static function modelStubs($fileName){
        
        return 
        "<?php

namespace Epaphrodites\\epaphrodites\\Console\\Models;
        
use Symfony\\Component\\Console\Input\\InputInterface;
use Symfony\\Component\\Console\\Output\\OutputInterface;
use Epaphrodites\\epaphrodites\Console\Setting\setting{$fileName};
        
class model{$fileName} extends setting{$fileName}{
        
        
    /**
    * @param \Symfony\Component\Console\Input\InputInterface \$input
    * @param \Symfony\Component\Console\Output\OutputInterface \$output
    */
    protected function execute( InputInterface \$input, OutputInterface \$output)
    {
        # Get console arguments
        \$name = \$input->getArgument('name');

        // Your actions
    }
}
        ";

    }  
    
    private static function settingArguments($fileName){
        return 
        "<?php

namespace Epaphrodites\\epaphrodites\\Console\Setting;
        
use Symfony\\Component\\Console\Command\\Command;
use Symfony\\Component\\Console\Input\\InputArgument;
        
class setting{$fileName} extends Command
{
        
    protected function configure()
    {
        \$this->setDescription('Add your command description')
                ->setHelp('This is help.')
                ->addArgument('name', InputArgument::REQUIRED, 'Your argument name');
    }
}        
        ";
    }    
}