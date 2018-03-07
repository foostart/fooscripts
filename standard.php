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
     * Check CSS class using in HTML page but No define in CSS file
     * @param STRING $url is url of block
     * @return ARRAY list of residual CSS
     * @date 7/3/2018
     */
    public function checkHtmlCss($url){
            $remove_classes = [];
            $page_content = file_get_contents($url);

            if (!empty($page_content)) {

                $patterns = [
                    //get list of class name are using in HTML page
                    'class' => '/class="(.*?)"/',
                    //get list of CSS files
                    'link_css' => '/<link.*?href=[\'\"](.*?)[\'\"].*rel="stylesheet".*?>/',
                ];

                //get list of css contents
                $css_contents = [];
                preg_match_all($patterns['link_css'], $page_content, $_csses);

                if (!empty($_csses[1])) {
                    foreach ($_csses[1] as $_css) {
                        $url_css = file_get_contents($_css);
                        if (empty($url_css)) {
                            echo 'Không tìm thấy file css';
                            var_dump($_css);
                            die();
                        }
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

                                //each CSS class checking in all CSS files
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

    /**
     * Check defined CSS name in CSS file but not using in HTML page
     * Current just check only custom CSS file
     * Not check bootstrap and font-awesome
     * @param STRING $url block page
     * @return ARRAY list of residual CSS
     */
    public function checkCssHtml($url) {
        $remove_classes = [];
        $page_content = file_get_contents($url);

        if (!empty($page_content)) {

            $patterns = [
                //get list of class name are using in HTML page
                'class' => '/class="(.*?)"/',
                //get list of CSS files
                'link_css' => '/<link.*?href=[\'\"](.*?)[\'\"].*rel="stylesheet".*?>/',
            ];

            //get list of css contents
            $css_contents = [];
            preg_match_all($patterns['link_css'], $page_content, $_csses);

            if (!empty($_csses[1])) {
                foreach ($_csses[1] as $_css) {
                    $url_css = file_get_contents($_css);
                    if (empty($url_css)) {
                        echo 'Không tìm thấy file css';
                        var_dump($_css);
                        die();
                    }
                    $css_contents[] = file_get_contents($_css);
                }
            }

            //get list of using csses in page
            $css_using = [];
            preg_match_all($patterns['class'], $page_content, $_classes);
            if (!empty($_classes[1])) {

                $_classes = $_classes[1];

                foreach ($_classes as $key => $_class) {

                    $_class = trim($_class);

                    if (!empty($_class)) {

                        $_sub_classes = explode(' ', $_class);

                        foreach ($_sub_classes as $item) {

                            //each CSS class checking in all CSS files
                            if (!empty($item)) {
                                $item = trim($item);
                                $css_using[] = $item;
                            }
                        }
                    }
                }
            }//end if css_using

            /**
             * get list of defined CSS class
             */
            $css_define = [];
            $css_files = [];
            //get list of necessary CSS file
            if (!empty($_csses[1])) {

                $not_check_css_files = ['bootstrap', 'font-awesome'];

                if (!empty($not_check_css_files)) {
                    foreach ($_csses[1] as $_css_file) {

                        $flag = TRUE;
                        foreach ($not_check_css_files as $no_using) {

                            if (strpos($_css_file, $no_using)) {
                                $flag = FALSE;
                            }
                        }
                        if ($flag) {
                            $css_files[] = $_css_file;
                        }
                    }
                }

            }
            //get list of defined in CSS files
            if (!empty($css_files)) {
                $patterns = array_merge($patterns, array(
                    'defined' => '/(\..*?){/',
                ));
                foreach ($css_files as $_css_file) {
                    $css_content = file_get_contents($_css_file);
                    preg_match_all($patterns['defined'], $css_content, $_defined_css);

                    if (!empty($_defined_css[1])) {
                        foreach ($_defined_css[1] as $_d_c) {
                            $_d_c = trim($_d_c);

                            $sub_classes = explode(' ', $_d_c);

                            if (!empty($sub_classes)) {
                                foreach ($sub_classes as $_item) {
                                    $_item = trim($_item);
                                    if (!empty($_item)) {
                                        if (!in_array($_item, $css_define)) {
                                            $css_define[] = $_item;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }//end if defined css

            if (!empty($css_define) && !empty($css_using)) {
                $patterns = array_merge($patterns, array(
                    'class_name' => '/^\./',
                    'tag_name' => '/^[a..z]/',
                    'id_name' => '/^#/',
                    'atr' => '/:.*/'
                ));
                foreach ($css_define as $_item) {
                    //remove hover
                    $_item = preg_replace($patterns['atr'], '', $_item);
                    //class name
                    $preg = preg_match($patterns['class_name'], $_item);
                    if ($preg) {
                        $_item = substr($_item, 1);
                        if (!in_array($_item, $css_using)) {
                            $remove_classes[] = $_item;
                        }
                    }

                    //html tag name

                    //id name
                }
            }
        }//end if empty page_content

        return $remove_classes;
    }

}

$standard = new standard();