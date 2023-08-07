<?php
/**
 * Created by LoggerService.php
 *
 * PHP Version 5.4
 *
 * @author    waitlee <liduabc2012@gmail.com>
 * @copyright 2016 LMT Team, all rights reserved
 * @link      http://www.lemaitong.com
 */

namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Class LoggerService
 *
 * @package App\Services
 */
class LoggerService
{
    /**
     * @param $name
     * @param null $dir
     * @return Logger
     * @throws \Exception
     */
    public static function getLogger($name, $dir = null)
    {
        $logger = new Logger($name);

        $date = date('Y_m_d', time());

        $file_name = $name . '_' . $date . '.log';

        $path = storage_path() . '/logs/' . ($dir ? ($dir . '/') : '') . $file_name;

        $stream = new StreamHandler($path, Logger::INFO);

        $firePhp = new FirePHPHandler();

        $logger->pushHandler($stream);
        $logger->pushHandler($firePhp);

        return $logger;
    }
}
