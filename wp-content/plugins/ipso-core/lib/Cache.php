<?php
/**
 * @desc Class that implements the Cache functionality
 */
class Cache
{
    public static $instance;
    var $cachePath;

    public static function get_instance(){
        if (is_null(self::$instance)){
            self::$instance = new Cache(__DIR__.'/../cache/');
        }
        return self::$instance;
    }

    function __construct($path_to_cache='cache/'){
        $this->cachePath = $path_to_cache;
    }

    /**
     * @desc Function read retrieves value from cache
     * @param string $fileName - name of the cache file
     * @return bool/string
     * Usage: Cache::read('fileName.extension')
     */
    function read($fileName)
    {
        $fileName = $this->cachePath . $fileName;
        if (file_exists($fileName)) {
            $handle = fopen($fileName, 'rb');
            $data = fread($handle, filesize($fileName));
            $data = unserialize($data);
            // checking if cache expired
            if (time() > $data[0]) {
                // it expired, delete the file
                @unlink($fileName);
                return false;
            }
            fclose($handle);
            // cache is still valid, return the data
            return $data[1];
        } else {
            return false;
        }
    }

    /**
     * @desc Function for writing key => value to cache
     * @param string $fileName - name of the cache file (key)
     * @param mixed $variable - value
     * @param number $ttl - time to last in milliseconds
     * @return void
     * Usage: Cache::write('fileName.extension', value)
     */
    function write($fileName, $variable, $ttl)
    {
        $fileName = $this->cachePath . $fileName;
        $handle = fopen($fileName, 'a');
        fwrite($handle, serialize(array(time() + $ttl, $variable)));
        fclose($handle);
    }

    /**
     * @desc Function for deleting cache file
     * @param string $fileName - name of the cache file (key)
     * @return void
     * Usage: Cache::delete('fileName.extension')
     */
    function delete($fileName)
    {
        $fileName = $this->cachePath . $fileName;
        @unlink($fileName);
    }

}