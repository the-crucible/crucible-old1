<?php

/**
 * Description of View
 *
 * @author Tejaswi Sharma <tejaswi@crucible-framework.org>
 */
class View {

    const CSS_PATH = "css";
    const JS_PATH = "js";

    /*
     * @var View
     */

    private static $_instatnce = null;

    /**
     * @var array
     */
    private $_js = array();

    /**
     * @var array
     */
    private $_css = array();

    /**
     * @var array
     */
    private $_meta = array();

    /**
     * @var string
     */
    private $_title = null;

    /**
     * @var string view name
     */
    private $_view_name = null;

    /**
     * @var string layout name
     */
    private $_layout_name = null;

    /**
     * @var string app template dir
     */
    private $_app_template_dir = null;

    /**
     * @var string module template dir
     */
    private $_module_template_dir = null;

    /**
     * @var array array of input got from controller
     */
    private $_input_arr = array();

    /**
     * @var string content of the view;
     */
    private $_view_content = '';

    /**
     * It returns an instance of view
     * 
     * @return View
     */
    public static function getInstance() {
        if (is_null(self::$_instatnce)) {
            self::$_instatnce = new View();
        }
        return self::$_instatnce;
    }

    /**
     * It returns the same instance of view 
     * even if its cloned
     * 
     * @return View
     */
    public function __clone() {
        return self::getInstance();
    }

    /**
     * Constructor function
     */
    private function __construct() {
        $app = Request::getInstance()->getApp();
        $module = Request::getInstance()->getController();

        $app_dir = Router::getInstance()->getAppPath($app);
        $this->_app_template_dir = $app_dir . 'templates' . DS . 'layouts';
        $this->_module_template_dir = $app_dir . 'modules' . DS . $module
                . DS . 'templates';
    }

    /**
     * 
     */
    public function setInputArr($inputArr) {
        $this->_input_arr = $inputArr;
    }

    /**
     * It set the view name 
     * 
     * @param type $view_name
     */
    public function setView($view_name) {
        if (is_file($this->_module_template_dir . DS . $view_name . '.php')) {
            $this->_view_name = $view_name;
            return true;
        } else {
            return false;
        }
    }

    /**
     * It gives view name
     * 
     * @return string get view name
     */
    public function getView() {
        return $this->_view_name;
    }

    /**
     * It checks for layout file and set layout name
     * 
     * @param type $layout_name
     * @return boolean
     */
    public function setLayout($layout_name) {
        if (is_file($this->_app_template_dir . DS . $layout_name . '.php')) {
            $this->_layout_name = $layout_name;
            return true;
        } else {
            return false;
        }
    }

    /**
     * It returns layout name
     * 
     * @return string layout name
     */
    public function getLayout() {
        return $this->_layout_name;
    }

    /**
     * It get the final view
     */
    public function getBody() {
        # First get the view file processed
        $view_file = $this->_module_template_dir . DS . $this->getView() . '.php';
        # And put the result in the _view_content variable
        $this->_view_content = $this->_processView($view_file);
        
        # Finally process it with layout
        $layout_file = $this->_app_template_dir . DS . $this->getLayout() . '.php';
        return $this->_processView($layout_file);
    }

    /**
     * It returns the processes elements.
     */
    private function _processView($______v______File, $______i______arr = null) {
        $____f____a = $this->_getBindArr($______i______arr);

        if ($____f____a === false) {
            throw new InvalidViewInputException($______v______File);
        }

        ob_start();
        foreach ($____f____a as $__n => $__v) {
            $$__n = $__v;
        }
        include $______v______File;
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    /**
     * Returns the array of input variables which will be
     * used to embed values in the element/view/layout 
     * 
     * @param type $input_arr
     * @return boolean
     */
    private function _getBindArr($input_arr) {
        if (is_null($input_arr)) {
            return $this->_input_arr;
        } else if (!is_null($input_arr) && is_array($input_arr)) {
            return $input_arr;
        } else {
            return false;
        }
    }

    /**
     * Echo the element of the content
     * 
     * @param type $element_name name of the element
     */
    public static function printElement($element_name){
        echo self::getInstance()->getElement($element_name);
    }
    
    /**
     * Return the content of the element with the value 
     * of the variables embedded
     * 
     * @param string $element_name name of the element
     * @return string content of the element
     * @throws ElementNotFoundException thrown if element is not found
     */
    public function getElement($element_name,$input_array=null) {
        $element_file_name = $this->_module_template_dir . DS . "elements" . 
                                DS . $element_name . '.php';
        if (is_file($element_file_name)) {
            return $this->_processView($element_file_name,$input_array);
        } else {
            throw new ElementNotFoundException($element_name);
        }
    }

    /**
     * This function returns the main view content
     * 
     * @return string content of main view 
     */
    public function getMainView(){
        return $this->_view_content;
    }
    /**
     * echo the main view content
     */
    public static function printMainView(){
        echo self::getInstance()->getMainView();
    }

    /**
     * It add js path to the array;
     * 
     * @param string $js_path
     */
    public function addJs($js_paths) {
        # If $css_path is not an array but a string (single path)
        if (!is_array($js_paths)) {
            $js_paths = array($js_paths);
        }

        foreach ($js_paths as $js_path) {
            if ($this->_isUrl($js_path)) {
                #If url then just add it
                $this->_js[] = $js_path;
            } else {
                if (strpos($js_path, ".js") === FALSE) {
                    $js_path = $js_path . ".js";
                }
                $path = "/" . View::JS_PATH . "/$js_path";
                $this->_js[] = $path;
            }
        }
    }

    /**
     * It add js path to the array;
     * 
     * @param string $js_path
     */
    public static function addJavascript($js_path) {
        self::getInstance()->addJs($js_path);
    }
    
    /**
     * This function will return the js scripts
     * added for the page
     * 
     * @return array Array of the js scripts added
     */
    public function getJs() {
        return $this->_js;
    }

    /**
     * This function will print all the js scripts tag
     */
    public static function printJs() {
        $js_array = self::getInstance()->getJs();
        foreach ($js_array as $value) {
            echo "<script type='text/javascript' src='$value'></script>";
        }
    }

    /**
     * It adds css path to the array
     * 
     * @param string $css_path 
     */
    public function addCss($css_paths) {
        
        # If $css_path is not an array but a string (single path)
        if (!is_array($css_paths)) {
            $css_paths = array($css_paths);
        }

        foreach ($css_paths as $css_path) {
            if ($this->_isUrl($css_path)) {
                #If url then just add it
                $this->_css[] = $css_path;
            } else {
                if (strpos($css_path, ".css") === FALSE) {
                    $css_path = $css_path . ".css";
                }
                $path = "/" . View::CSS_PATH . "/$css_path";
                $this->_css[] = $path;
            }
        }
    }

    public static function addStyleSheet($css_path) {
        self::getInstance()->addCss($css_path);
    }

    /**
     * It returns array of stylesheets
     * 
     * @return array 
     */
    public function getCss() {
        return $this->_css;
    }

    /**
     * This function print all the css files as tags
     */
    public static function printCss() {
        $css_array = self::getInstance()->getCss();
        foreach ($css_array as $value) {
            echo "<link rel='stylesheet' type='text/css' href='$value'>";
        }
    }

    /**
     * It adds meta info to the array
     * 
     * @param type $meta_info
     */
    public function addMeta($meta_info) {
        $this->_meta[] = $meta_info;
    }

    /**
     * It adds meta info to the array
     * 
     * @param type $meta_info
     */
    public static function addMetaInfo($meta_info) {
        self::getInstance()->addMeta($meta_info);
    }

    /**
     * This function returns meta array
     * 
     * @return array get meta array
     */
    public function getMeta() {
        return $this->_meta;
    }

    public static function printMeta() {
        $meta_arr = self::getInstance()->getMeta();
        foreach ($meta_arr as $meta_att_arr) {
            $meta_att_content = '';
            foreach ($meta_att_arr as $attr => $att_value) {
                $meta_att_content .= "$attr='$att_value' ";
            }
            echo "<meta $meta_att_content>";
        }
    }

    /**
     * It sets title name
     * 
     * @param string $title
     */
    public function setTitle($title) {
        $this->_title = $title;
    }

    /**
     * It set the title of the page
     */
    public static function setPageTitle($title) {
        self::getInstance()->setTitle($title);
    }

    /**
     * This function will return the title 
     * of the page
     * 
     * @return string title of the page
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * This function will be used to print title tag
     * along with the set title
     */
    public static function printTitle() {
        echo "<title>" . self::getInstance()->getTitle() . "</title>";
    }

    /**
     * 
     * @param string $path 
     * @return boolean
     */
    private function _isUrl($path) {
        if (preg_match("/^http/", $path)) {
            return true;
        } else {
            return false;
        }
    }

}

?>
