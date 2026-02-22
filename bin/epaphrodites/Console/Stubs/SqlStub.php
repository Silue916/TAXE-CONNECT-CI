<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class SqlStub{

/**
 * @return string
*/    
public static function insertNoSql($name){

    $stub = 
    "
    /**
     * Request to insert datas
     * @param string \$value1
     * @param string \$value2
     * @return bool
    */
    public function {$name}(\$value1 , \$value2){
        
        \$document =
            [
                'value1' => \$value1,
                'value2' => \$value2,
            ];
        
        \$this->db(1)->selectCollection('collection')->insertOne(\$document);

        \$actions = 'Recent action title';
        static::initQuery()['setting']->noSqlActionsRecente(\$actions);;

        return true;

    }"; 
        
    return $stub;    

}

/**
 * @return string
 */
public static function updateNoSql($name){

    $stub = 
    "
      /**
        * Request to update 
         * @param string \$value1
         * @param string \$value2
         * @return bool
       */
      public function {$name}(\$value1 , \$value2 , \$Id){
        
        \$filter = [ '_id' => \$Id ];
    
        \$update = [
            \'\$set\'=> [ 
                'value1' => \$value1,
                'value2' => \$value2
                ]
        ];   

        \$this->db(1)->selectCollection('collection')->updateMany(\$filter, \$update);

        \$actions = 'Recent action title';
        static::initQuery()['setting']->noSqlActionsRecente(\$actions);

        return true;
    }"; 
        
    return $stub;    
}

/**
 * @return string
 */
public static function deleteNoSql($name){

    $stub = 
    "/**
     * Request to delete
     * @param int \$Id
     * @return bool
     */
    public function {$name}(\$Id){
        
        \$filter = [ '_id' => \$Id ];

        \$this->db(1)->selectCollection('collection')->deleteMany(\$filter);
        
        \$actions = 'Recent action title';
        static::initQuery()['setting']->noSqlActionsRecente(\$actions);     

        return true;
    }"; 
        
    return $stub;    
}

/**
 * @return string
 */
public static function selectNoSql($name){

$stub = 
"
  /**
    * Request to select
    * @param string \$value
    * @return array
   */
   public function {$name}(\$value){
        
    \$documents = [];

    \$result = \$this->db(1)
        ->selectCollection('recentactions')
        ->find(['usersactions' => \$value ]);

    foreach (\$result as \$document) {
        \$documents[] = \$document;
    }

    return  \$documents;
    "; 
        
    return $stub;    
}

public static function countNoSql($name){

$stub = 
"
  /**
    * Request to count
    * @return int
   */
   public function {$name}(){
        
        \$result = \$this->db(1)
            ->selectCollection('collection')
            ->countDocuments([]);

        return \$result;
    }"; 
        
return $stub;    

}

/**
 * @return string
*/    
public static function insertSql($name){

    $stub = 
    "
    /**
     * Request to insert datas
     * @param string \$value1
     * @param string \$value2
     * @param string \$value3
     * @return bool
    */
    public function {$name}(\$value1 , \$value2 , \$value3){
        
    \$this->table('table')
            ->insert(' value1 , value2 , value3 ')
            ->values(' ? , ? , ? ')
            ->param([\$value1 , \$value2 , \$value3])
            ->IQuery();
    
    \$actions = 'Recent action title';
    static::initQuery()['setting']->ActionsRecente(\$actions);

    return true;

    }"; 
        
    return $stub;    

}

/**
 * @return string
 */
public static function updateSql($name){

    $stub = 
    "
    /**
     * @param string \$value1
     * @param string \$value2
     * @param int \$Id
     * @return bool
    */
    public function {$name}(\$value1 , \$value2 , \$Id){
        
        \$this->table('table')
                ->set(['value1' , 'value2'])
                ->where('id')
                ->param([\$value1 , \$value2 ,  \$Id])
                ->UQuery();
    
        \$actions = 'Recent action title';
        static::initQuery()['setting']->ActionsRecente(\$actions);

        return true;
    }"; 
        
    return $stub;    
}

/**
 * @return string
 */
public static function deleteSql($name){

    $stub = 
    "/**
     * Request to delete
     * @param string \$value
     * @return bool
    */
    public function {$name}(\$value){
        
        \$this->table('table')
                    ->where('id')
                    ->param([\$value])
                    ->DQuery();
    
        \$actions = 'Recent action title';
        static::initQuery()['setting']->ActionsRecente(\$actions);      

        return true;
    }"; 
        
    return $stub;    
}


/**
 * @return string
 */
public static function selectSql($name){

$stub = 
"
  /**
    * Request to select
    * @param string \$value
    * @return bool
   */
   public function {$name}(\$value){
        
        \$result = \$this->table('table')
                    ->where('id')
                    ->param([\$value])
                    ->SQuery();
    
        return \$result;
    }"; 
        
    return $stub;    
}

public static function countSql($name){

$stub = 
    "
    /**
     * Request to count
     * @return int
    */
    public function {$name}(){
        
        \$result = \$this->table('table')
                    ->SQuery('count(id) as nbre');
          
        return \$result[0]['nbre'];
    }"; 
        
return $stub;    

}

protected static function SwicthRequestContent($type,$name){

    
    switch ($type) {

        case 'insert':
                return self::insertSql($name);
                break;

            case 'update':
                return self::updateSql($name);
                break;

            case 'delete':
                return self::deleteSql($name);
                break; 
            
            case 'count':
                return self::countSql($name);
                break;                   
            
            default:
                return self::selectSql($name);
                break;
        }
    }

    protected static function SwicthNoSqlRequestContent($type,$name){

        switch ($type) {
    
            case 'insert':
                    return self::insertNoSql($name);
                    break;
    
                case 'update':
                    return self::updateNoSql($name);
                    break;
    
                case 'delete':
                    return self::deleteNoSql($name);
                    break; 
                
                case 'count':
                    return self::countNoSql($name);
                    break;                   
                
                default:
                    return self::selectNoSql($name);
                    break;
            }
        }    


}