<?php
/*
|-----------------------------------------------------------------------
| Manage standard structure of block
|-----------------------------------------------------------------------
| Correct format:
| ID
| ---ID.php
| ---ID-content.php
| ---ID-screenshot.png
| ---css
| -------bootstrap-min-3.7.css
| -------font-awesome-min-.css
| -------ID.css
| ---js
| -------jQuery-min-2.js
| -------bootstrap-min-2.js
| ---libs
| ---less
| ---images
| ---fonts
|
|
*/
class standard {
    private $root_dir;
    private $dir_blocks;
    private $correct_format_IDs;
    private $not_format_IDs;

    public $correct_log = 'log_correct.txt';
    public $not_log = 'log_not.txt';
    public $log = 'log.txt';

    public $php_files = [];
    public $screenshot_files = [];

    public $max_blocks = 1;

    public function __construct($root_path = NULL) {

        if (!empty($root_path) && realpath($root_path)) {
                $this->root_dir = $root_path;

                fclose(fopen($this->correct_log, 'w'));
                fclose(fopen($this->not_log, 'w'));
                fclose(fopen($this->log, 'w'));
        }

    }

    public function initPHPFiles($dir_block, $id) {
        $this->php_files = [
            $dir_block."\\{$id}.php",
            $dir_block."\\{$id}-content.php"
        ];
    }

    public function initScreenshotFiles($dir_block, $id) {
        $this->screenshot_files = [
            'jpg' => $dir_block."\\{$id}-screenshot.jpg",
            'png' => $dir_block."\\{$id}-screenshot.png"
        ];
    }

    /**
     * Get dir of block
     */
    public function setDirBlocks() {

        if (is_dir($this->root_dir)) {

            $this->dir_blocks = [];

            $dir_blocks = scandir($this->root_dir);

            foreach ($dir_blocks as $id) {
                $id = (int)$id;
                if ($id > 0) {
                    $this->dir_blocks[] = realpath($this->root_dir.'/'.$id);
                }

            }
            return TRUE;
        }
        return FALSE;
    }

    public function getPathBlocks() {

    }
    public function setPHP($dir_block){

        $flag = FALSE;

        $id = basename($dir_block);

        $this->initPHPFiles($dir_block, $id);

        $php_files = glob(realpath($dir_block).'\*.php');

        if (count($php_files) == 1) {

            $php_file = realpath(reset($php_files));

            if (in_array($php_file, $this->php_files)) {

                if (strcmp($php_file, $this->php_files[0]) == 0) {
                    copy($php_file, $this->php_files[1]);
                } else {
                    copy($php_file, $this->php_files[0]);
                }

            } else {
                copy($php_file, $this->php_files[0]);
                rename($php_file, $this->php_files[1]);
            }

            $flag = TRUE;

        } elseif (count($php_files) == 2) {
                $_flag = TRUE;
                foreach ($php_files as $php_file) {
                    if (!in_array($php_file, $this->php_files)) {
                        $flag = FALSE;
                        $_flag = FALSE;
                        break;
                    }
                }
                if ($_flag) {
                    $flag = TRUE;
                }
        } else {
            $flag  = FALSE;
        }

        return $flag;


    }
    public function setScreenshot($dir_block){

        $flag = FALSE;

        $id = basename($dir_block);

        $this->initScreenshotFiles($dir_block, $id);

        $screenshot_files = glob(realpath($dir_block).'\*.{png,jpg,PNG,JPG}', GLOB_BRACE);

        if (count($screenshot_files) == 1) {

            $screenshot_file = strtolower(realpath(reset($screenshot_files)));

            $path_info = pathinfo($screenshot_file);

            if (!in_array($screenshot_file, $this->screenshot_files)) {

                rename($screenshot_file, $this->screenshot_files[strtolower($path_info['extension'])]);

            }

            $flag = TRUE;

        } else {

            $flag = FALSE;

        }

        return $flag;
    }

    public function writeLogs(){

        //correct log
        if (!empty($this->correct_format_IDs)) {
            foreach ($this->correct_format_IDs as $id) {
                file_put_contents($this->correct_log, $id.PHP_EOL, FILE_APPEND);
            }
        }

        //not log
        if (!empty($this->not_format_IDs)) {
            foreach ($this->not_format_IDs as $id) {
                file_put_contents($this->not_log, $id.PHP_EOL, FILE_APPEND);
            }
        }
    }

    /**
     * Running
     */
    public function buildStructure(){

        $this->setDirBlocks();

        if ($this->dir_blocks) {
            $index = 0;
            foreach ($this->dir_blocks as $dir_block) {

                if ($index > $this->max_blocks) {
                    break;
                }
                $index++;

                file_put_contents($this->log, $dir_block.PHP_EOL, FILE_APPEND);

                if (is_dir($dir_block)) {

                    $id = basename($dir_block);

                    $flag = $this->setPHP($dir_block);
                    $_flag = $this->setScreenshot($dir_block);

                    $flag = $flag ? $this->setScreenshot($dir_block) : FALSE;

                    if ($flag) {
                        $this->correct_format_IDs[] = $id;
                    } else {
                        $this->not_format_IDs[] = $id;
                    }
                }
            }

            $this->writeLogs();
        }

    }

    public function buildContent(){

        $this->setDirBlocks();

        if ($this->dir_blocks) {
            $index = 0;
            foreach ($this->dir_blocks as $dir_block) {

                if ($index > $this->max_blocks) {
                    break;
                }
                $index++;

                file_put_contents($this->log, $dir_block.PHP_EOL, FILE_APPEND);

                if (is_dir($dir_block)) {

                    $id = basename($dir_block);


                }
            }

            $this->writeLogs();
        }

    }

    /**
     * Check HTML/CSS
     * Return list of residual css
     */
    public function checkHtmlCss($url){
            $remove_classes = [];
            $page_content = file_get_contents($url);

            if (!empty($page_content)) {

                $patterns = [
                    'class' => '/class="(.*?)"/',
                    'link_css' => '/<link.*?href=[\'\"](.*?)[\'\"].*rel="stylesheet".*?>/',
                ];

                //get list of css contents
                $css_contents = [];
                preg_match_all($patterns['link_css'], $page_content, $_csses);
                if (!empty($_csses[1])) {
                    foreach ($_csses[1] as $_css) {
                        $css_contents[] = file_get_contents($_css);
                    }
                }

                //get list of using csses in page
                preg_match_all($patterns['class'], $page_content, $_classes);
                if (!empty($_classes[1])) {

                    $classes = $_classes[1];

                    foreach ($classes as $class) {

                        $class = trim($class);

                        if (!empty($class)) {

                            $sub_classes = explode(' ', $class);

                            foreach ($sub_classes as $item) {
                                if (!empty($item)) {
                                    $flag = FALSE;
                                    if (!empty($css_contents)) {
                                        foreach ($css_contents as $_css_content) {
                                            if (!$flag) {
                                                $flag = strpos($_css_content, $item);
                                            }
                                        }
                                        if (!$flag) {
                                            $remove_classes[$item] = $item;
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }
            return $remove_classes;

    }

}

$standard = new standard();