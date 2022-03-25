<?php
/**
 * PEAR_Common, the base class for the PEAR Installer
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V. V. Cox <cox@idecnet.com>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1.0
 * @deprecated File deprecated since Release 1.4.0a1
 */

/**
 * Include error handling
 */
require_once 'PEAR.php';

/**
 * PEAR_Common error when an invalid PHP file is passed to PEAR_Common::analyzeSourceCode()
 */
define('PEAR_COMMON_ERROR_INVALIDPHP', 1);
define('_PEAR_COMMON_PACKAGE_NAME_PREG', '[A-Za-z][a-zA-Z0-9_]+');
define('PEAR_COMMON_PACKAGE_NAME_PREG', '/^' . _PEAR_COMMON_PACKAGE_NAME_PREG . '\\z/');

// this should allow: 1, 1.0, 1.0RC1, 1.0dev, 1.0dev123234234234, 1.0a1, 1.0b1, 1.0pl1
define('_PEAR_COMMON_PACKAGE_VERSION_PREG', '\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?');
define('PEAR_COMMON_PACKAGE_VERSION_PREG', '/^' . _PEAR_COMMON_PACKAGE_VERSION_PREG . '\\z/i');

// XXX far from perfect :-)
define('_PEAR_COMMON_PACKAGE_DOWNLOAD_PREG', '(' . _PEAR_COMMON_PACKAGE_NAME_PREG .
    ')(-([.0-9a-zA-Z]+))?');
define('PEAR_COMMON_PACKAGE_DOWNLOAD_PREG', '/^' . _PEAR_COMMON_PACKAGE_DOWNLOAD_PREG .
    '\\z/');

define('_PEAR_CHANNELS_NAME_PREG', '[A-Za-z][a-zA-Z0-9\.]+');
define('PEAR_CHANNELS_NAME_PREG', '/^' . _PEAR_CHANNELS_NAME_PREG . '\\z/');

// this should allow any dns or IP address, plus a path - NO UNDERSCORES ALLOWED
define('_PEAR_CHANNELS_SERVER_PREG', '[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*(\/[a-zA-Z0-9\-]+)*');
define('PEAR_CHANNELS_SERVER_PREG', '/^' . _PEAR_CHANNELS_SERVER_PREG . '\\z/i');

define('_PEAR_CHANNELS_PACKAGE_PREG', '(' . _PEAR_CHANNELS_SERVER_PREG . ')\/('
         . _PEAR_COMMON_PACKAGE_NAME_PREG . ')');
define('PEAR_CHANNELS_PACKAGE_PREG', '/^' . _PEAR_CHANNELS_PACKAGE_PREG . '\\z/i');

define('_PEAR_COMMON_CHANNEL_DOWNLOAD_PREG', '(' . _PEAR_CHANNELS_NAME_PREG . ')::('
    . _PEAR_COMMON_PACKAGE_NAME_PREG . ')(-([.0-9a-zA-Z]+))?');
define('PEAR_COMMON_CHANNEL_DOWNLOAD_PREG', '/^' . _PEAR_COMMON_CHANNEL_DOWNLOAD_PREG . '\\z/');

/**
 * List of temporary files and directories registered by
 * PEAR_Common::addTempFile().
 * @var array
 */
$GLOBALS['_PEAR_Common_tempfiles'] = [];

/**
 * Valid maintainer roles
 * @var array
 */
$GLOBALS['_PEAR_Common_maintainer_roles'] = ['lead','developer','contributor','helper'];

/**
 * Valid release states
 * @var array
 */
$GLOBALS['_PEAR_Common_release_states'] = ['alpha','beta','stable','snapshot','devel'];

/**
 * Valid dependency types
 * @var array
 */
$GLOBALS['_PEAR_Common_dependency_types'] = ['pkg','ext','php','prog','ldlib','rtlib','os','websrv','sapi'];

/**
 * Valid dependency relations
 * @var array
 */
$GLOBALS['_PEAR_Common_dependency_relations'] = ['has','eq','lt','le','gt','ge','not', 'ne'];

/**
 * Valid file roles
 * @var array
 */
$GLOBALS['_PEAR_Common_file_roles'] = ['php','ext','test','doc','data','src','script'];

/**
 * Valid replacement types
 * @var array
 */
$GLOBALS['_PEAR_Common_replacement_types'] = ['php-const', 'pear-config', 'package-info'];

/**
 * Valid "provide" types
 * @var array
 */
$GLOBALS['_PEAR_Common_provide_types'] = ['ext', 'prog', 'class', 'function', 'feature', 'api'];

/**
 * Valid "provide" types
 * @var array
 */
$GLOBALS['_PEAR_Common_script_phases'] = ['pre-install', 'post-install', 'pre-uninstall', 'post-uninstall', 'pre-build', 'post-build', 'pre-configure', 'post-configure', 'pre-setup', 'post-setup'];

/**
 * Class providing common functionality for PEAR administration classes.
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V. V. Cox <cox@idecnet.com>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.12
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a1
 * @deprecated This class will disappear, and its components will be spread
 *             into smaller classes, like the AT&T breakup, as of Release 1.4.0a1
 */
class PEAR_Common extends PEAR
{
    /**
     * User Interface object (PEAR_Frontend_* class).  If null,
     * the log() method uses print.
     * @var object
     */
    public $ui = null;

    /**
     * Configuration object (PEAR_Config).
     * @var PEAR_Config
     */
    public $config = null;

    /** stack of elements, gives some sort of XML context */
    public $element_stack = [];

    /** name of currently parsed XML element */
    public $current_element;

    /** array of attributes of the currently parsed XML element */
    public $current_attributes = [];

    /** assoc with information about a package */
    public $pkginfo = [];

    public $current_path = null;

    /**
     * Flag variable used to mark a valid package file
     * @var boolean
     * @access private
     */
    public $_validPackageFile;

    /**
     * PEAR_Common constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = &PEAR_Config::singleton();
        $this->debug = $this->config->get('verbose');
    }

    /**
     * PEAR_Common destructor
     *
     * @access private
     */
    public function _PEAR_Common()
    {
        // doesn't work due to bug #14744
        //$tempfiles = $this->_tempfiles;
        $tempfiles =& $GLOBALS['_PEAR_Common_tempfiles'];
        while ($file = array_shift($tempfiles)) {
            if (@is_dir($file)) {
                if (!class_exists('System')) {
                    require_once 'System.php';
                }

                System::rm(['-rf', $file]);
            } elseif (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Register a temporary file or directory.  When the destructor is
     * executed, all registered temporary files and directories are
     * removed.
     *
     * @param string  $file  name of file or directory
     *
     * @return void
     *
     * @access public
     */
    public static function addTempFile($file)
    {
        if (!class_exists('PEAR_Frontend')) {
            require_once 'PEAR/Frontend.php';
        }
        PEAR_Frontend::addTempFile($file);
    }

    /**
     * Wrapper to System::mkDir(), creates a directory as well as
     * any necessary parent directories.
     *
     * @param string  $dir  directory name
     *
     * @return bool TRUE on success, or a PEAR error
     *
     * @access public
     */
    public function mkDirHier($dir)
    {
        // Only used in Installer - move it there ?
        $this->log(2, "+ create dir $dir");
        if (!class_exists('System')) {
            require_once 'System.php';
        }
        return System::mkDir(['-p', $dir]);
    }

    /**
     * Logging method.
     *
     * @param int    $level  log level (0 is quiet, higher is noisier)
     * @param string $msg    message to write to the log
     *
     * @return void
     */
    public function log($level, $msg, $append_crlf = true)
    {
        if ($this->debug >= $level) {
            if (!class_exists('PEAR_Frontend')) {
                require_once 'PEAR/Frontend.php';
            }

            $ui = &PEAR_Frontend::singleton();
            if (is_a($ui, 'PEAR_Frontend')) {
                $ui->log($msg, $append_crlf);
            } else {
                print "$msg\n";
            }
        }
    }

    /**
     * Create and register a temporary directory.
     *
     * @param string $tmpdir (optional) Directory to use as tmpdir.
     *                       Will use system defaults (for example
     *                       /tmp or c:\windows\temp) if not specified
     *
     * @return string name of created directory
     *
     * @access public
     */
    public function mkTempDir($tmpdir = '')
    {
        $topt = $tmpdir ? ['-t', $tmpdir] : [];
        $topt = array_merge($topt, ['-d', 'pear']);
        if (!class_exists('System')) {
            require_once 'System.php';
        }

        if (!$tmpdir = System::mktemp($topt)) {
            return false;
        }

        self::addTempFile($tmpdir);
        return $tmpdir;
    }

    /**
     * Set object that represents the frontend to be used.
     *
     * @param  object Reference of the frontend object
     * @return void
     * @access public
     */
    public function setFrontendObject(&$ui)
    {
        $this->ui = &$ui;
    }

    /**
     * Return an array containing all of the states that are more stable than
     * or equal to the passed in state
     *
     * @param string Release state
     * @param boolean Determines whether to include $state in the list
     * @return false|array False if $state is not a valid release state
     */
    public static function betterStates($state, $include = false)
    {
        static $states = ['snapshot', 'devel', 'alpha', 'beta', 'stable'];
        $i = array_search($state, $states);
        if ($i === false) {
            return false;
        }
        if ($include) {
            $i--;
        }
        return array_slice($states, $i + 1);
    }

    /**
     * Get the valid roles for a PEAR package maintainer
     *
     * @return array
     */
    public static function getUserRoles()
    {
        return $GLOBALS['_PEAR_Common_maintainer_roles'];
    }

    /**
     * Get the valid package release states of packages
     *
     * @return array
     */
    public static function getReleaseStates()
    {
        return $GLOBALS['_PEAR_Common_release_states'];
    }

    /**
     * Get the implemented dependency types (php, ext, pkg etc.)
     *
     * @return array
     */
    public static function getDependencyTypes()
    {
        return $GLOBALS['_PEAR_Common_dependency_types'];
    }

    /**
     * Get the implemented dependency relations (has, lt, ge etc.)
     *
     * @return array
     */
    public static function getDependencyRelations()
    {
        return $GLOBALS['_PEAR_Common_dependency_relations'];
    }

    /**
     * Get the implemented file roles
     *
     * @return array
     */
    public static function getFileRoles()
    {
        return $GLOBALS['_PEAR_Common_file_roles'];
    }

    /**
     * Get the implemented file replacement types in
     *
     * @return array
     */
    public static function getReplacementTypes()
    {
        return $GLOBALS['_PEAR_Common_replacement_types'];
    }

    /**
     * Get the implemented file replacement types in
     *
     * @return array
     */
    public static function getProvideTypes()
    {
        return $GLOBALS['_PEAR_Common_provide_types'];
    }

    /**
     * Get the implemented file replacement types in
     *
     * @return array
     */
    public static function getScriptPhases()
    {
        return $GLOBALS['_PEAR_Common_script_phases'];
    }

    /**
     * Test whether a string contains a valid package name.
     *
     * @param string $name the package name to test
     *
     * @return bool
     *
     * @access public
     */
    public function validPackageName($name)
    {
        return (bool)preg_match(PEAR_COMMON_PACKAGE_NAME_PREG, $name);
    }

    /**
     * Test whether a string contains a valid package version.
     *
     * @param string $ver the package version to test
     *
     * @return bool
     *
     * @access public
     */
    public function validPackageVersion($ver)
    {
        return (bool)preg_match(PEAR_COMMON_PACKAGE_VERSION_PREG, $ver);
    }

    /**
     * @param string $path relative or absolute include path
     * @return boolean
     */
    public static function isIncludeable($path)
    {
        if (file_exists($path) && is_readable($path)) {
            return true;
        }

        $ipath = explode(PATH_SEPARATOR, ini_get('include_path'));
        foreach ($ipath as $include) {
            $test = realpath($include . DIRECTORY_SEPARATOR . $path);
            if (file_exists($test) && is_readable($test)) {
                return true;
            }
        }

        return false;
    }

    public function _postProcessChecks($pf)
    {
        if (!PEAR::isError($pf)) {
            return $this->_postProcessValidPackagexml($pf);
        }

        $errs = $pf->getUserinfo();
        if (is_array($errs)) {
            foreach ($errs as $error) {
                $e = $this->raiseError($error['message'], $error['code'], null, null, $error);
            }
        }

        return $pf;
    }

    /**
     * Returns information about a package file.  Expects the name of
     * a gzipped tar file as input.
     *
     * @param string  $file  name of .tgz file
     *
     * @return array  array with package information
     *
     * @access public
     * @deprecated use PEAR_PackageFile->fromTgzFile() instead
     *
     */
    public function infoFromTgzFile($file)
    {
        $packagefile = new PEAR_PackageFile($this->config);
        $pf = &$packagefile->fromTgzFile($file, PEAR_VALIDATE_NORMAL);
        return $this->_postProcessChecks($pf);
    }

    /**
     * Returns information about a package file.  Expects the name of
     * a package xml file as input.
     *
     * @param string  $descfile  name of package xml file
     *
     * @return array  array with package information
     *
     * @access public
     * @deprecated use PEAR_PackageFile->fromPackageFile() instead
     *
     */
    public function infoFromDescriptionFile($descfile)
    {
        $packagefile = new PEAR_PackageFile($this->config);
        $pf = &$packagefile->fromPackageFile($descfile, PEAR_VALIDATE_NORMAL);
        return $this->_postProcessChecks($pf);
    }

    /**
     * Returns information about a package file.  Expects the contents
     * of a package xml file as input.
     *
     * @param string  $data  contents of package.xml file
     *
     * @return array   array with package information
     *
     * @access public
     * @deprecated use PEAR_PackageFile->fromXmlstring() instead
     *
     */
    public function infoFromString($data)
    {
        $packagefile = new PEAR_PackageFile($this->config);
        $pf = &$packagefile->fromXmlString($data, PEAR_VALIDATE_NORMAL, false);
        return $this->_postProcessChecks($pf);
    }

    /**
     * @param PEAR_PackageFile_v1|PEAR_PackageFile_v2
     * @return array
     */
    public function _postProcessValidPackagexml(&$pf)
    {
        if (!is_a($pf, 'PEAR_PackageFile_v2')) {
            $this->pkginfo = $pf->toArray();
            return $this->pkginfo;
        }

        // sort of make this into a package.xml 1.0-style array
        // changelog is not converted to old format.
        $arr = $pf->toArray(true);
        $arr = array_merge($arr, $arr['old']);
        unset($arr['old'], $arr['xsdversion'], $arr['contents'], $arr['compatible'],
              $arr['channel'], $arr['uri'], $arr['dependencies'], $arr['phprelease'],
              $arr['extsrcrelease'], $arr['zendextsrcrelease'], $arr['extbinrelease'],
              $arr['zendextbinrelease'], $arr['bundle'], $arr['lead'], $arr['developer'],
              $arr['helper'], $arr['contributor']);
        $arr['filelist'] = $pf->getFilelist();
        $this->pkginfo = $arr;
        return $arr;
    }

    /**
     * Returns package information from different sources
     *
     * This method is able to extract information about a package
     * from a .tgz archive or from a XML package definition file.
     *
     * @access public
     * @param  string Filename of the source ('package.xml', '<package>.tgz')
     * @return string
     * @deprecated use PEAR_PackageFile->fromAnyFile() instead
     */
    public function infoFromAny($info)
    {
        if (is_string($info) && file_exists($info)) {
            $packagefile = new PEAR_PackageFile($this->config);
            $pf = &$packagefile->fromAnyFile($info, PEAR_VALIDATE_NORMAL);
            if (PEAR::isError($pf)) {
                $errs = $pf->getUserinfo();
                if (is_array($errs)) {
                    foreach ($errs as $error) {
                        $e = $this->raiseError($error['message'], $error['code'], null, null, $error);
                    }
                }

                return $pf;
            }

            return $this->_postProcessValidPackagexml($pf);
        }

        return $info;
    }

    /**
     * Return an XML document based on the package info (as returned
     * by the PEAR_Common::infoFrom* methods).
     *
     * @param array  $pkginfo  package info
     *
     * @return string XML data
     *
     * @access public
     * @deprecated use a PEAR_PackageFile_v* object's generator instead
     */
    public function xmlFromInfo($pkginfo)
    {
        $config      = &PEAR_Config::singleton();
        $packagefile = new PEAR_PackageFile($config);
        $pf = &$packagefile->fromArray($pkginfo);
        $gen = &$pf->getDefaultGenerator();
        return $gen->toXml(PEAR_VALIDATE_PACKAGING);
    }

    /**
     * Validate XML package definition file.
     *
     * @param  string $info Filename of the package archive or of the
     *                package definition file
     * @param  array $errors Array that will contain the errors
     * @param  array $warnings Array that will contain the warnings
     * @param  string $dir_prefix (optional) directory where source files
     *                may be found, or empty if they are not available
     * @access public
     * @return boolean
     * @deprecated use the validation of PEAR_PackageFile objects
     */
    public function validatePackageInfo($info, &$errors, &$warnings, $dir_prefix = '')
    {
        $config      = &PEAR_Config::singleton();
        $packagefile = new PEAR_PackageFile($config);
        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        if (strpos($info, '<?xml') !== false) {
            $pf = &$packagefile->fromXmlString($info, PEAR_VALIDATE_NORMAL, '');
        } else {
            $pf = &$packagefile->fromAnyFile($info, PEAR_VALIDATE_NORMAL);
        }

        PEAR::staticPopErrorHandling();
        if (PEAR::isError($pf)) {
            $errs = $pf->getUserinfo();
            if (is_array($errs)) {
                foreach ($errs as $error) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Build a "provides" array from data returned by
     * analyzeSourceCode().  The format of the built array is like
     * this:
     *
     *  array(
     *    'class;MyClass' => 'array('type' => 'class', 'name' => 'MyClass'),
     *    ...
     *  )
     *
     *
     * @param array $srcinfo array with information about a source file
     * as returned by the analyzeSourceCode() method.
     *
     * @return void
     *
     * @access public
     *
     */
    public function buildProvidesArray($srcinfo)
    {
        $file = basename($srcinfo['source_file']);
        $pn = '';
        if (isset($this->_packageName)) {
            $pn = $this->_packageName;
        }

        $pnl = strlen($pn);
        foreach ($srcinfo['declared_classes'] as $class) {
            $key = "class;$class";
            if (isset($this->pkginfo['provides'][$key])) {
                continue;
            }

            $this->pkginfo['provides'][$key] =
                ['file'=> $file, 'type' => 'class', 'name' => $class];
            if (isset($srcinfo['inheritance'][$class])) {
                $this->pkginfo['provides'][$key]['extends'] =
                    $srcinfo['inheritance'][$class];
            }
        }

        foreach ($srcinfo['declared_methods'] as $class => $methods) {
            foreach ($methods as $method) {
                $function = "$class::$method";
                $key = "function;$function";
                if ($method[0] == '_' || !strcasecmp($method, $class) ||
                    isset($this->pkginfo['provides'][$key])) {
                    continue;
                }

                $this->pkginfo['provides'][$key] =
                    ['file'=> $file, 'type' => 'function', 'name' => $function];
            }
        }

        foreach ($srcinfo['declared_functions'] as $function) {
            $key = "function;$function";
            if ($function[0] == '_' || isset($this->pkginfo['provides'][$key])) {
                continue;
            }

            if (!strstr($function, '::') && strncasecmp($function, $pn, $pnl)) {
                $warnings[] = "in1 " . $file . ": function \"$function\" not prefixed with package name \"$pn\"";
            }

            $this->pkginfo['provides'][$key] =
                ['file'=> $file, 'type' => 'function', 'name' => $function];
        }
    }

    /**
     * Analyze the source code of the given PHP file
     *
     * @param  string Filename of the PHP file
     * @return mixed
     * @access public
     */
    public static function analyzeSourceCode($file)
    {
        if (!class_exists('PEAR_PackageFile_v2_Validator')) {
            require_once 'PEAR/PackageFile/v2/Validator.php';
        }

        $a = new PEAR_PackageFile_v2_Validator();
        return $a->analyzeSourceCode($file);
    }

    public function detectDependencies($any, $status_callback = null)
    {
        if (!function_exists("token_get_all")) {
            return false;
        }

        if (PEAR::isError($info = $this->infoFromAny($any))) {
            return $this->raiseError($info);
        }

        if (!is_array($info)) {
            return false;
        }

        $deps = [];
        $used_c = $decl_c = $decl_f = $decl_m = [];
        foreach ($info['filelist'] as $file => $fa) {
            $tmp = $this->analyzeSourceCode($file);
            $used_c = @array_merge($used_c, $tmp['used_classes']);
            $decl_c = @array_merge($decl_c, $tmp['declared_classes']);
            $decl_f = @array_merge($decl_f, $tmp['declared_functions']);
            $decl_m = @array_merge($decl_m, $tmp['declared_methods']);
            $inheri = @array_merge($inheri, $tmp['inheritance']);
        }

        $used_c = array_unique($used_c);
        $decl_c = array_unique($decl_c);
        $undecl_c = array_diff($used_c, $decl_c);

        return ['used_classes' => $used_c,
                     'declared_classes' => $decl_c,
                     'declared_methods' => $decl_m,
                     'declared_functions' => $decl_f,
                     'undeclared_classes' => $undecl_c,
                     'inheritance' => $inheri,
                     ];
    }

    /**
     * Download a file through HTTP.  Considers suggested file name in
     * Content-disposition: header and can run a callback function for
     * different events.  The callback will be called with two
     * parameters: the callback type, and parameters.  The implemented
     * callback types are:
     *
     *  'setup'       called at the very beginning, parameter is a UI object
     *                that should be used for all output
     *  'message'     the parameter is a string with an informational message
     *  'saveas'      may be used to save with a different file name, the
     *                parameter is the filename that is about to be used.
     *                If a 'saveas' callback returns a non-empty string,
     *                that file name will be used as the filename instead.
     *                Note that $save_dir will not be affected by this, only
     *                the basename of the file.
     *  'start'       download is starting, parameter is number of bytes
     *                that are expected, or -1 if unknown
     *  'bytesread'   parameter is the number of bytes read so far
     *  'done'        download is complete, parameter is the total number
     *                of bytes read
     *  'connfailed'  if the TCP connection fails, this callback is called
     *                with array(host,port,errno,errmsg)
     *  'writefailed' if writing to disk fails, this callback is called
     *                with array(destfile,errmsg)
     *
     * If an HTTP proxy has been configured (http_proxy PEAR_Config
     * setting), the proxy will be used.
     *
     * @param string  $url       the URL to download
     * @param object  $ui        PEAR_Frontend_* instance
     * @param object  $config    PEAR_Config instance
     * @param string  $save_dir  (optional) directory to save file in
     * @param mixed   $callback  (optional) function/method to call for status
     *                           updates
     * @param false|string|array $lastmodified header values to check against
     *                                         for caching
     *                                         use false to return the header
     *                                         values from this download
     * @param false|array        $accept       Accept headers to send
     * @param false|string       $channel      Channel to use for retrieving
     *                                         authentication
     *
     * @return mixed  Returns the full path of the downloaded file or a PEAR
     *                error on failure.  If the error is caused by
     *                socket-related errors, the error object will
     *                have the fsockopen error code available through
     *                getCode().  If caching is requested, then return the header
     *                values.
     *                If $lastmodified was given and the there are no changes,
     *                boolean false is returned.
     *
     * @access public
     */
    public function downloadHttp(
        $url,
        &$ui,
        $save_dir = '.',
        $callback = null,
        $lastmodified = null,
        $accept = false,
        $channel = false
    ) {
        if (!class_exists('PEAR_Downloader')) {
            require_once 'PEAR/Downloader.php';
        }
        return PEAR_Downloader::_downloadHttp(
            $this,
            $url,
            $ui,
            $save_dir,
            $callback,
            $lastmodified,
            $accept,
            $channel
        );
    }
}

require_once 'PEAR/Config.php';
require_once 'PEAR/PackageFile.php';
