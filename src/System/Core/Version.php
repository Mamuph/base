<?php


/**
 * Version helper class
 *
 * @package     Mamuph
 * @category    Helpers
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 *
 */
class Core_Version
{

    public static function get(Array $version_info)
    {
        return sprintf('%d.%d build: %d', $version_info['major'], $version_info['minor'], $version_info['build']);
    }



}