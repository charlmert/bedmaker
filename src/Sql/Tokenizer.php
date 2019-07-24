<?php

namespace BedMaker\Sql;

use BedMaker\Sql\Elloquent\RawDB;
use BedMaker\Sql\Elloquent\WithModels;
use PhpMyAdmin\SqlParser\Parser;

class Tokenizer
{
    private $sql;
    private $modifiedSql;
    private $models;
    private $parser;

    public function __construct($sql, array $models = []) {
        $this->sql = $sql;
        $this->modifieldSql = $sql;
        $this->models = $models;
        $this->parser = new Parser($sql);

        if (count($this->parser->errors) > 0) {
            throw new \Exception($this->parser->errors[0]->getMessage());
        }
    }

    public function load($sql, array $modelCalsses = []) {
        $this->sql = $sql;
        $this->modifieldSql = $sql;
        $this->models = $models;
        $this->parser = new Parser($sql);

        if (count($this->parser->errors) > 0) {
            throw new \Exception($this->parser->errors[0]->getMessage());
        }
    }

    public function toElloquent() {
        if (count($this->models) > 0) {
          $this->toElloquentWithModels();
        } else {
          $this->toElloquentRawDB();
        }

        return $this->modifiedSql;
    }

    public function toElloquentRawDB() {
        $this->modifiedSql = RawDB::transform($this->parser);
    }

    public function toElloquentWithModels() {
        $this->modifiedSql = WithModels::transform($this->parser);
    }
}
