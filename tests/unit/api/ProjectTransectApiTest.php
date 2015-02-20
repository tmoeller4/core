<?php

use Dias\MediaType;
use Dias\Transect;
use Dias\Role;

class ProjectTransectApiTest extends ApiTestCase {

	private $transect;

	public function setUp()
	{
		parent::setUp();

		$this->transect = TransectTest::create('test', base_path().'/tests/files');
		$this->transect->save();
		$this->project->addTransectId($this->transect->id);
	}

	public function testIndex()
	{
		$this->doTestApiRoute('GET', '/api/v1/projects/1/transects');

		// api key authentication
		$this->callToken('GET', '/api/v1/projects/1/transects', $this->user);
		$this->assertResponseStatus(401);

		$this->callToken('GET', '/api/v1/projects/1/transects', $this->guest);
		$this->assertResponseOk();

		// session cookie authentication
		$this->be($this->guest);
		$r = $this->callAjax('GET', '/api/v1/projects/1/transects');
		$this->assertResponseOk();
		// response should not be an empty array
		$this->assertStringStartsWith('[{', $r->getContent());
		$this->assertStringEndsWith('}]', $r->getContent());
		$this->assertNotContains('pivot', $r->getContent());
	}

	public function testStore()
	{
		$this->doTestApiRoute('POST', '/api/v1/projects/1/transects');

		// api key authentication
		$this->callToken('POST', '/api/v1/projects/1/transects', $this->editor);
		$this->assertResponseStatus(401);

		$this->callToken('POST', '/api/v1/projects/1/transects', $this->admin);
		// mssing arguments
		$this->assertResponseStatus(400);

		// session cookie authentication
		$this->be($this->admin);
		$this->callAjax('POST', '/api/v1/projects/1/transects', array(
			'_token' => Session::token(),
			'name' => 'my transect no. 1',
			'url' => 'random',
			'media_type_id' => 99999,
			'images' => '["1.jpg"]'
		));
		// media type does not exist
		$this->assertResponseStatus(400);

		$this->callAjax('POST', '/api/v1/projects/1/transects', array(
			'_token' => Session::token(),
			'name' => 'my transect no. 1',
			'url' => 'random',
			'media_type_id' => MediaType::timeSeriesId(),
			'images' => "[]"
		));
		// images array is empty
		$this->assertResponseStatus(400);

		$r = $this->callAjax('POST', '/api/v1/projects/1/transects', array(
			'_token' => Session::token(),
			'name' => 'my transect no. 1',
			'url' => 'random',
			'media_type_id' => MediaType::timeSeriesId(),
			'images' => '["1.jpg"]'
		));
		$this->assertResponseOk();
		$this->assertStringStartsWith('{', $r->getContent());
		$this->assertStringEndsWith('}', $r->getContent());

		$id = json_decode($r->getContent())->id;
		$transect = Transect::find($id);
		$this->assertEquals('1.jpg', $transect->images()->first()->filename);
	}

	public function testAttach()
	{
		$tid = $this->transect->id;

		$secondProject = ProjectTest::create();
		$secondProject->save();
		$pid = $secondProject->id;
		// $secondProject->addUserId($this->admin->id, Role::adminId());

		$this->doTestApiRoute('POST', '/api/v1/projects/'.$pid.'/transects/'.$tid);

		// api key authentication
		$this->callToken('POST', '/api/v1/projects/'.$pid.'/transects/'.$tid, $this->admin);
		$this->assertResponseStatus(401);

		$secondProject->addUserId($this->admin->id, Role::adminId());

		// session cookie authentication
		$this->be($this->admin);
		$this->assertEmpty($secondProject->fresh()->transects);
		$this->callAjax('POST', '/api/v1/projects/'.$pid.'/transects/'.$tid, array('_token' => Session::token()));
		$this->assertResponseOk();
		$this->assertNotEmpty($secondProject->fresh()->transects);
	}

	public function testDestroy()
	{
		$id = $this->transect->id;
		$image = ImageTest::create();
		$image->transect()->associate($this->transect);
		$image->save();
		$image->getThumb();

		$this->doTestApiRoute('DELETE', '/api/v1/projects/1/transects/'.$id);

		// api key authentication
		$this->callToken('DELETE', '/api/v1/projects/1/transects/'.$id, $this->user);
		$this->assertResponseStatus(401);

		$this->callToken('DELETE', '/api/v1/projects/1/transects/'.$id, $this->guest);
		$this->assertResponseStatus(401);

		$this->callToken('DELETE', '/api/v1/projects/1/transects/'.$id, $this->editor);
		$this->assertResponseStatus(401);

		$this->callToken('DELETE', '/api/v1/projects/1/transects/'.$id, $this->admin);
		// trying to delete withour force
		$this->assertResponseStatus(400);

		// session cookie authentication
		$this->be($this->admin);
		$this->callAjax('DELETE', '/api/v1/projects/1/transects/'.$id, array(
			'_token' => Session::token(),
			'force' => 'abc'
		));
		// deleting with force succeeds
		$this->assertResponseOk();
		$this->assertNull($this->transect->fresh());

		$this->assertTrue(File::exists($image->thumbPath));
		$this->assertNotNull($image->fresh());
		// call cleanup command immediately
		Artisan::call('remove-deleted-images');
		$this->assertNull($image->fresh());
		$this->assertFalse(File::exists($image->thumbPath));
	}
}