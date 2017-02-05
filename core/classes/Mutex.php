<?php

namespace Air;

/**
 * Filesystem mutex
 */
class Mutex
{
    var $writablePath = '';
    var $lockName = '';
    var $fileHandle = null;

    /**
     * Mutex constructor.
     * @param $lockName
     * @param string $writablePath
     */
    public function __construct($lockName, $writablePath = null)
    {
        $this->lockName = preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $lockName);
        $this->writablePath = $writablePath ? $writablePath : cfg()->tmpDir;
    }

    /**
     * Try to lock
     * @return file handle or false
     */
    public function lock()
    {
        if (!$this->fileHandle) {
            $this->fileHandle = @fopen($this->getLockFilePath(), 'a+');
            if ($this->fileHandle) {
                if (flock($this->fileHandle, LOCK_EX | LOCK_NB)) {
                    return $this->fileHandle;
                } else {
                    fclose($this->fileHandle);
                    return false;
                }
            } else {
                return false;
            }
        }
        return $this->fileHandle;
    }

    /**
     * Release lock
     * @return bool
     */
    public function release()
    {
        if (!$this->fileHandle) {
            return false;
        }
        $success = fclose($this->fileHandle);
        $filePath = $this->getLockFilePath();
        if (file_exists($filePath)) unlink($filePath);
        $this->fileHandle = null;
        return $success;
    }

    /**
     * Get lock file name
     * @return string
     */
    public function getLockFilePath()
    {
        return $this->writablePath . DIRECTORY_SEPARATOR . $this->lockName;
    }

    /**
     * Check if locked
     * @return bool
     */
    public function isLocked()
    {
        if ($this->fileHandle) return $this->fileHandle;
        $this->fileHandle = @fopen($this->getLockFilePath(), 'a+');
        if ($this->fileHandle) {
            if (flock($this->fileHandle, LOCK_EX | LOCK_NB)) {
                $this->release();
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * Truncate file and write current pid in lock file
     */
    public function writePid()
    {
        ftruncate($this->fileHandle, 0);
        fwrite($this->fileHandle, getmypid());
    }
}