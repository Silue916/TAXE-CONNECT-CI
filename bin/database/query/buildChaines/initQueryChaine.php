<?php

namespace Epaphrodites\database\query\buildChaines;

trait initQueryChaine{

    /**
     * @return void
     */
    public function initQueryChaine():void{
       
        $this->table = NULL;

        $this->join = NULL;

        $this->joinFull = NULL;

        $this->joinLeft = NULL;

        $this->joinRight = NULL;

        $this->chaine = NULL;

        $this->like = NULL;

        $this->where = NULL;

        $this->and = NULL;

        $this->andJSON = NULL;

        $this->group = NULL;

        $this->order = NULL;

        $this->insert = NULL;

        $this->values = NULL;

        $this->rset = NULL;

        $this->set = NULL;

        $this->replace = NULL;

        $this->having = NULL;

        $this->set_i = NULL;

        $this->limit = NULL;

        $this->limit_i = NULL;

        $this->rlimit = NULL;

        $this->offset = NULL;

        $this->match = NULL;

        $this->sumCase = NULL;

        $this->or = NULL;

        $this->set_date = NULL;

        $this->between_date = NULL;
    }
}