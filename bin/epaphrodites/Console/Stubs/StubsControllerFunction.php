<?php

 namespace Epaphrodites\epaphrodites\Console\Stubs;

 class StubsControllerFunction{
    
    /**
     * @param string $controllersName
     * @param string $name
     * @return void
     */
    public static function Generate(string $controllersName, string $name , bool $model = false ):void
    {
       
        $stubs = $model===true ? static::apiStubs($name) : static::stubs($name);

        $FilesContent = file_get_contents($controllersName);
   
        $lastBracketPosition = strrpos($FilesContent, '}');

        if ($lastBracketPosition !== false) {
            $FilesContent = substr($FilesContent, 0, $lastBracketPosition);
        }  

        if ($lastBracketPosition !== false) {

            $FilesContent = substr_replace($FilesContent, $stubs."\n}", $lastBracketPosition);
            file_put_contents($controllersName, $FilesContent, LOCK_EX);
        }
    } 
    
    /**
     * @param string $initPage
     * @return string
     */
    public static function stubs(string $initPage):string
    {

        $functionName = static::transformToFunction($initPage);

        $stub = 
        "
    /**
    * start view function
    * 
    * @param string \$html
    * @return void
    */
     public final function {$functionName}(string \$html): void{
    
        \$this->views( \$html, [], false );
    }";  
        
        return $stub;
    }

   /**
     * @param string $initPage
     * @return string
     */
    public static function apiStubs(string $initPage):string
    {

        $functionName = static::transformToFunction($initPage);

        $stub = 
        "
    /**
    * make your api
    */
     public final function {$functionName}(){
    
        \$Result = [];
        \$code = 400;

        if (static::isValidApiMethod()) {

            \$code = 200;
            \$Result = ['this' , 'is' , 'api' , 'result' , 'test']; 
        }

        return \$this->Response->JsonResponse(\$code, \$Result);
    }";  
        
        return $stub;
    }    

    /**
     *  @param string $initPage
     * @return string
     */
    private static function transformToFunction($initPage): string
    {

        $parts = explode('_', $initPage);

        $camelCaseParts = array_map(function ($part) {
            return ucfirst($part);
        }, $parts);

        $camelCaseString = lcfirst(implode('', $camelCaseParts));

        $contract = explode('/', $camelCaseString);

        $parts = count($contract) > 1 ? $contract[1] : $contract[0];

        return $parts;
    }    
 }