<?php
/**
 * PEAR_PackageFile_v2, package.xml version 2.0, read/write version
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 1.4.0a8
 */
/**
 * For base class
 */
require_once 'PEAR/PackageFile/v2.php';
/**
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: 1.10.12
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a8
 */
class PEAR_PackageFile_v2_rw extends PEAR_PackageFile_v2
{
    /**
     * @param string Extension name
     * @return bool success of operation
     */
    public function setProvidesExtension($extension)
    {
        if (in_array(
            $this->getPackageType(),
            ['extsrc', 'extbin', 'zendextsrc', 'zendextbin']
        )) {
            if (!isset($this->_packageInfo['providesextension'])) {
                // ensure that the channel tag is set up in the right location
                $this->_packageInfo = $this->_insertBefore(
                    $this->_packageInfo,
                    ['usesrole', 'usestask', 'srcpackage', 'srcuri', 'phprelease',
                    'extsrcrelease', 'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                    'bundle', 'changelog'],
                    $extension,
                    'providesextension'
                );
            }
            $this->_packageInfo['providesextension'] = $extension;
            return true;
        }
        return false;
    }

    public function setPackage($package)
    {
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['attribs'])) {
            $this->_packageInfo = array_merge(['attribs' => [
                                 'version' => '2.0',
                                 'xmlns' => 'http://pear.php.net/dtd/package-2.0',
                                 'xmlns:tasks' => 'http://pear.php.net/dtd/tasks-1.0',
                                 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                                 'xsi:schemaLocation' => 'http://pear.php.net/dtd/tasks-1.0
    http://pear.php.net/dtd/tasks-1.0.xsd
    http://pear.php.net/dtd/package-2.0
    http://pear.php.net/dtd/package-2.0.xsd',
                             ]], $this->_packageInfo);
        }
        if (!isset($this->_packageInfo['name'])) {
            return $this->_packageInfo = array_merge(
                ['name' => $package],
                $this->_packageInfo
            );
        }
        $this->_packageInfo['name'] = $package;
    }

    /**
     * set this as a package.xml version 2.1
     * @access private
     */
    public function _setPackageVersion2_1()
    {
        $info = [
                                 'version' => '2.1',
                                 'xmlns' => 'http://pear.php.net/dtd/package-2.1',
                                 'xmlns:tasks' => 'http://pear.php.net/dtd/tasks-1.0',
                                 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                                 'xsi:schemaLocation' => 'http://pear.php.net/dtd/tasks-1.0
    http://pear.php.net/dtd/tasks-1.0.xsd
    http://pear.php.net/dtd/package-2.1
    http://pear.php.net/dtd/package-2.1.xsd',
                             ];
        if (!isset($this->_packageInfo['attribs'])) {
            $this->_packageInfo = array_merge(['attribs' => $info], $this->_packageInfo);
        } else {
            $this->_packageInfo['attribs'] = $info;
        }
    }

    public function setUri($uri)
    {
        unset($this->_packageInfo['channel']);
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['uri'])) {
            // ensure that the uri tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['extends', 'summary', 'description', 'lead',
                'developer', 'contributor', 'helper', 'date', 'time', 'version',
                'stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $uri,
                'uri'
            );
        }
        $this->_packageInfo['uri'] = $uri;
    }

    public function setChannel($channel)
    {
        unset($this->_packageInfo['uri']);
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['channel'])) {
            // ensure that the channel tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['extends', 'summary', 'description', 'lead',
                'developer', 'contributor', 'helper', 'date', 'time', 'version',
                'stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $channel,
                'channel'
            );
        }
        $this->_packageInfo['channel'] = $channel;
    }

    public function setExtends($extends)
    {
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['extends'])) {
            // ensure that the extends tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['summary', 'description', 'lead',
                'developer', 'contributor', 'helper', 'date', 'time', 'version',
                'stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $extends,
                'extends'
            );
        }
        $this->_packageInfo['extends'] = $extends;
    }

    public function setSummary($summary)
    {
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['summary'])) {
            // ensure that the summary tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['description', 'lead',
                'developer', 'contributor', 'helper', 'date', 'time', 'version',
                'stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $summary,
                'summary'
            );
        }
        $this->_packageInfo['summary'] = $summary;
    }

    public function setDescription($desc)
    {
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['description'])) {
            // ensure that the description tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['lead',
                'developer', 'contributor', 'helper', 'date', 'time', 'version',
                'stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $desc,
                'description'
            );
        }
        $this->_packageInfo['description'] = $desc;
    }

    /**
     * Adds a new maintainer - no checking of duplicates is performed, use
     * updatemaintainer for that purpose.
     */
    public function addMaintainer($role, $handle, $name, $email, $active = 'yes')
    {
        if (!in_array($role, ['lead', 'developer', 'contributor', 'helper'])) {
            return false;
        }
        if (isset($this->_packageInfo[$role])) {
            if (!isset($this->_packageInfo[$role][0])) {
                $this->_packageInfo[$role] = [$this->_packageInfo[$role]];
            }
            $this->_packageInfo[$role][] =
                [
                    'name' => $name,
                    'user' => $handle,
                    'email' => $email,
                    'active' => $active,
                ];
        } else {
            $testarr = ['lead',
                    'developer', 'contributor', 'helper', 'date', 'time', 'version',
                    'stability', 'license', 'notes', 'contents', 'compatible',
                    'dependencies', 'providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease',
                    'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'];
            foreach (['lead', 'developer', 'contributor', 'helper'] as $testrole) {
                array_shift($testarr);
                if ($role == $testrole) {
                    break;
                }
            }
            if (!isset($this->_packageInfo[$role])) {
                // ensure that the extends tag is set up in the right location
                $this->_packageInfo = $this->_insertBefore(
                    $this->_packageInfo,
                    $testarr,
                    [],
                    $role
                );
            }
            $this->_packageInfo[$role] =
                [
                    'name' => $name,
                    'user' => $handle,
                    'email' => $email,
                    'active' => $active,
                ];
        }
        $this->_isValid = 0;
    }

    public function updateMaintainer($newrole, $handle, $name, $email, $active = 'yes')
    {
        $found = false;
        foreach (['lead', 'developer', 'contributor', 'helper'] as $role) {
            if (!isset($this->_packageInfo[$role])) {
                continue;
            }
            $info = $this->_packageInfo[$role];
            if (!isset($info[0])) {
                if ($info['user'] == $handle) {
                    $found = true;
                    break;
                }
            }
            foreach ($info as $i => $maintainer) {
                if (is_array($maintainer) && $maintainer['user'] == $handle) {
                    $found = $i;
                    break 2;
                }
            }
        }
        if ($found === false) {
            return $this->addMaintainer($newrole, $handle, $name, $email, $active);
        }
        if ($found !== false) {
            if ($found === true) {
                unset($this->_packageInfo[$role]);
            } else {
                unset($this->_packageInfo[$role][$found]);
                $this->_packageInfo[$role] = array_values($this->_packageInfo[$role]);
            }
        }
        $this->addMaintainer($newrole, $handle, $name, $email, $active);
        $this->_isValid = 0;
    }

    public function deleteMaintainer($handle)
    {
        $found = false;
        foreach (['lead', 'developer', 'contributor', 'helper'] as $role) {
            if (!isset($this->_packageInfo[$role])) {
                continue;
            }
            if (!isset($this->_packageInfo[$role][0])) {
                $this->_packageInfo[$role] = [$this->_packageInfo[$role]];
            }
            foreach ($this->_packageInfo[$role] as $i => $maintainer) {
                if ($maintainer['user'] == $handle) {
                    $found = $i;
                    break;
                }
            }
            if ($found !== false) {
                unset($this->_packageInfo[$role][$found]);
                if (!count($this->_packageInfo[$role]) && $role == 'lead') {
                    $this->_isValid = 0;
                }
                if (!count($this->_packageInfo[$role])) {
                    unset($this->_packageInfo[$role]);
                    return true;
                }
                $this->_packageInfo[$role] =
                    array_values($this->_packageInfo[$role]);
                if (count($this->_packageInfo[$role]) == 1) {
                    $this->_packageInfo[$role] = $this->_packageInfo[$role][0];
                }
                return true;
            }
            if (count($this->_packageInfo[$role]) == 1) {
                $this->_packageInfo[$role] = $this->_packageInfo[$role][0];
            }
        }
        return false;
    }

    public function setReleaseVersion($version)
    {
        if (isset($this->_packageInfo['version']) &&
              isset($this->_packageInfo['version']['release'])) {
            unset($this->_packageInfo['version']['release']);
        }
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $version, [
            'version' => ['stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
            'release' => ['api']]);
        $this->_isValid = 0;
    }

    public function setAPIVersion($version)
    {
        if (isset($this->_packageInfo['version']) &&
              isset($this->_packageInfo['version']['api'])) {
            unset($this->_packageInfo['version']['api']);
        }
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $version, [
            'version' => ['stability', 'license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
            'api' => []]);
        $this->_isValid = 0;
    }

    /**
     * snapshot|devel|alpha|beta|stable
     */
    public function setReleaseStability($state)
    {
        if (isset($this->_packageInfo['stability']) &&
              isset($this->_packageInfo['stability']['release'])) {
            unset($this->_packageInfo['stability']['release']);
        }
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $state, [
            'stability' => ['license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
            'release' => ['api']]);
        $this->_isValid = 0;
    }

    /**
     * @param devel|alpha|beta|stable
     */
    public function setAPIStability($state)
    {
        if (isset($this->_packageInfo['stability']) &&
              isset($this->_packageInfo['stability']['api'])) {
            unset($this->_packageInfo['stability']['api']);
        }
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $state, [
            'stability' => ['license', 'notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
            'api' => []]);
        $this->_isValid = 0;
    }

    public function setLicense($license, $uri = false, $filesource = false)
    {
        if (!isset($this->_packageInfo['license'])) {
            // ensure that the license tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['notes', 'contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                0,
                'license'
            );
        }
        if ($uri || $filesource) {
            $attribs = [];
            if ($uri) {
                $attribs['uri'] = $uri;
            }
            $uri = true; // for test below
            if ($filesource) {
                $attribs['filesource'] = $filesource;
            }
        }
        $license = $uri ? ['attribs' => $attribs, '_content' => $license] : $license;
        $this->_packageInfo['license'] = $license;
        $this->_isValid = 0;
    }

    public function setNotes($notes)
    {
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['notes'])) {
            // ensure that the notes tag is set up in the right location
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['contents', 'compatible',
                'dependencies', 'providesextension', 'usesrole', 'usestask', 'srcpackage', 'srcuri',
                'phprelease', 'extsrcrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'extbinrelease', 'bundle', 'changelog'],
                $notes,
                'notes'
            );
        }
        $this->_packageInfo['notes'] = $notes;
    }

    /**
     * This is only used at install-time, after all serialization
     * is over.
     * @param string file name
     * @param string installed path
     */
    public function setInstalledAs($file, $path)
    {
        if ($path) {
            return $this->_packageInfo['filelist'][$file]['installed_as'] = $path;
        }
        unset($this->_packageInfo['filelist'][$file]['installed_as']);
    }

    /**
     * This is only used at install-time, after all serialization
     * is over.
     */
    public function installedFile($file, $atts)
    {
        if (isset($this->_packageInfo['filelist'][$file])) {
            $this->_packageInfo['filelist'][$file] =
                array_merge($this->_packageInfo['filelist'][$file], $atts['attribs']);
        } else {
            $this->_packageInfo['filelist'][$file] = $atts['attribs'];
        }
    }

    /**
     * Reset the listing of package contents
     * @param string base installation dir for the whole package, if any
     */
    public function clearContents($baseinstall = false)
    {
        $this->_filesValid = false;
        $this->_isValid = 0;
        if (!isset($this->_packageInfo['contents'])) {
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['compatible',
                    'dependencies', 'providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease',
                    'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                    'bundle', 'changelog'],
                [],
                'contents'
            );
        }
        if ($this->getPackageType() != 'bundle') {
            $this->_packageInfo['contents'] =
                ['dir' => ['attribs' => ['name' => '/']]];
            if ($baseinstall) {
                $this->_packageInfo['contents']['dir']['attribs']['baseinstalldir'] = $baseinstall;
            }
        } else {
            $this->_packageInfo['contents'] = ['bundledpackage' => []];
        }
    }

    /**
     * @param string relative path of the bundled package.
     */
    public function addBundledPackage($path)
    {
        if ($this->getPackageType() != 'bundle') {
            return false;
        }
        $this->_filesValid = false;
        $this->_isValid = 0;
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $path, [
                'contents' => ['compatible', 'dependencies', 'providesextension',
                'usesrole', 'usestask', 'srcpackage', 'srcuri', 'phprelease',
                'extsrcrelease', 'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'bundle', 'changelog'],
                'bundledpackage' => []]);
    }

    /**
     * @param string file name
     * @param PEAR_Task_Common a read/write task
     */
    public function addTaskToFile($filename, $task)
    {
        if (!method_exists($task, 'getXml')) {
            return false;
        }
        if (!method_exists($task, 'getName')) {
            return false;
        }
        if (!method_exists($task, 'validate')) {
            return false;
        }
        if (!$task->validate()) {
            return false;
        }
        if (!isset($this->_packageInfo['contents']['dir']['file'])) {
            return false;
        }
        $this->getTasksNs(); // discover the tasks namespace if not done already
        $files = $this->_packageInfo['contents']['dir']['file'];
        if (!isset($files[0])) {
            $files = [$files];
            $ind = false;
        } else {
            $ind = true;
        }
        foreach ($files as $i => $file) {
            if (isset($file['attribs'])) {
                if ($file['attribs']['name'] == $filename) {
                    if ($ind) {
                        $t = isset($this->_packageInfo['contents']['dir']['file'][$i]
                              ['attribs'][$this->_tasksNs .
                              ':' . $task->getName()]) ?
                              $this->_packageInfo['contents']['dir']['file'][$i]
                              ['attribs'][$this->_tasksNs .
                              ':' . $task->getName()] : false;
                        if ($t && !isset($t[0])) {
                            $this->_packageInfo['contents']['dir']['file'][$i]
                                [$this->_tasksNs . ':' . $task->getName()] = [$t];
                        }
                        $this->_packageInfo['contents']['dir']['file'][$i][$this->_tasksNs .
                            ':' . $task->getName()][] = $task->getXml();
                    } else {
                        $t = isset($this->_packageInfo['contents']['dir']['file']
                              ['attribs'][$this->_tasksNs .
                              ':' . $task->getName()]) ? $this->_packageInfo['contents']['dir']['file']
                              ['attribs'][$this->_tasksNs .
                              ':' . $task->getName()] : false;
                        if ($t && !isset($t[0])) {
                            $this->_packageInfo['contents']['dir']['file']
                                [$this->_tasksNs . ':' . $task->getName()] = [$t];
                        }
                        $this->_packageInfo['contents']['dir']['file'][$this->_tasksNs .
                            ':' . $task->getName()][] = $task->getXml();
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string path to the file
     * @param string filename
     * @param array extra attributes
     */
    public function addFile($dir, $file, $attrs)
    {
        if ($this->getPackageType() == 'bundle') {
            return false;
        }
        $this->_filesValid = false;
        $this->_isValid = 0;
        $dir = preg_replace(['!\\\\+!', '!/+!'], ['/', '/'], $dir);
        if ($dir == '/' || $dir == '') {
            $dir = '';
        } else {
            $dir .= '/';
        }
        $attrs['name'] = $dir . $file;
        if (!isset($this->_packageInfo['contents'])) {
            // ensure that the contents tag is set up
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['compatible', 'dependencies', 'providesextension', 'usesrole', 'usestask',
                'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease',
                'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'bundle', 'changelog'],
                [],
                'contents'
            );
        }
        if (isset($this->_packageInfo['contents']['dir']['file'])) {
            if (!isset($this->_packageInfo['contents']['dir']['file'][0])) {
                $this->_packageInfo['contents']['dir']['file'] =
                    [$this->_packageInfo['contents']['dir']['file']];
            }
            $this->_packageInfo['contents']['dir']['file'][]['attribs'] = $attrs;
        } else {
            $this->_packageInfo['contents']['dir']['file']['attribs'] = $attrs;
        }
    }

    /**
     * @param string Dependent package name
     * @param string Dependent package's channel name
     * @param string minimum version of specified package that this release is guaranteed to be
     *               compatible with
     * @param string maximum version of specified package that this release is guaranteed to be
     *               compatible with
     * @param string versions of specified package that this release is not compatible with
     */
    public function addCompatiblePackage($name, $channel, $min, $max, $exclude = false)
    {
        $this->_isValid = 0;
        $set = [
            'name' => $name,
            'channel' => $channel,
            'min' => $min,
            'max' => $max,
        ];
        if ($exclude) {
            $set['exclude'] = $exclude;
        }
        $this->_isValid = 0;
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $set, [
                'compatible' => ['dependencies', 'providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog']
            ]);
    }

    /**
     * Removes the <usesrole> tag entirely
     */
    public function resetUsesrole()
    {
        if (isset($this->_packageInfo['usesrole'])) {
            unset($this->_packageInfo['usesrole']);
        }
    }

    /**
     * @param string
     * @param string package name or uri
     * @param string channel name if non-uri
     */
    public function addUsesrole($role, $packageOrUri, $channel = false)
    {
        $set = ['role' => $role];
        if ($channel) {
            $set['package'] = $packageOrUri;
            $set['channel'] = $channel;
        } else {
            $set['uri'] = $packageOrUri;
        }
        $this->_isValid = 0;
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $set, [
                'usesrole' => ['usestask', 'srcpackage', 'srcuri',
                    'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog']
            ]);
    }

    /**
     * Removes the <usestask> tag entirely
     */
    public function resetUsestask()
    {
        if (isset($this->_packageInfo['usestask'])) {
            unset($this->_packageInfo['usestask']);
        }
    }


    /**
     * @param string
     * @param string package name or uri
     * @param string channel name if non-uri
     */
    public function addUsestask($task, $packageOrUri, $channel = false)
    {
        $set = ['task' => $task];
        if ($channel) {
            $set['package'] = $packageOrUri;
            $set['channel'] = $channel;
        } else {
            $set['uri'] = $packageOrUri;
        }
        $this->_isValid = 0;
        $this->_packageInfo = $this->_mergeTag($this->_packageInfo, $set, [
                'usestask' => ['srcpackage', 'srcuri',
                    'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog']
            ]);
    }

    /**
     * Remove all compatible tags
     */
    public function clearCompatible()
    {
        unset($this->_packageInfo['compatible']);
    }

    /**
     * Reset dependencies prior to adding new ones
     */
    public function clearDeps()
    {
        if (!isset($this->_packageInfo['dependencies'])) {
            $this->_packageInfo = $this->_mergeTag(
                $this->_packageInfo,
                [],
                [
                    'dependencies' => ['providesextension', 'usesrole', 'usestask',
                        'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                        'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog']]
            );
        }
        $this->_packageInfo['dependencies'] = [];
    }

    /**
     * @param string minimum PHP version allowed
     * @param string maximum PHP version allowed
     * @param array $exclude incompatible PHP versions
     */
    public function setPhpDep($min, $max = false, $exclude = false)
    {
        $this->_isValid = 0;
        $dep =
            [
                'min' => $min,
            ];
        if ($max) {
            $dep['max'] = $max;
        }
        if ($exclude) {
            if (count($exclude) == 1) {
                $exclude = $exclude[0];
            }
            $dep['exclude'] = $exclude;
        }
        if (isset($this->_packageInfo['dependencies']['required']['php'])) {
            $this->_stack->push(
                __FUNCTION__,
                'warning',
                ['dep' =>
            $this->_packageInfo['dependencies']['required']['php']],
                'warning: PHP dependency already exists, overwriting'
            );
            unset($this->_packageInfo['dependencies']['required']['php']);
        }
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'php' => ['pearinstaller', 'package', 'subpackage', 'extension', 'os', 'arch']
            ]
        );
        return true;
    }

    /**
     * @param string minimum allowed PEAR installer version
     * @param string maximum allowed PEAR installer version
     * @param string recommended PEAR installer version
     * @param array incompatible version of the PEAR installer
     */
    public function setPearinstallerDep($min, $max = false, $recommended = false, $exclude = false)
    {
        $this->_isValid = 0;
        $dep =
            [
                'min' => $min,
            ];
        if ($max) {
            $dep['max'] = $max;
        }
        if ($recommended) {
            $dep['recommended'] = $recommended;
        }
        if ($exclude) {
            if (count($exclude) == 1) {
                $exclude = $exclude[0];
            }
            $dep['exclude'] = $exclude;
        }
        if (isset($this->_packageInfo['dependencies']['required']['pearinstaller'])) {
            $this->_stack->push(
                __FUNCTION__,
                'warning',
                ['dep' =>
            $this->_packageInfo['dependencies']['required']['pearinstaller']],
                'warning: PEAR Installer dependency already exists, overwriting'
            );
            unset($this->_packageInfo['dependencies']['required']['pearinstaller']);
        }
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'pearinstaller' => ['package', 'subpackage', 'extension', 'os', 'arch']
            ]
        );
    }

    /**
     * Mark a package as conflicting with this package
     * @param string package name
     * @param string package channel
     * @param string extension this package provides, if any
     * @param string|false minimum version required
     * @param string|false maximum version allowed
     * @param array|false versions to exclude from installation
     */
    public function addConflictingPackageDepWithChannel(
        $name,
        $channel,
        $providesextension = false,
        $min = false,
        $max = false,
        $exclude = false
    ) {
        $this->_isValid = 0;
        $dep = $this->_constructDep(
            $name,
            $channel,
            false,
            $min,
            $max,
            false,
            $exclude,
            $providesextension,
            false,
            true
        );
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'package' => ['subpackage', 'extension', 'os', 'arch']
            ]
        );
    }

    /**
     * Mark a package as conflicting with this package
     * @param string package name
     * @param string package channel
     * @param string extension this package provides, if any
     */
    public function addConflictingPackageDepWithUri($name, $uri, $providesextension = false)
    {
        $this->_isValid = 0;
        $dep =
            [
                'name' => $name,
                'uri' => $uri,
                'conflicts' => '',
            ];
        if ($providesextension) {
            $dep['providesextension'] = $providesextension;
        }
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'package' => ['subpackage', 'extension', 'os', 'arch']
            ]
        );
    }

    public function addDependencyGroup($name, $hint)
    {
        $this->_isValid = 0;
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            ['attribs' => ['name' => $name, 'hint' => $hint]],
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'group' => [],
            ]
        );
    }

    /**
     * @param string package name
     * @param string|false channel name, false if this is a uri
     * @param string|false uri name, false if this is a channel
     * @param string|false minimum version required
     * @param string|false maximum version allowed
     * @param string|false recommended installation version
     * @param array|false versions to exclude from installation
     * @param string extension this package provides, if any
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     * @param bool if true, tells the installer to negate this dependency (conflicts)
     * @return array
     * @access private
     */
    public function _constructDep(
        $name,
        $channel,
        $uri,
        $min,
        $max,
        $recommended,
        $exclude,
        $providesextension = false,
        $nodefault = false,
        $conflicts = false
    ) {
        $dep =
            [
                'name' => $name,
            ];
        if ($channel) {
            $dep['channel'] = $channel;
        } elseif ($uri) {
            $dep['uri'] = $uri;
        }
        if ($min) {
            $dep['min'] = $min;
        }
        if ($max) {
            $dep['max'] = $max;
        }
        if ($recommended) {
            $dep['recommended'] = $recommended;
        }
        if ($exclude) {
            if (is_array($exclude) && count($exclude) == 1) {
                $exclude = $exclude[0];
            }
            $dep['exclude'] = $exclude;
        }
        if ($conflicts) {
            $dep['conflicts'] = '';
        }
        if ($nodefault) {
            $dep['nodefault'] = '';
        }
        if ($providesextension) {
            $dep['providesextension'] = $providesextension;
        }
        return $dep;
    }

    /**
     * @param package|subpackage
     * @param string group name
     * @param string package name
     * @param string package channel
     * @param string minimum version
     * @param string maximum version
     * @param string recommended version
     * @param array|false optional excluded versions
     * @param string extension this package provides, if any
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     * @return bool false if the dependency group has not been initialized with
     *              {@link addDependencyGroup()}, or a subpackage is added with
     *              a providesextension
     */
    public function addGroupPackageDepWithChannel(
        $type,
        $groupname,
        $name,
        $channel,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false,
        $providesextension = false,
        $nodefault = false
    ) {
        if ($type == 'subpackage' && $providesextension) {
            return false; // subpackages must be php packages
        }
        $dep = $this->_constructDep(
            $name,
            $channel,
            false,
            $min,
            $max,
            $recommended,
            $exclude,
            $providesextension,
            $nodefault
        );
        return $this->_addGroupDependency($type, $dep, $groupname);
    }

    /**
     * @param package|subpackage
     * @param string group name
     * @param string package name
     * @param string package uri
     * @param string extension this package provides, if any
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     * @return bool false if the dependency group has not been initialized with
     *              {@link addDependencyGroup()}
     */
    public function addGroupPackageDepWithURI(
        $type,
        $groupname,
        $name,
        $uri,
        $providesextension = false,
        $nodefault = false
    ) {
        if ($type == 'subpackage' && $providesextension) {
            return false; // subpackages must be php packages
        }
        $dep = $this->_constructDep(
            $name,
            false,
            $uri,
            false,
            false,
            false,
            false,
            $providesextension,
            $nodefault
        );
        return $this->_addGroupDependency($type, $dep, $groupname);
    }

    /**
     * @param string group name (must be pre-existing)
     * @param string extension name
     * @param string minimum version allowed
     * @param string maximum version allowed
     * @param string recommended version
     * @param array incompatible versions
     */
    public function addGroupExtensionDep(
        $groupname,
        $name,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false
    ) {
        $this->_isValid = 0;
        $dep = $this->_constructDep($name, false, false, $min, $max, $recommended, $exclude);
        return $this->_addGroupDependency('extension', $dep, $groupname);
    }

    /**
     * @param package|subpackage|extension
     * @param array dependency contents
     * @param string name of the dependency group to add this to
     * @return boolean
     * @access private
     */
    public function _addGroupDependency($type, $dep, $groupname)
    {
        $arr = ['subpackage', 'extension'];
        if ($type != 'package') {
            array_shift($arr);
        }
        if ($type == 'extension') {
            array_shift($arr);
        }
        if (!isset($this->_packageInfo['dependencies']['group'])) {
            return false;
        } else {
            if (!isset($this->_packageInfo['dependencies']['group'][0])) {
                if ($this->_packageInfo['dependencies']['group']['attribs']['name'] == $groupname) {
                    $this->_packageInfo['dependencies']['group'] = $this->_mergeTag(
                        $this->_packageInfo['dependencies']['group'],
                        $dep,
                        [
                            $type => $arr
                        ]
                    );
                    $this->_isValid = 0;
                    return true;
                } else {
                    return false;
                }
            } else {
                foreach ($this->_packageInfo['dependencies']['group'] as $i => $group) {
                    if ($group['attribs']['name'] == $groupname) {
                        $this->_packageInfo['dependencies']['group'][$i] = $this->_mergeTag(
                            $this->_packageInfo['dependencies']['group'][$i],
                            $dep,
                            [
                            $type => $arr
                        ]
                        );
                        $this->_isValid = 0;
                        return true;
                    }
                }
                return false;
            }
        }
    }

    /**
     * @param optional|required
     * @param string package name
     * @param string package channel
     * @param string minimum version
     * @param string maximum version
     * @param string recommended version
     * @param string extension this package provides, if any
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     * @param array|false optional excluded versions
     */
    public function addPackageDepWithChannel(
        $type,
        $name,
        $channel,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false,
        $providesextension = false,
        $nodefault = false
    ) {
        if (!in_array($type, ['optional', 'required'], true)) {
            $type = 'required';
        }
        $this->_isValid = 0;
        $arr = ['optional', 'group'];
        if ($type != 'required') {
            array_shift($arr);
        }
        $dep = $this->_constructDep(
            $name,
            $channel,
            false,
            $min,
            $max,
            $recommended,
            $exclude,
            $providesextension,
            $nodefault
        );
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                $type => $arr,
                'package' => ['subpackage', 'extension', 'os', 'arch']
            ]
        );
    }

    /**
     * @param optional|required
     * @param string name of the package
     * @param string uri of the package
     * @param string extension this package provides, if any
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     */
    public function addPackageDepWithUri(
        $type,
        $name,
        $uri,
        $providesextension = false,
        $nodefault = false
    ) {
        $this->_isValid = 0;
        $arr = ['optional', 'group'];
        if ($type != 'required') {
            array_shift($arr);
        }
        $dep = $this->_constructDep(
            $name,
            false,
            $uri,
            false,
            false,
            false,
            false,
            $providesextension,
            $nodefault
        );
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                $type => $arr,
                'package' => ['subpackage', 'extension', 'os', 'arch']
            ]
        );
    }

    /**
     * @param optional|required optional, required
     * @param string package name
     * @param string package channel
     * @param string minimum version
     * @param string maximum version
     * @param string recommended version
     * @param array incompatible versions
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     */
    public function addSubpackageDepWithChannel(
        $type,
        $name,
        $channel,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false,
        $nodefault = false
    ) {
        $this->_isValid = 0;
        $arr = ['optional', 'group'];
        if ($type != 'required') {
            array_shift($arr);
        }
        $dep = $this->_constructDep(
            $name,
            $channel,
            false,
            $min,
            $max,
            $recommended,
            $exclude,
            $nodefault
        );
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                $type => $arr,
                'subpackage' => ['extension', 'os', 'arch']
            ]
        );
    }

    /**
     * @param optional|required optional, required
     * @param string package name
     * @param string package uri for download
     * @param bool if true, tells the installer to ignore the default optional dependency group
     *             when installing this package
     */
    public function addSubpackageDepWithUri($type, $name, $uri, $nodefault = false)
    {
        $this->_isValid = 0;
        $arr = ['optional', 'group'];
        if ($type != 'required') {
            array_shift($arr);
        }
        $dep = $this->_constructDep($name, false, $uri, false, false, false, false, $nodefault);
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                $type => $arr,
                'subpackage' => ['extension', 'os', 'arch']
            ]
        );
    }

    /**
     * @param optional|required optional, required
     * @param string extension name
     * @param string minimum version
     * @param string maximum version
     * @param string recommended version
     * @param array incompatible versions
     */
    public function addExtensionDep(
        $type,
        $name,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false
    ) {
        $this->_isValid = 0;
        $arr = ['optional', 'group'];
        if ($type != 'required') {
            array_shift($arr);
        }
        $dep = $this->_constructDep($name, false, false, $min, $max, $recommended, $exclude);
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                $type => $arr,
                'extension' => ['os', 'arch']
            ]
        );
    }

    /**
     * @param string Operating system name
     * @param boolean true if this package cannot be installed on this OS
     */
    public function addOsDep($name, $conflicts = false)
    {
        $this->_isValid = 0;
        $dep = ['name' => $name];
        if ($conflicts) {
            $dep['conflicts'] = '';
        }
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'os' => ['arch']
            ]
        );
    }

    /**
     * @param string Architecture matching pattern
     * @param boolean true if this package cannot be installed on this architecture
     */
    public function addArchDep($pattern, $conflicts = false)
    {
        $this->_isValid = 0;
        $dep = ['pattern' => $pattern];
        if ($conflicts) {
            $dep['conflicts'] = '';
        }
        $this->_packageInfo = $this->_mergeTag(
            $this->_packageInfo,
            $dep,
            [
                'dependencies' => ['providesextension', 'usesrole', 'usestask',
                    'srcpackage', 'srcuri', 'phprelease', 'extsrcrelease', 'extbinrelease',
                    'zendextsrcrelease', 'zendextbinrelease', 'bundle', 'changelog'],
                'required' => ['optional', 'group'],
                'arch' => []
            ]
        );
    }

    /**
     * Set the kind of package, and erase all release tags
     *
     * - a php package is a PEAR-style package
     * - an extbin package is a PECL-style extension binary
     * - an extsrc package is a PECL-style source for a binary
     * - an zendextbin package is a PECL-style zend extension binary
     * - an zendextsrc package is a PECL-style source for a zend extension binary
     * - a bundle package is a collection of other pre-packaged packages
     * @param php|extbin|extsrc|zendextsrc|zendextbin|bundle
     * @return bool success
     */
    public function setPackageType($type)
    {
        $this->_isValid = 0;
        if (!in_array($type, ['php', 'extbin', 'extsrc', 'zendextsrc',
                                   'zendextbin', 'bundle'])) {
            return false;
        }

        if (in_array($type, ['zendextsrc', 'zendextbin'])) {
            $this->_setPackageVersion2_1();
        }

        if ($type != 'bundle') {
            $type .= 'release';
        }

        foreach (['phprelease', 'extbinrelease', 'extsrcrelease',
                       'zendextsrcrelease', 'zendextbinrelease', 'bundle'] as $test) {
            unset($this->_packageInfo[$test]);
        }

        if (!isset($this->_packageInfo[$type])) {
            // ensure that the release tag is set up
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['changelog'],
                [],
                $type
            );
        }

        $this->_packageInfo[$type] = [];
        return true;
    }

    /**
     * @return bool true if package type is set up
     */
    public function addRelease()
    {
        if ($type = $this->getPackageType()) {
            if ($type != 'bundle') {
                $type .= 'release';
            }
            $this->_packageInfo = $this->_mergeTag(
                $this->_packageInfo,
                [],
                [$type => ['changelog']]
            );
            return true;
        }
        return false;
    }

    /**
     * Get the current release tag in order to add to it
     * @param bool returns only releases that have installcondition if true
     * @return array|null
     */
    public function &_getCurrentRelease($strict = true)
    {
        if ($p = $this->getPackageType()) {
            if ($strict) {
                if ($p == 'extsrc' || $p == 'zendextsrc') {
                    $a = null;
                    return $a;
                }
            }
            if ($p != 'bundle') {
                $p .= 'release';
            }
            if (isset($this->_packageInfo[$p][0])) {
                return $this->_packageInfo[$p][count($this->_packageInfo[$p]) - 1];
            } else {
                return $this->_packageInfo[$p];
            }
        } else {
            $a = null;
            return $a;
        }
    }

    /**
     * Add a file to the current release that should be installed under a different name
     * @param string <contents> path to file
     * @param string name the file should be installed as
     */
    public function addInstallAs($path, $as)
    {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        $r = $this->_mergeTag(
            $r,
            ['attribs' => ['name' => $path, 'as' => $as]],
            [
                'filelist' => [],
                'install' => ['ignore']
            ]
        );
    }

    /**
     * Add a file to the current release that should be ignored
     * @param string <contents> path to file
     * @return bool success of operation
     */
    public function addIgnore($path)
    {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        $r = $this->_mergeTag(
            $r,
            ['attribs' => ['name' => $path]],
            [
                'filelist' => [],
                'ignore' => []
            ]
        );
    }

    /**
     * Add an extension binary package for this extension source code release
     *
     * Note that the package must be from the same channel as the extension source package
     * @param string
     */
    public function addBinarypackage($package)
    {
        if ($this->getPackageType() != 'extsrc' && $this->getPackageType() != 'zendextsrc') {
            return false;
        }
        $r = &$this->_getCurrentRelease(false);
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        $r = $this->_mergeTag(
            $r,
            $package,
            [
                'binarypackage' => ['filelist'],
            ]
        );
    }

    /**
     * Add a configureoption to an extension source package
     * @param string
     * @param string
     * @param string
     */
    public function addConfigureOption($name, $prompt, $default = null)
    {
        if ($this->getPackageType() != 'extsrc' && $this->getPackageType() != 'zendextsrc') {
            return false;
        }

        $r = &$this->_getCurrentRelease(false);
        if ($r === null) {
            return false;
        }

        $opt = ['attribs' => ['name' => $name, 'prompt' => $prompt]];
        if ($default !== null) {
            $opt['attribs']['default'] = $default;
        }

        $this->_isValid = 0;
        $r = $this->_mergeTag(
            $r,
            $opt,
            [
                'configureoption' => ['binarypackage', 'filelist'],
            ]
        );
    }

    /**
     * Set an installation condition based on php version for the current release set
     * @param string minimum version
     * @param string maximum version
     * @param false|array incompatible versions of PHP
     */
    public function setPhpInstallCondition($min, $max, $exclude = false)
    {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        if (isset($r['installconditions']['php'])) {
            unset($r['installconditions']['php']);
        }
        $dep = ['min' => $min, 'max' => $max];
        if ($exclude) {
            if (is_array($exclude) && count($exclude) == 1) {
                $exclude = $exclude[0];
            }
            $dep['exclude'] = $exclude;
        }
        if ($this->getPackageType() == 'extsrc' || $this->getPackageType() == 'zendextsrc') {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['configureoption', 'binarypackage',
                        'filelist'],
                    'php' => ['extension', 'os', 'arch']
                ]
            );
        } else {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['filelist'],
                    'php' => ['extension', 'os', 'arch']
                ]
            );
        }
    }

    /**
     * @param optional|required optional, required
     * @param string extension name
     * @param string minimum version
     * @param string maximum version
     * @param string recommended version
     * @param array incompatible versions
     */
    public function addExtensionInstallCondition(
        $name,
        $min = false,
        $max = false,
        $recommended = false,
        $exclude = false
    ) {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        $dep = $this->_constructDep($name, false, false, $min, $max, $recommended, $exclude);
        if ($this->getPackageType() == 'extsrc' || $this->getPackageType() == 'zendextsrc') {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['configureoption', 'binarypackage',
                        'filelist'],
                    'extension' => ['os', 'arch']
                ]
            );
        } else {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['filelist'],
                    'extension' => ['os', 'arch']
                ]
            );
        }
    }

    /**
     * Set an installation condition based on operating system for the current release set
     * @param string OS name
     * @param bool whether this OS is incompatible with the current release
     */
    public function setOsInstallCondition($name, $conflicts = false)
    {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        if (isset($r['installconditions']['os'])) {
            unset($r['installconditions']['os']);
        }
        $dep = ['name' => $name];
        if ($conflicts) {
            $dep['conflicts'] = '';
        }
        if ($this->getPackageType() == 'extsrc' || $this->getPackageType() == 'zendextsrc') {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['configureoption', 'binarypackage',
                        'filelist'],
                    'os' => ['arch']
                ]
            );
        } else {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['filelist'],
                    'os' => ['arch']
                ]
            );
        }
    }

    /**
     * Set an installation condition based on architecture for the current release set
     * @param string architecture pattern
     * @param bool whether this arch is incompatible with the current release
     */
    public function setArchInstallCondition($pattern, $conflicts = false)
    {
        $r = &$this->_getCurrentRelease();
        if ($r === null) {
            return false;
        }
        $this->_isValid = 0;
        if (isset($r['installconditions']['arch'])) {
            unset($r['installconditions']['arch']);
        }
        $dep = ['pattern' => $pattern];
        if ($conflicts) {
            $dep['conflicts'] = '';
        }
        if ($this->getPackageType() == 'extsrc' || $this->getPackageType() == 'zendextsrc') {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['configureoption', 'binarypackage',
                        'filelist'],
                    'arch' => []
                ]
            );
        } else {
            $r = $this->_mergeTag(
                $r,
                $dep,
                [
                    'installconditions' => ['filelist'],
                    'arch' => []
                ]
            );
        }
    }

    /**
     * For extension binary releases, this is used to specify either the
     * static URI to a source package, or the package name and channel of the extsrc/zendextsrc
     * package it is based on.
     * @param string Package name, or full URI to source package (extsrc/zendextsrc type)
     */
    public function setSourcePackage($packageOrUri)
    {
        $this->_isValid = 0;
        if (isset($this->_packageInfo['channel'])) {
            $this->_packageInfo = $this->_insertBefore(
                $this->_packageInfo,
                ['phprelease',
                'extsrcrelease', 'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'bundle', 'changelog'],
                $packageOrUri,
                'srcpackage'
            );
        } else {
            $this->_packageInfo = $this->_insertBefore($this->_packageInfo, ['phprelease',
                'extsrcrelease', 'extbinrelease', 'zendextsrcrelease', 'zendextbinrelease',
                'bundle', 'changelog'], $packageOrUri, 'srcuri');
        }
    }

    /**
     * Generate a valid change log entry from the current package.xml
     * @param string|false
     */
    public function generateChangeLogEntry($notes = false)
    {
        return [
            'version' =>
                [
                    'release' => $this->getVersion('release'),
                    'api' => $this->getVersion('api'),
                    ],
            'stability' =>
                $this->getStability(),
            'date' => $this->getDate(),
            'license' => $this->getLicense(true),
            'notes' => $notes ? $notes : $this->getNotes()
            ];
    }

    /**
     * @param string release version to set change log notes for
     * @param array output of {@link generateChangeLogEntry()}
     */
    public function setChangelogEntry($releaseversion, $contents)
    {
        if (!isset($this->_packageInfo['changelog'])) {
            $this->_packageInfo['changelog']['release'] = $contents;
            return;
        }
        if (!isset($this->_packageInfo['changelog']['release'][0])) {
            if ($this->_packageInfo['changelog']['release']['version']['release'] == $releaseversion) {
                $this->_packageInfo['changelog']['release'] = [
                    $this->_packageInfo['changelog']['release']];
            } else {
                $this->_packageInfo['changelog']['release'] = [
                    $this->_packageInfo['changelog']['release']];
                return $this->_packageInfo['changelog']['release'][] = $contents;
            }
        }
        foreach ($this->_packageInfo['changelog']['release'] as $index => $changelog) {
            if (isset($changelog['version']) &&
                  strnatcasecmp($changelog['version']['release'], $releaseversion) == 0) {
                $curlog = $index;
            }
        }
        if (isset($curlog)) {
            $this->_packageInfo['changelog']['release'][$curlog] = $contents;
        } else {
            $this->_packageInfo['changelog']['release'][] = $contents;
        }
    }

    /**
     * Remove the changelog entirely
     */
    public function clearChangeLog()
    {
        unset($this->_packageInfo['changelog']);
    }
}
