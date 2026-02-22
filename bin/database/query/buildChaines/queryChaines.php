<?php

namespace Epaphrodites\database\query\buildChaines;

trait queryChaines
{

    private $table;
    private $except;
    private $key;
    private $lastIndex;
    private $rdb;
    private $id;
    private $all;
    private $chaine;
    private $count;
    private $index;
    private $where;
    private $search;
    private $set_date;
    private $like;
    private $match;
    private $between;
    private $between_date;
    private $and;
    private $andJSON;
    private $order;
    private $join;
    private $sumCase;
    private $joinFull;
    private $joinLeft;
    private $joinRight;
    private $limit;
    private $rlimit;
    private $rset;
    private $limit_i;
    private $group;
    private $insert;
    private $values;
    private $set;
    private $set_i;
    private $replace;
    private $having;
    private $or;
    private $is;
    private $offset;
    private ?int $db = 1;
    private ?array $param = [];
    private ?bool $close = false;
    private ?array $multiChaine = []; 

    /**
     * Sets the database to use
     *
     * @param null|int $db
     * @return mixed
     */
    public function sdb(
        int $db = 1
    ): mixed{
        $this->db = $db;
        return $this;
    }  

    /**
     * Enables or disables connection closure
     *
     * @return mixed
     */
    public function close():mixed{
        $this->close = true;

        return $this;
    }

    /**
     * No display error
     * 
     * @return mixed
     */
    public function except():mixed{
        $this->except = true;
        
        return $this;
    }    

    /**
     * Sets parameters for the query
     *
     * @param array|null $param
     * @return self
     */
    public function param(
        array $param = []
    ): self{
        $this->param = NULL;

        $this->param = $param;

        return $this;
    }

    /**
     * Sets parameters for the query
     *
     * @return self
     */
    public function count(): self
    {
        $this->count = NULL;

        $this->count = 'COUNT';

        return $this;
    }  
    
    /**
     * Sets parameters for the query
     *
     * @return self
     */
    public function all(): self
    {
        $this->all = NULL;

        $this->all = 'all';

        return $this;
    }    
    
    /**
     * Sets parameters for the query
     *
     * @return self
     */
    public function lastIndex(): self
    {
        $this->lastIndex = NULL;

        $this->lastIndex = 'lastIndex';

        return $this;
    }    

    /**
     * Sets parameters for the query
     *
     * @param array|null $index
     * @return self
     */
    public function index(
        string $index
    ): self{
        $this->index = NULL;

        $this->index = $index;

        return $this;
    }        

    /**
     * Sets parameters for the query
     *
     * @param array|null $search
     * @return self
     */
    public function search(
        array $search = []
    ): self{
        $this->search = NULL;

        $this->search = $search;

        return $this;
    }    

    /**
     * Sets the query string or string
     *
     * @param string $id The query string or chain
     * @return self
     */
    public function id(
        string $id
    ): self{
        $this->id = NULL;

        $this->id = "$id";

        return $this;
    }

    /**
     * Sets the query string or string
     *
     * @param string $string The query string or chain
     * @return self
     */
    public function chaine(
        string $string
    ): self{
        $this->chaine = NULL;

        $this->chaine = "$string";

        return $this;
    }

    /**
     * Sets parameters for the query
     *
     * @param array|null $multiChaine
     * @return self
     */
    public function multiChaine(
        array $multiChaine = []
    ): self{
        $this->multiChaine = NULL;

        $this->multiChaine = $multiChaine;

        return $this;
    }     

    /**
     * Sets the query string or string
     *
     * @param string $key The query string or chain
     * @return self
     */
    public function key(
        string $key
    ): self{
        $this->key = NULL;

        $this->key = "$key";

        return $this;
    }    

    /**
     * Sets the table name for the query
     *
     * @param string $table The table name
     * @return self
     */
    public function table(
        string $table
    ): self{
        $this->table = NULL;

        $this->table = "$table";

        return $this;
    }

    /**
     * Sets the insert statement for the query
     *
     * @param string $insert The insert statement
     * @return self
     */
    public function insert(
        string $insert
    ): self{
        $this->insert = NULL;

        $this->insert = "$insert";

        return $this;
    }

    /**
     * Sets the values for the query
     *
     * @param string $values The values to be inserted
     * @return self
     */
    public function values(
        string $values
    ): self{
        $this->values = NULL;

        $this->values = "$values";

        return $this;
    }

    /**
     * Sets the WHERE condition for the query
     *
     * @param string $where The WHERE condition
     * @param string|null $type The type of comparison (e.g., '=', '>', '<', etc.)
     * @param string|null $format is for oracle date format 
     * @return self
     */
    public function where(
        string $where, 
        string|null $type = null, 
        string|null $format = null
    ): self{
        $this->where = NULL;

        $this->where = $type !== null 
                    ? ($format !== null 
                        ? "$where $type TO_DATE( ?, $format )" 
                        : "$where $type ?") 
                    : "$where = ?";
    
        return $this;
    }

    /**
     * Sets the LIKE condition for the query
     *
     * @param string $like The LIKE condition
     * @return self
     */
    public function like(
        string $like
    ): self{
        $this->like = NULL;

        $this->like = "$like";

        return $this;
    }

    /**
     * Sets the MATCH condition for the query
     *
     * @param string $match The MATCH condition
     * @return self
     */
    public function match(
        string $match
    ): self{
        $this->match = NULL;

        $this->match = "$match";

        return $this;
    }

    /**between_date
     * Sets the BETWEEN condition for the query
     *
     * @param string $between The BETWEEN condition
     * @return self
     */
    public function between(
        string $between
    ): self{
        $this->between = NULL;

        $this->between = "$between";

        return $this;
    }

    /**
     * Sets the BETWEEN condition for the query
     *
     * @param string $between The BETWEEN condition
     * @return self
     */
    public function betweenDate(
        string $between_date
    ): self{
        $this->between_date = NULL;

        $this->between_date = "$between_date";

        return $this;
    }    

    /**
     * Sets the LIMIT and OFFSET for the query
     *
     * @param int $begin The begin limit
     * @param int $end The end limit
     * @return self
     */
    public function limit(
        int $begin, 
        int $end
    ): self{
        $this->limit = NULL;

        $this->limit = "LIMIT $end OFFSET $begin";

        return $this;
    }

    /**
     * Sets OFFSET for the query
     *
     * @param int $begin The begin limit
     * @param int $end The end limit
     * @return self
     */
    public function offset(
        int $begin, 
        int $end
    ): self{
        $this->offset = NULL;

        $this->offset = "OFFSET $begin ROWS FETCH NEXT $end ROWS ONLY";

        return $this;
    }    

    /**
     * @param int $begin The begin limit
     * @param int $end The end limit
     * @return self
     */
    public function rlimit(
        int $begin, 
        int $end
    ): self{

        $this->rlimit = NULL;

        $this->rlimit = [ 'begin' => $begin , 'end' => $end ];

        return $this;
    }    

    /**
     * Sets the IS condition for the query
     *
     * @param string $type The type of condition
     * @param string $property The property to check
     * @return self
     */
    public function is(
        string $type, 
        string $property
    ): self{
        $this->is = NULL;

        $this->is = " AND $property IS $type";

        return $this;
    }

    /**
     * Sets the HAVING clause for the query
     *
     * @param string $count The COUNT() function argument
     * @param string $sign The sign for comparison (e.g., '>', '=', '<', etc.)
     * @return self
     */
    public function having(
        string $count, 
        string $sign
    ): self{
        $this->having = NULL;

        $this->having = "HAVING COUNT($count) $sign ?";

        return $this;
    }

    /**
     * Sets the LIMIT for the query
     *
     * @param int $limit The LIMIT value
     * @return self
     */
    public function limit_i(
        int $limit
    ): self{
        $this->limit_i = NULL;

        $this->limit_i = "LIMIT $limit";

        return $this;
    }

    /**
     * Sets the ORDER BY clause for the query
     *
     * @param string $key The key to order by
     * @param string $direction The direction of ordering ('ASC' or 'DESC')
     * @return self
     */
    public function orderBy(
        string $key, 
        string $direction
    ): self{
        $this->order = NULL;

        $this->order = "ORDER BY $key $direction";

        return $this;
    }
    /**
     * Sets the GROUP BY clause for the query
     *
     * @param string $group The field to group by
     * @return self
     */
    public function groupBy(
        string $group
    ): self{

        $this->group = NULL;

        $this->group = "GROUP BY $group";

        return $this;
    }

    /**
     * Sets the AND conditions for the query
     *
     * @param array $getand The array of conditions to be connected with AND
     * @return self
     */
    public function and(
        array $getand = []
    ): self{
        $this->and = NULL;

        foreach ($getand as $val) {
            $this->and .= " AND " . $val . " = ? ";
        }

        return $this;
    }

    /**
     * Sets the JSON AND conditions for the query
     *
     * @param array $getand The array of conditions to be connected with AND
     * @return self
     */
    public function andJSON(
        string $jsonB = 'data',
        array $getand = []
    ): self{
        $this->andJSON = NULL;

        foreach ($getand as $val) {
            $this->andJSON .= " AND " . "$jsonB::jsonb ->> '$val'" . " = ? ";
        }

        return $this;
    }    

    /**
     * Sets the OR conditions for the query
     *
     * @param array $getOr The array of conditions to be connected with OR
     * @return self
     */
    public function or(
        array $getOr = []
    ): self{
        $this->or = NULL;

        foreach ($getOr as $val) {
            $this->or .= " OR " . $val . " = ? ";
        }

        return $this;
    }

    /**
     * Sets the JOIN clauses for the query
     *
     * @param array $getJoin The array of JOIN clauses
     * @return self
     */
    public function join(
        array $getJoin = []
    ): self{

        $this->join = NULL;

        foreach ($getJoin as $val) {
            $this->join .= ' INNER JOIN ' . str_replace('|', ' ON ', $val);
        }

        return $this;
    }

    /**
     * Sets the LEFT JOIN clauses for the query
     *
     * @param array $getJoin The array of JOIN clauses
     * @return self
     */
    public function joinLeft(
        array $getJoin = []
    ): self{
        $this->joinLeft = NULL;

        foreach ($getJoin as $val) {
            $this->joinLeft .= ' LEFT JOIN ' . str_replace('|', ' ON ', $val);
        }

        return $this;
    }   
    
    /**
     * Sets the RIGHT JOIN clauses for the query
     *
     * @param array $getJoin The array of JOIN clauses
     * @return self
     */
    public function joinRight(
        array $getJoin = []
    ): self{

        $this->joinRight = NULL;

        foreach ($getJoin as $val) {
            $this->joinRight .= ' RIGHT JOIN ' . str_replace('|', ' ON ', $val);
        }

        return $this;
    } 
    
    /**
     * Sets the FULL JOIN clauses for the query
     *
     * @param array $getJoin The array of JOIN clauses
     * @return self
     */
    public function joinFull(
        array $getJoin = []
    ): self{

        $this->joinFull = NULL;

        foreach ($getJoin as $val) {
            $this->joinFull .= ' FULL JOIN ' . str_replace('|', ' ON ', $val);
        }

        return $this;
    }     

    /**
     * Sets the SET clause for the query
     *
     * @param array $getSet The array of properties to set
     * @return self
     */
    public function set(
        array $getSet = []
    ): self{

        $this->set = NULL;

        foreach ($getSet as $val) {
            $this->set .= " {$val} = ? ,";
        }

        $this->set = rtrim($this->set, " , ");

        return $this;
    }

    /**
     * Sets the SET oracle format date clause for the query
     *
     * @param array $getSet The array of properties to set
     * @param bool $state
     * @return self
     */
    public function setDate(
        array $getSet = [], 
        bool $state = false
    ): self{

        $this->set_date = NULL;

        $addSet = $state==true ? 'SET':',';

        foreach ($getSet as $val) {
            $this->set_date .= " $addSet {$val} = TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') ,";
        }

        $this->set_date = rtrim($this->set_date, " , ");

        return $this;
    }    

    /**
     * Sets up the SUM(CASE...) clause for the query
     * @param array $sumCase
     * @return self
     */
    public function sumCase(
        array $sumCase = []
    ): self{
        $this->sumCase = NULL;
    
        foreach ($sumCase as $val) {
            $explod = explode('|', $val);
            $property = $explod[0];
            $alias = $explod[1] ?? $explod[0];
    
            $this->sumCase .= " SUM(CASE WHEN {$property} = ? THEN 1 ELSE 0 END) AS {$alias}, ";
        }
    
        $this->sumCase = rtrim($this->sumCase, ", ");
    
        return $this;
    }
    
    /**
     * Sets parameters for the query
     *
     * @param array|null $rset
     * @return self
     */
    public function rset(
        array $rset = []
    ): self{
        $this->rset = NULL;

        $this->rset = $rset;

        return $this;
    }     

    /**
     * Sets the SET clause with arithmetic operations for the query
     *
     * @param array $getSet The array of properties to set
     * @param string|null $sign The arithmetic sign for the properties
     * @return self
     */
    public function set_i(
        array $getSet = [], 
        string $sign = "+"
    ): self{
        $this->set_i = NULL;

        foreach ($getSet as $val) {
            $this->set_i .= $val . " = $val $sign ?" . " , ";
        }

        $this->set_i = rtrim($this->set_i, " , ");

        return $this;
    }

    /**
     * Sets the REPLACE function for properties in the query
     *
     * @param array $properties The array of properties for REPLACE function
     * @return self
     */
    public function replace(
        array $properties = []
    ): self{

        $this->replace = NULL;

        foreach ($properties as $val) {
            $this->replace .= $val . " = REPLACE( $val , ? , ? ) , ";
        }

        $this->replace = rtrim($this->replace, " , ");

        return $this;
    } 
}