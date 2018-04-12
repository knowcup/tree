<?php

/**
 * Description:
 * Motto: 知杯者，无惧.
 * Author: Captain <QQ:497439092>
 * Date: 2018/4/12
 * Time: 16:26
 */

use knowcup\tree\Tree;

include "../src/Tree.php";
$array = [
    [
        'id' => 1,
        'parent_id' => null,
        'name' => '第一级 One',
    ],
    [
        'id' => 2,
        'parent_id' => null,
        'name' => '第一级 Two',
    ],
    [
        'id' => 3,
        'parent_id' => null,
        'name' => '第一级 Three',
    ],

    [
        'id' => 4,
        'parent_id' => 1,
        'name' => '第二级 One',
    ],
    [
        'id' => 5,
        'parent_id' => 1,
        'name' => '第二级 Two',
    ],
    [
        'id' => 6,
        'parent_id' => 1,
        'name' => '第二级 Three',
    ],
    [
        'id' => 7,
        'parent_id' => 3,
        'name' => '第二级 Four',
    ],
    [
        'id' => 8,
        'parent_id' => 3,
        'name' => '第二级 Five',
    ],
    [
        'id' => 9,
        'parent_id' => 5,
        'name' => '第三级 One',
    ],
    [
        'id' => 10,
        'parent_id' => 3,
        'name' => '第三级 Two',
    ]
];

var_dump( Tree::listTreeArr($array) );
var_dump( Tree::nestTreeArr($array) );