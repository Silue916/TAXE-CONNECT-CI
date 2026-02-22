<?php

namespace Epaphrodites\epaphrodites\env\config;

class ResponseSequence extends ApiResponses
{

    /**
     * @param null|int $Code
     * @param null|bool $Json
     * @return void
    */
    public static function DefaultResponses(?int $Code = 404 , ?bool $Json =false ){
        
       echo $Json === false ? static::json( $Code , [] ) : static::Http( $Code , [] ) ;
    }       
    
    /**
     * @param null|int $CodeStatut
     * @param null|array $datas
     * @param null|bool $Json
     * @return void
    */    
    public static function JsonResponse( ?int $CodeStatut = 200, ?array $datas=[] , ?bool $Json =false  )
    {
        echo $Json === false ? static::json($CodeStatut , $datas) : static::Http( $CodeStatut , $datas ) ;

    }

}