<?php


namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Watson\Rememberable\Rememberable;

class BaseEloquent extends BaseEloquent 
{
    use Rememberable;

    protected $rememberCacheTag;
    protected $rememberFor;

    // kita override constructor dari Illuminate\Database\Eloquent\Model
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // kita gunakan nama tabel sebagai cache tag
        $this->rememberCacheTag = $this->getTable();

        // kita set lifetime cache selama 24 jam atau sehari
        $this->rememberFor = 60*24;
    }

    // Semua operasi insert dan update melalui Eloquent pada akhirnya
    // akan memanggil method 'save' di Illuminate\Database\Eloquent\Model
    // kita akan override method ini untuk melakukan invalidate sebelum
    // operasi save dilakukan
    public function save(array $options = [])
    {
        $this->invalidateCache();
        parent::save($options);
    }

    // Semua operasi delete melalui Eloquent pada akhirnya
    // akan memanggil method 'save' di Illuminate\Database\Eloquent\Model
    // kita akan override method ini untuk melakukan invalidate sebelum
    // operasi save dilakukan
    public function delete()
    {
        $this->invalidateCache();
        parent::delete();
    }

    // Saat invalidate, kita akan hapus semua cache berdasarkan tag
    protected function invalidateCache()
    {
        Cache::tags($this->getTag())->flush();
    }

    // kita gunakan nama tabel sebagai tag
    protected function getTag()
    {
        return $this->getTable();
    }
}// App\BaseModel.