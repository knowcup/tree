<?php
/**
 * Description:数组转为树状结构工具类，支持列表树、嵌套树
 * Motto: 知杯者，无惧.
 * Author: Captain <QQ:497439092>
 * Date: 2018/4/8
 * Time: 16:58
 */

namespace knowcup\tree;


class Tree
{
    // 数组的主键
    private static $primKey = "id";
    // 上下级关联字段的名称
    private static $parentField = "parent_id";
    // 生成的层级字段的名称
    private static $levelField = "level";
    // 嵌套后的标识
    private static $nestPrimKey = "id";
    // 嵌套后的字段
    private static $nestField= "sons";

    // 原始数据
    private static $origArr = [];
    // 排序后的数据
    private static $neatArr = [];
    // 主键数组
    private static $primArr = [];
    // 上级数组
    private static $pareArr = [];
    // 排序数组
    private static $seqArr = [];

    /**
     * 对数组进行树状排序（列表）
     * 添加{self::$levelField}字段作为数组元素的级别信息
     * @param array $data
     * @return array
     */
    public static function listTreeArr(array $data):array{
        self::$origArr = $data;
        self::setPrimArr();
        return self::setNeatArr( $data );
    }

    /**
     * 整理元素顺序
     * 如果参数为按照一定的顺序排好： 如上级在前，子级在后， 效率会高很多
     * @param array $dataArr
     *              需要排序的数组
     * @return array
     */
    public static function setNeatArr( array $dataArr ):array{

        if(count($dataArr) > 0){
            foreach ($dataArr as $item) {
                $id = $item[self::$primKey];
                $pid = $item[self::$parentField];
                // 当前节点的位置
                $site = array_search($id, self::$seqArr);

                // 避免上级ID为当前ID  视为脏数据 忽略
                // 过滤已经排过序的元素
                if ($pid == $id || false !== $site) {
                    continue;
                }

                // 如果没有上级
                // 或者 上级目前不存在
                // 放在数组的前面
                if (empty($pid)) {
                    // 设置级别层次
                    $item[self::$levelField] = self::$primArr[$id][self::$levelField] = 1;
                    array_push(self::$seqArr, $id);
                    array_push(self::$neatArr, $item);
                } else {
                    // 查找上级是否存在
                    // 存在：放在上级节点的前面
                    $parentSite = array_search($pid, self::$seqArr);
                    // 插入上级的最后一个子节点之后
                    if (false !== $parentSite) {
                        // 设置级别层次
                        $item[self::$levelField] = self::$primArr[$id][self::$levelField] = self::$primArr[$pid][self::$levelField] + 1;
                        // 寻找上级最后一个元素的位置
                        $length = count(self::$neatArr);
                        // 上级的上级
                        $ppid = self::$primArr[$pid][self::$parentField];
                        // 从当前位置开始， 第一个上级ID = 当前上级的上级的ID  视为：当前上级的最后一个元素
                        for($i = $parentSite+1; $i<=$length; $i++ ){
                            if(!isset(self::$neatArr[$i]) || empty(self::$neatArr[$i][self::$parentField]) || self::$neatArr[$i][self::$parentField] == $ppid){
                                $parentSite = $i-1;
                                break;
                            }
                        }

                        array_splice(self::$seqArr, $parentSite+1,0, $id);
                        array_splice(self::$neatArr, $parentSite+1,0, array($item));

                        // 不存在：查找所有上级元素  进行排序
                    }else{
                        // 父节点清空
                        self::$pareArr = [];
                        self::getParentArr($id);
                        self::$pareArr = array_reverse(self::$pareArr);
                        self::setNeatArr(self::$pareArr);
                    }
                }
            }
        }

        return self::$neatArr;
    }

    /**
     * 对数组进行树状排序（嵌套）
     * 添加{self::$levelField}字段作为数组元素的级别信息
     * 添加{self::$nestField}字段作为数组元素的嵌套标识
     *      若$nestField重复，数据会出现异常
     * @param array $data
     * @return array
     */
    public static function nestTreeArr(array $data):array {
        self::$origArr = $data;
        self::setPrimArr();
        self::setNeatArr( $data );
        return self::setNestArr( self::$neatArr );
    }

    /**
     * 根据给定的数组（经过setNeatArr整理过的） 整理嵌套关系
     * @param array $dataArr
     * @return array
     */
    public static function setNestArr(array $dataArr):array {
        $return = $levelInfo = [];
        foreach ($dataArr as $k=>$item){
            $item['sons'] = [];

            // 从第二条数据 开始对比
            if(!empty($dataArr[--$k])){

                // 等级相同
                if($dataArr[$k]['level'] == $item['level']){
                    array_pop($levelInfo);
                }elseif($dataArr[$k]['level'] > $item['level']){
                    $diff = $dataArr[$k]['level'] - $item['level'] + 1;
                    for($i=0; $i<$diff; $i++){
                        array_pop($levelInfo);
                    }
                }
            }

            // 根节点
            if(empty($levelInfo))
                $return[$item[self::$nestPrimKey]] = $item;
            // 子节点
            else{
                $execStr = '$return["'. implode('"]["'. self::$nestField .'"]["', $levelInfo) .'"]["'. self::$nestField .'"]["'. $item[self::$nestPrimKey]  .'"] = $item;';
                eval($execStr);
            }

            $levelInfo[] = $item[self::$nestPrimKey];
        }

        return $return;
    }

    /**
     * 设置数组为主键为key的数组
     */
    private static function setPrimArr(){
        foreach (self::$origArr as $item) {
            self::$primArr[$item[self::$primKey]] = $item;
        }
    }

    /**
     * 逐级获取父级数组
     * @param int $id
     * @return array
     */
    private static function getParentArr(int $id): array {
        self::$pareArr[] = self::$primArr[$id];

        if(isset(self::$primArr[$id])){
            $pid = self::$primArr[$id][self::$parentField];
            if(!empty($pid) || isset(self::$neatArr[$pid])){
                self::getParentArr($pid);
            }
        }

        return self::$pareArr;
    }

    /**
     * @param string $levelField
     */
    public static function setLevelField(string $levelField)
    {
        self::$levelField = $levelField;
    }

    /**
     * @param string $parentField
     */
    public static function setParentField(string $parentField)
    {
        self::$parentField = $parentField;
    }

    /**
     * @param string $primKey
     */
    public static function setPrimKey(string $primKey)
    {
        self::$primKey = $primKey;
    }

    /**
     * @param string $nestPrimKey
     */
    public static function setNestPrimKey(string $nestPrimKey)
    {
        self::$nestPrimKey = $nestPrimKey;
    }

    /**
     * @param string $nestField
     */
    public static function setNestField(string $nestField)
    {
        self::$nestField = $nestField;
    }
}