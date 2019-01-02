<?php declare(strict_types = 1);

namespace App\Library\Logger;

use App\Library\Ensure;
use App\ValueObject\File;
use DateTime;

class FileLogger implements Logger
{

    use Ensure;

    /** @var File **/
    private $logFile;

    private const TYPE_WARNING = 'WARNING';

    private const TYPE_ERROR = 'ERROR';

    public function __construct(File $logFile)
    {
        $this->logFile = $logFile;
    }

    private function log(string $message, string $type)
    {
        $this->ensureTypeExists($type);
        $this->ensurePathIsWritable($this->logFile->getPath());
        
        file_put_contents($this->logFile->getPath(), $this->format($message, $type), FILE_APPEND);
    }

    public function warning(string $message)
    {
        $this->log($message, self::TYPE_WARNING);
    }

    public function error(string $message)
    {
        $this->log($message, self::TYPE_ERROR);
    }

    private function format(string $message, string $type)
    {
        $this->ensureTypeExists($type);
        $now = new DateTime();
        
        return date(DATE_RSS, $now->getTimestamp()) . $type . ': ' . $message . "\n";
    }

    private function ensureTypeExists(string $type)
    {
        if (defined('self::TYPE_' . $type) !== true) {
            throw new \Exception('Log type unknown: ' . $type);
        }
    }
}
