<?php

namespace BedMaker\Sql\Elloquent;

class RawDB
{

    /*
      DB::select('posts.id AS alias','posts.title','posts.body')
     ->from('posts')
     ->where('posts.author_id', '=', 1)
     ->orderBy('posts.published_at', 'DESC')
     ->limit(10)
     ->get();
     */
    public static function transform($parser) {
        $queryCode = 'DB::select(';
        foreach ($parser->statements as $statement) {
            $selectArr = [];
            foreach ($statement->expr as $expr) {
                $selectArr[] = "'{$expr->expr} AS {$expr->alias}'";
            }

            $queryCode .= join(', ', $selectArr);
            $queryCode .= ')' . "\n";

            $fromArr = [];
            foreach ($statement->from as $from) {
                $fromArr[] = "->from('{$from->table} AS {$from->alias}')";
            }

            $queryCode .= join("\n", $fromArr) . "\n";

            $whereArr = [];
            foreach ($statement->where as $where) {
                $whereArr[] = "->where('{$where->expr} {$from->alias}')";
            }

            $queryCode .= join("\n", $whereArr) . "\n";

            dd($queryCode);
        }
        //dd($parser->statements[0]->from[0]);
        return $queryCode;
    }
}
