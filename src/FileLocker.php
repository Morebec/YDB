<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\File\File;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
  * FileLocker 
  */
 class FileLocker
 {
    /** @var Semaphore */
    private $semaphone;

    /** @var Factory */
    private $factory;

    function __construct()
    {
        $store = new SemaphoreStore();
        $factory = new Factory($store);

        $this->store = $store;
        $this->factory = $factory;
    }

    /**
     * Creates a lock for a file
     * @param  File   $file the file to lock
     * @return LockInterface       the lock
     */
    public function createFileLock(File $file): LockInterface
    {
        $lock = $this->factory->createLock($file->getRealPath());
        return $lock;
    }
 } 