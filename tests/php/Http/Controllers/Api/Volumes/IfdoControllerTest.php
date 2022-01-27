<?php

namespace Biigle\Tests\Http\Controllers\Api\Volumes;

use ApiTestCase;
use Biigle\Tests\ImageTest;
use Illuminate\Http\UploadedFile;
use Storage;

class IfdoControllerTest extends ApiTestCase
{
    public function testGet()
    {
        $id = $this->volume()->id;

        $this->doTestApiRoute('GET', "/api/v1/volumes/{$id}/ifdo");

        $this->beUser();
        $this->getJson("/api/v1/volumes/{$id}/ifdo")
            ->assertStatus(403);

        $this->beGuest();
        $this->getJson("/api/v1/volumes/{$id}/ifdo")
            ->assertStatus(404);

        $disk = Storage::fake('ifdos');
        $disk->put($id, 'abc');

        $this->getJson("/api/v1/volumes/-1/ifdo")
            ->assertStatus(404);

        $response = $this->getJson("/api/v1/volumes/{$id}/ifdo");
        $response->assertStatus(200);
        $this->assertEquals("attachment; filename=biigle-volume-{$id}-ifdo.yaml", $response->headers->get('content-disposition'));
    }
}
