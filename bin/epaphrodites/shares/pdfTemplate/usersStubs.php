<?php

namespace Epaphrodites\epaphrodites\shares\pdfTemplate;

trait usersStubs 
{

    /**
     * Model to print PDF file
     */
    public function userList()
    {
        $html ='<h1>Enjoy yourself</h1>';
        return $this->generate( $html , 'user_list' );
    }
}
