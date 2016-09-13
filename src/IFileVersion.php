<?php
namespace Isappit\Ifile;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @link       https://github.com/isappit/ifile for the canonical source repository
 * @copyright  Copyright (c) 2011-2016 isApp.it (http://www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */

/**
 * IFile Version Class 
 */
final class IFileVersion
{
    /**
     * IFile version
     */
    const VERSION = '2.0';
	
	/**
     * IFile Versione Date
     * YYYY-MM-DD
     */
    const VERSIONDATE = '2016-03-15';

    /**
     * IFile version URL
     * 
     */
    const URLVERSION = 'http://www.isapp.it/ifile/ifile-version';
	 /**
     * Last stable version
     *
     * @var string
     */
    protected static $_latestVersion;
	
    /**
     *
     * Compare whit IFileVersion::VERSION
     *
     * @param  string  $version  format version (e.g. "1.0.1").
     * @return int           -1 minor version
     *                        0 equal version
     *                       +1 major version
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }

    /**
     * Get last stable version
     *
     * @link http://www.isapp.it/en/download-ifile.html
     * @return string
     */
    public static function getLatest()
    {
        if (null === self::$_latestVersion) {
            self::$_latestVersion = 'Not available';

            $handle = fopen(self::URLVERSION, 'r');
            if (false !== $handle) {
                self::$_latestVersion = stream_get_contents($handle);
                fclose($handle);
            }
        }

        return self::$_latestVersion;
    }
}
