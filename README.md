# tree
数组转为树状结构，支持树状嵌套、树状列表
php版本 >= 7.0

<p>数组:</p>
  <pre><code>$array = [
    [ 'id' => 1, 'parent_id' => null, 'name' => '第一级 One' ],
    [ 'id' => 2, 'parent_id' => null, 'name' => '第一级 Two' ],
    [ 'id' => 3, 'parent_id' => null, 'name' => '第一级 Three' ],
    [ 'id' => 4, 'parent_id' => 1, 'name' => '第二级 One' ],
    [ 'id' => 5, 'parent_id' => 1, 'name' => '第二级 Two' ],
    [ 'id' => 6, 'parent_id' => 1, 'name' => '第二级 Three' ],
    [ 'id' => 7, 'parent_id' => 3, 'name' => '第二级 Four' ],
    [ 'id' => 8, 'parent_id' => 3, 'name' => '第二级 Five' ],
    [ 'id' => 9, 'parent_id' => 5, 'name' => '第三级 One' ],
    [ 'id' => 10, 'parent_id' => 5, 'name' => '第三级 Two' ]
];</code></pre>

<p>转为树状列表（平行）：</p>
  <pre><code>\knowcup\tree\Tree::listTreeArr($array);</code></pre>
  
<p>转为树状列表（嵌套）：</p>
  <pre><code>\knowcup\tree\Tree::nestTreeArr($array);</code></pre>
