<?php

namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;


class Database extends Admin
{
    private $fp;
    private $size = 0; // 当前打开文件大小
    protected $config = [];

    protected function _initialize()
    {
        parent::_initialize();

        $this->config = [
            'path' => CACHE_PATH . "backup/", // 路径
            'part' => 2097152, // 分卷大小 2M
            'compress' => 0, // 0:不压缩 1:启用压缩
            'level' => 9
        ]; // 压缩级别, 1:普通 4:一般 9:最高
    }

    /**
     * 数据表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $tables = isset($post['tables']) ? $post['tables'] : [];
            if (empty ($tables)) {
                $this->error('请选择要备份的数据表');
            }
            // 检查备份目录是否可写 创建备份目录
            is_writeable($this->config ['path']) || mkdir($this->config ['path'], 0777, true);
            // 生成备份文件信息
            $file = ['name' => date('Ymd_His', time()), 'part' => 1];
            // 卷标及卷名
            session('backup_file', $file);
            // 缓存要备份的表
            session('backup_tables', $tables);

            // 初始化文件
            if (false !== $this->create()) {
                $param = ['part' => 0, 'start_num' => 0];
                echo "初始化完成";
                echo '<script>self.location.href="' . url('Admin/Database/export', $param) . '";</script>';
            } else {
                $this->error('初始化失败，备份文件创建失败！');
            }
        } else {
            $list = Db::query('SHOW TABLE STATUS');
            $list = array_map('array_change_key_case', $list);
            $this->assign('tablelist', $list);
            return $this->fetch();
        }
    }

    /**
     * 数据库文件列表
     */
    public function backuplist()
    {
        $files = glob($this->config ['path'] . '*.sql');
        $list = [];
        foreach ($files as $k => $item) {
            $name = explode('_', basename($item));
            $temp = [];
            $temp ['name'] = basename($item);
            $temp ['size'] = $this->format_bytes(filesize($item));
            $temp ['time'] = filemtime($item);
            $temp ['part'] = str_replace('.sql', '', $name [2]);
            $temp ['per'] = str_replace($name [2], '', $temp ['name']);
            $list [] = $temp;
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 备份数据库
     */
    public function export()
    {
        if ($this->request->has('part') && $this->request->has('start_num')) { // 备份数据
            $part = $this->request->param('part', 0, 'intval');
            $start_num = $this->request->param('start_num', 0, 'intval');
            $tables = session('backup_tables');
            // 备份指定表
            $start_num = $this->export_database($tables [$part], $start_num);

            $str = '<div style="height: auto;margin: 300px 0 auto auto;text-align: center;">';
            // 备份出错
            if (false === $start_num) { // 出错
                $this->error('备份出错！');
            } elseif (0 === $start_num) { // 此表已备份完成，要备份 下一表
                if (isset ($tables [++$part])) {
                    $param = ['part' => $part, 'start_num' => 0];
                    $str .= '数据表：' . $tables [$part] . ' 备份完成';
                    $str .= '</div>';
                    $str .= '<script>self.location.href="' . url('', $param) . '";</script>';
                    echo $str;
                } else { // 备份完成，清空缓存
                    session('backup_tables', null);
                    session('backup_file', null);
                    $this->success('备份完成！', url('index'));
                }
            } else { // 表数据超过一千条
                $param = ['part' => $part, 'start_num' => $start_num [0]];
                $rate = floor(100 * ($start_num [0] / $start_num [1]));
                $str .= '正在备份数据表：' . $tables [$part] . '  ...(' . $rate . '%)';
                $str .= '</div>';
                $str .= '<script>self.location.href="' . url('', $param) . '";</script>';
                echo $str;
            }
        } else { // 出错
            $this->error('参数错误！');
        }
    }

    /**
     * 起始行数
     *
     * @param unknown $table
     *            表名
     * @param unknown $start
     *            起始行数
     * @return boolean|multitype:number unknown |number
     */
    public function export_database($table, $start_num)
    {
        // 备份表结构
        if (0 == $start_num) {
            $result = Db::query("SHOW CREATE TABLE `{$table}`");
            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= trim($result [0] ['Create Table']) . ";\n\n";
            if (false === $this->write($sql)) {
                return false;
            }
        }

        // 数据总数
        $result = Db::query("SELECT COUNT(*) AS count FROM `{$table}`");
        $count = $result ['0'] ['count'];
        // 备份表数据
        if ($count) {
            // 写入数据注释
            if (0 == $start_num) {
                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n";
                $this->write($sql);
            }
            // 备份数据记录
            $result = Db::query("SELECT * FROM `{$table}` LIMIT {$start_num}, 1000");
            foreach ($result as $row) {
                $row = $this->check_string($row);
                $sql = "INSERT INTO `{$table}` VALUES ('" . implode("', '", $row) . "');\n";
                if (false === $this->write($sql)) {
                    return false;
                }
            }
            // 还有更多数据
            if ($count > $start_num + 1000) {
                // 起始行数，总条数
                return [$start_num + 1000, $count];
            }
        }

        // 备份下一表
        return 0;
    }

    /**
     * 还原数据
     *
     * @param string $part
     * @param string $start_num
     */
    public function import()
    {
        // 查询文件前缀
        $per = $this->request->has('part') ? $this->request->param('part', 0, 'intval') : '';
        if (empty ($per)) {
            $import_file = session('import_file');
        } else {
            $import_file ['pre'] = $per;
            $import_file ['part'] = 1;
            session('import_file', $import_file);
        }

        $file_list = glob($this->config ['path'] . $import_file ['pre'] . '*.sql');
        $file_count = count($file_list);
        if ($import_file ['part'] > count($file_list)) {
            $this->error('还原错误');
        }
        // 起始行数
        $start_num = $this->request->has('start_num') ? $this->request->param('start_num', 0, 'intval') : '';

        // 还原结果，有三种可能结果，0：此卷已还原完成 ，false:还原失败，param:还原位置，文件长度
        $start_num = $this->import_database($start_num);
        // 卷标
        $part = $import_file ['part'];
        if (false === $start_num) {
            $this->error('还原数据出错！');
        } elseif (0 === $start_num) { // 下一卷
            if (isset ($file_list [++$part])) {
                $param = ['part' => $part, 'start_num' => 0];
                echo "正在还原...#{$part}";
                echo '<script>self.location.href="' . U('', $param) . '";</script>';
            } else {
                session('import_file', null);
                $this->config ['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->success('还原完成！', url('database/backuplist'));
            }
        } else {
            $param = ['part' => $part, 'start_num' => $start_num [0]]; // 返回的
            if ($start_num [1]) {
                $rate = floor(100 * ($start_num [0] / $start_num [1]));
                echo "正在还原...#{$part} ({$rate}%)";
                echo '<script>self.location.href="' . url('', $param) . '";</script>';
            } else {
                echo "正在还原...#{$part}";
                echo '<script>self.location.href="' . url('', $param) . '";</script>';
            }
        }
    }

    /**
     * 还原表结构
     *
     * @param unknown $start_num
     *            起始行数
     * @return boolean|number|multitype:number
     */
    public function import_database($start_num)
    {
        // 还原数据
        $import_file = session('import_file');
        if ($this->config ['compress']) {
            $file_name = $this->config ['path'] . $import_file ['pre'] . $import_file ['part'] . '.gz';
            $fp = gzopen($file_name, 'r');
            $size = 0;
        } else {
            $file_name = $this->config ['path'] . $import_file ['pre'] . $import_file ['part'] . '.sql';
            $size = filesize($file_name);
            $fp = fopen($file_name, 'r');
        }
        // 起始行数
        if ($start_num) {
            $this->config ['compress'] ? gzseek($fp, $start_num) : fseek($fp, $start_num);
        }

        $sql = '';
        for ($i = 0; $i < 1000; $i++) {
            /*
             * 每次读取一行内容，都用正则匹配，如果西欧不成功，则继续读取内容直到可以匹配为止
             */
            $sql .= $this->config ['compress'] ? gzgets($fp) : fgets($fp);
            // 匹配这行内容是否是合法SQL语句
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== Db::execute(trim($sql))) {
                    $start_num += strlen($sql);
                } else {
                    return false;
                }
                // 执行完SQL语句后，清空SQL语句，以便组合下一次：可执行的SQL语句
                $sql = '';
            } elseif ($this->config ['compress'] ? gzeof($fp) : feof($fp)) { // 是否在文件结尾
                $import_file ['part']++;
                session('import_file', $import_file);
                return 0;
            }
        }
        return array($start_num, $size);
    }

    // 优化表
    public function optimization()
    {
        // 表名
        $tables = $this->request->param('tables');
        if (empty ($tables)) {
            $this->error("请指定要优化的表！");
        } else {
            $table_str = is_array($tables) ? implode('`,`', $tables) : $tables;
            if (Db::query("OPTIMIZE TABLE `{$table_str}`")) {
                $this->success("数据表优化完成！");
            } else {
                $this->error("数据表优化出错请重试！");
            }
        }
    }

    // 修复表
    public function repair()
    {
        // 表名
        $tables = $this->request->param('tables');
        if (empty ($tables)) {
            $this->error("请指定要修复的表！");
        } else {
            $table_str = is_array($tables) ? implode('`,`', $tables) : $tables;
            if (Db::query("REPAIR TABLE `{$table_str}`")) {
                $this->success("数据表修复完成！");
            } else {
                $this->error("数据表修复出错请重试！");
            }
        }
    }

    // 表结构
    public function showcreat()
    {
        // 表名
        $tablename = $this->request->param('tables');
        if (empty ($tablename)) {
            $this->error("请指定要显示结构的表！");
        }

        $result = Db::query("SHOW CREATE TABLE `{$tablename}`");
        $this->assign('sql', $result [0] ['create table']);
        return $this->fetch();
    }

    /**
     * 在卷首写入初始数据
     *
     * @return boolean
     */
    public function create()
    {
        $sql = "-- -----------------------------\n";
        $sql .= "-- Think MySQL Data Transfer \n";
        $sql .= "-- \n";
        $sql .= "-- Host     : " . config('DB_HOST') . "\n";
        $sql .= "-- Port     : " . config('DB_PORT') . "\n";
        $sql .= "-- Database : " . config('DB_NAME') . "\n";
        $sql .= "-- \n";
        $backup_file = session('backup_file');
        $sql .= "-- Part : #{$backup_file['part']}\n";
        $sql .= "-- Date : " . date("Y-m-d H:i:s") . "\n";
        $sql .= "-- Author : 壮壮\n";
        $sql .= "-- Url : zzcms.com\n";
        $sql .= "-- -----------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        return $this->write($sql);
    }

    /**
     * 写入SQL语句
     *
     * @param string $sql
     *            要写入的SQL语句
     * @return boolean true - 写入成功，false - 写入失败！
     */
    private function write($sql)
    {
        $size = strlen($sql);
        // 由于压缩原因，无法计算出压缩后的长度，这里假设压缩率为50%，
        // 一般情况压缩率都会高于50%；
        $size = $this->config ['compress'] ? $size / 2 : $size;
        $this->open($size);
        return $this->config ['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }

    /**
     * 打开一个卷，用于写入数据
     *
     * @param integer $size
     *            写入数据的大小
     */
    private function open($size)
    {
        $backup_file = session('backup_file');
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config ['part']) {
                $this->config ['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->fp = null;
                $backup_file ['part']++;
                session('backup_file', $backup_file);
                $this->create();
            }
        } else {
            $backuppath = $this->config ['path'];
            $filename = "{$this->config['path']}{$backup_file ['name']}_{$backup_file ['part']}.sql";
            if ($this->config ['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = @gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = @fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }

    /**
     * 删除备份文件
     */
    public function delete_file()
    {
        $files = $this->request->param('filenames');
        if (empty ($files)) {
            $this->error('请选择要删除的文件');
        }
        if (is_array($files)) {
            foreach ($files as $k => $item) {
                @unlink($this->config ['path'] . $item);
            }
        } else {
            @unlink($this->config ['path'] . $files);
        }
        $this->redirect('database/backuplist');
    }

    /**
     * 过滤
     * @param $size
     */
    private function check_string($row)
    {
        return $row;
        if (version_compare(PHP_VERSION, '7', '<')) {
            return array_map('mysql_escape_string ', $row);
        } else {
            return array_map('mysqli_real_escape_string', $row);
        }
    }

    /**
     * 格式化字节大小
     * @param number $size 字节数
     * @param string $delimiter 数字和单位分隔符
     * @return string 格式化后的带单位的大小
     *
     */
    private function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units [$i];
    }
}