<?php

namespace Biigle\Jobs;

use App;
use Biigle\Jobs\Job;
use Biigle\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FileCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Storage;
use VipsImage;

class ProcessNewVideo extends Job implements ShouldQueue
{
    use SerializesModels;

    /**
     * The new video that should be processed.
     *
     * @var Video
     */
    public $video;

    /**
     * The FFMpeg video instance.
     *
     * @var \FFMpeg\Media\Video
     */
    protected $ffmpegVideo;

    /**
     * Create a new instance.
     *
     * @param Video $video The video that should be processed.
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            FileCache::getOnce($this->video, [$this, 'handleFile']);
        } catch (Exception $e) {
            Log::warning("Could not process new video {$this->video->id}: {$e->getMessage()}");
            if (App::runningUnitTests()) {
                throw $e;
            }
        }
    }

    /**
     * Process a cached video file.
     *
     * @param Video $file
     * @param string $path
     */
    public function handleFile($file, $path)
    {
        $this->video->duration = $this->getVideoDuration($path);
        $this->video->save();

        $times = $this->getThumbnailTimes($this->video->duration);
        $disk = Storage::disk(config('videos.thumbnail_storage_disk'));
        $fragment = fragment_uuid_path($this->video->uuid);
        $format = config('thumbnails.format');
        $width = config('thumbnails.width');
        $height = config('thumbnails.height');

        foreach ($times as $index => $time) {
            $buffer = $this->generateVideoThumbnail($path, $time, $width, $height, $format);
            $disk->put("{$fragment}/{$index}.{$format}", $buffer);
        }
    }

    /**
     * Get the duration of the video.
     *
     * @param string $path Video file path.
     *
     * @return float Duration in seconds.
     */
    protected function getVideoDuration($path)
    {
        return (float) FFProbe::create()
            ->format($path)
            ->get('duration');
    }

    /**
     * Generate a thumbnail from the video at the specified time.
     *
     * @param string $path Path to the video file.
     * @param float $time Time for the thumbnail in seconds.
     * @param int $width Width of the thumbnail.
     * @param int $height Height of the thumbnail.
     * @param string $format File format of the thumbnail (e.g. 'jpg').
     *
     * @return string Vips image buffer string.
     */
    protected function generateVideoThumbnail($path, $time, $width, $height, $format)
    {
        // Cache the video instance.
        if (!isset($this->ffmpegVideo)) {
            $this->ffmpegVideo = FFMpeg::create()->open($path);
        }

        $buffer = $this->ffmpegVideo->frame(TimeCode::fromSeconds($time))
            ->save(null, false, true);

        return VipsImage::thumbnail_buffer($buffer, $width, ['height' => $height])
            ->writeToBuffer(".{$format}", [
                'Q' => 85,
                'strip' => true,
            ]);
    }

    /**
     * Get the times at which thumbnails should be sampled.
     *
     * @param float $duration Video duration.
     *
     * @return array
     */
    protected function getThumbnailTimes($duration)
    {
        $count = config('videos.thumbnail_count');

        if ($count <= 1) {
            return [$duration / 2];
        }

        // Start from 0.5 and stop at $duration - 0.5 because FFMpeg sometimes does not
        // extract frames from a time code that is equal to 0 or $duration.
        $step = ($duration - 1) / floatval($count - 1);
        $start = 0.5;
        $end = $duration - 0.5;
        $range = range($start, $end, $step);

        // Sometimes there is one entry too few due to rounding errors.
        if (count($range) < $count) {
            $range[] = $end;
        }

        return $range;
    }
}
