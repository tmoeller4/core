<?php

namespace Biigle;

use Biigle\FileCache\Contracts\File as FileContract;
use Biigle\Traits\HasJsonAttributes;
use Illuminate\Database\Eloquent\Model;

class VolumeFile extends Model implements FileContract
{
    use HasJsonAttributes;

    /**
     * Don't maintain timestamps for this model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Adds the `url` attribute to the model. The url is the absolute path
     * to the original file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return "{$this->volume->url}/{$this->filename}";
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * The volume this video belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volume()
    {
        return $this->belongsTo(Volume::class);
    }
}
