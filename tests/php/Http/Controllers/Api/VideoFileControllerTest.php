<?php

namespace Biigle\Tests\Http\Controllers\Api;

use ApiTestCase;
use Biigle\MediaType;
use Biigle\Tests\VideoTest;
use Mockery;
use RuntimeException;
use Storage;

class VideoFileControllerTest extends ApiTestCase
{
    public function testShow()
    {
        $disk = Storage::fake('test');
        $disk->buildTemporaryUrlsUsing(function () {
            // Act as if the storage disk driver does not support temporary URLs.
            throw new RuntimeException;
        });
        $disk->put('files/video.mp4', 'testvideo');
        $id = $this->volume(['media_type_id' => MediaType::videoId()])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
            'attrs' => ['size' => 9],
        ]);

        $this->doTestApiRoute('GET', "api/v1/videos/{$video->id}/file");

        $this->beUser();
        $this->get('api/v1/videos/foo/file')->assertStatus(404);
        $this->get("api/v1/videos/{$video->id}/file")->assertStatus(403);

        $this->beGuest();
        $this->get("api/v1/videos/{$video->id}/file")->assertStatus(200);
    }

    public function testShowNotFound()
    {
        $disk = Storage::fake('test');
        $disk->buildTemporaryUrlsUsing(function () {
            // Act as if the storage disk driver does not support temporary URLs.
            throw new RuntimeException;
        });
        $id = $this->volume(['media_type_id' => MediaType::videoId()])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
            'attrs' => ['size' => 9],
        ]);

        $this->beGuest();
        $this->get("api/v1/videos/{$video->id}/file")->assertStatus(404);
    }

    public function testShowPartial()
    {
        $disk = Storage::fake('test');
        $disk->buildTemporaryUrlsUsing(function () {
            // Act as if the storage disk driver does not support temporary URLs.
            throw new RuntimeException;
        });
        $disk->put('files/video.mp4', 'testvideo');
        $id = $this->volume(['media_type_id' => MediaType::videoId()])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
            'attrs' => ['size' => 9],
        ]);

        $this->beGuest();
        $response = $this->withHeaders(['Range' => 'bytes=3-'])
            ->getJson("api/v1/videos/{$video->id}/file")
            ->assertStatus(206);

        $this->assertEquals(6, $response->headers->get('Content-Length'));
        $this->assertTrue($response->headers->has('Content-Range'));
        $this->assertEquals('bytes 3-8/9', $response->headers->get('Content-Range'));
    }

    public function testShowRemote()
    {
        $id = $this->volume([
            'media_type_id' => MediaType::videoId(),
            'url' => 'https://domain.tld',
        ])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
            'attrs' => ['size' => 9],
        ]);

        $this->beGuest();
        $this->get("api/v1/videos/{$video->id}/file")->assertRedirect($video->url);
    }

    public function testShowTempUrl()
    {
        $id = $this->volume(['media_type_id' => MediaType::videoId()])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
            'attrs' => ['size' => 9],
        ]);

        $mock = Mockery::mock();
        $mock->shouldReceive('temporaryUrl')->once()->andReturn('myurl');
        Storage::shouldReceive('disk')->andReturn($mock);

        $this->beGuest();
        $this->get("api/v1/videos/{$video->id}/file")
            ->assertRedirect('myurl');
    }

    public function testShowNotProcessed()
    {
        $id = $this->volume(['media_type_id' => MediaType::videoId()])->id;
        $video = VideoTest::create([
            'filename' => 'video.mp4',
            'volume_id' => $id,
        ]);

        $this->beGuest();
        $this->get("api/v1/videos/{$video->id}/file")->assertStatus(428);
    }
}
