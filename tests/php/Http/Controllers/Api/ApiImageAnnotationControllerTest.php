<?php

class ApiImageAnnotationControllerTest extends ApiTestCase
{
    private $image;

    public function setUp()
    {
        parent::setUp();
        $this->image = ImageTest::create();
        $this->project()->addTransectId($this->image->transect->id);
    }

    public function testIndex()
    {
        $annotation = AnnotationTest::create([
            'image_id' => $this->image->id,
            'points' => [10, 20, 30, 40],
        ]);

        $label = LabelTest::create([
            'name' => 'My label',
            'color' => 'bada55',
        ]);

        AnnotationLabelTest::create([
            'label_id' => $label->id,
            'annotation_id' => $annotation->id,
            'user_id' => $this->editor()->id,
        ]);

        $this->doTestApiRoute('GET',"/api/v1/images/{$this->image->id}/annotations");

        $this->beUser();
        $this->get("/api/v1/images/{$this->image->id}/annotations");
        $this->assertResponseStatus(403);

        $this->beGuest();
        $this->get("/api/v1/images/{$this->image->id}/annotations")
            ->seeJson(['points' => [10, 20, 30, 40]])
            ->seeJson(['color' => 'bada55'])
            ->seeJson(['name' => 'My label']);
        $this->assertResponseOk();
    }

    public function testStore()
    {
        $label = LabelTest::create();

        $this->doTestApiRoute('POST', "/api/v1/images/{$this->image->id}/annotations");

        $this->beGuest();
        $this->post("/api/v1/images/{$this->image->id}/annotations");
        $this->assertResponseStatus(403);

        $this->beEditor();
        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations");
        // missing arguments
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => 99999,
            'points' => '',
        ]);
        // shape does not exist
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$lineId,
            'label_id' => 99999,
        ]);
        // label is required
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
        ]);
        // confidence required
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
            'confidence' => 2
        ]);
        // confidence must be between 0 and 1
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
            'confidence' => -1
        ]);
        // confidence must be between 0 and 1
        $this->assertResponseStatus(422);

        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
            'confidence' => 0.5,
            'points' => '[]',
        ]);
        // at least one point required
        $this->assertResponseStatus(422);

        $this->post("/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
            'confidence' => 0.5,
            'points' => '[10, 11]',
        ]);
        // label does not belong to a label tree of the project of the image
        $this->assertResponseStatus(403);

        $this->project()->labelTrees()->attach($label->label_tree_id);
        // policies are cached
        Cache::flush();

        $this->post("/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $label->id,
            'confidence' => 0.5,
            'points' => '[10, 11]',
        ]);

        $this->assertResponseOk();

        $this->seeJson(['points' => [10, 11]]);
        $this->seeJson(['name' => $label->name]);
        $this->seeJson(['color' => $label->color]);

        $annotation = $this->image->annotations->first();
        $this->assertNotNull($annotation);
        $this->assertEquals(2, sizeof($annotation->points));
        $this->assertEquals(1, $annotation->labels()->count());
    }

    public function testStoreValidatePoints()
    {
        $this->beEditor();
        $this->json('POST', "/api/v1/images/{$this->image->id}/annotations", [
            'shape_id' => \Dias\Shape::$pointId,
            'label_id' => $this->labelRoot()->id,
            'confidence' => 0.5,
            'points' => '[10, 11, 12, 13]',
        ]);
        // invalid number of points
        $this->assertResponseStatus(422);
    }
}