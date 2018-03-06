<?php
$root_url = 'http://localhost/demo/';

$pattern = '/class="(.*?)"/';

$id = @$_GET['id'];
$remove_classes = array();
if (!empty($id)) {
    $url = $root_url .'type-3.php';

	    $url_less = $root_url . 'less/type-3.less';
    $url_bootsrap = $root_url . 'css/bootstrap.min.css';
    $url_awesome = $root_url . 'css/font-awesome.min.css';


    $content_html = file_get_contents($url);

    $content_less = file_get_contents($url_less);
    $content_bootstrap = file_get_contents($url_bootsrap);
    $content_awesome = file_get_contents($url_awesome);


    preg_match_all($pattern, $content_html, $matches);

    if (!empty($matches[1])) {
        $classes = $matches[1];

        foreach ($classes as $class) {
            $class = trim($class);
            if (!empty($class)) {
                $sub_classes = explode(' ', $class);

                foreach ($sub_classes as $item) {
                    if (!empty($item)) {

                        $is_less = strpos($content_less, $item);

                        $is_bootstrap = strpos($content_bootstrap, $item);

                        $is_awesome = strpos($content_awesome, $item);

                        if (!$is_less && !$is_bootstrap && !$is_awesome) {
                            $remove_classes[] = $item;
                        }
                    }
                }
            }
        }
    }
}