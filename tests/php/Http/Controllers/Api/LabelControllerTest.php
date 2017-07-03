<?php

namespace Biigle\Tests\Http\Controllers\Api;

use Biigle\Role;
use ApiTestCase;
use Biigle\Tests\LabelTest;
use Biigle\Tests\LabelTreeTest;
use Biigle\Tests\AnnotationLabelTest;

class LabelControllerTest extends ApiTestCase
{
    public function testUpdate()
    {
        $tree = LabelTreeTest::create();
        $otherLabel = LabelTest::create();
        $label = LabelTest::create([
            'name' => 'label name',
            'color' => 'abcdef',
            'parent_id' => null,
            'label_tree_id' => $tree->id,
        ]);
        $sibling = LabelTest::create(['label_tree_id' => $tree->id]);
        $tree->addMember($this->editor(), Role::$editor);

        $this->doTestApiRoute('PUT', "/api/v1/labels/{$label->id}");

        // only label tree members can edit a label
        $this->beUser();
        $this->json('PUT', "/api/v1/labels/{$label->id}");
        $this->assertResponseStatus(403);

        $this->beEditor();
        $this->json('PUT', "/api/v1/labels/{$label->id}", [
            'name' => '',
        ]);
        // name must be filled
        $this->assertResponseStatus(422);

        $this->json('PUT', "/api/v1/labels/{$label->id}", [
            'color' => '',
        ]);
        // color must be filled
        $this->assertResponseStatus(422);

        $this->json('PUT', "/api/v1/labels/{$label->id}", [
            'parent_id' => $otherLabel->id,
        ]);
        // parent is not from same tree
        $this->assertResponseStatus(422);

        $this->json('PUT', "/api/v1/labels/{$label->id}", [
            'name' => 'new label name',
            'color' => 'bada55',
            'parent_id' => $sibling->id,
        ]);
        $this->assertResponseOk();

        $label = $label->fresh();
        $this->assertEquals('new label name', $label->name);
        $this->assertEquals('bada55', $label->color);
        $this->assertEquals($sibling->id, $label->parent_id);
    }

    public function testDestroy()
    {
        $label = LabelTest::create();
        $label->tree->addMember($this->editor(), Role::$editor);

        $this->doTestApiRoute('DELETE', "/api/v1/labels/{$label->id}");

        // only label tree members can remove a label
        $this->beUser();
        $this->json('DELETE', "/api/v1/labels/{$label->id}");
        $this->assertResponseStatus(403);

        // make sure the label is used somewhere
        $a = AnnotationLabelTest::create(['label_id' => $label->id]);

        $this->beEditor();
        $this->json('DELETE', "/api/v1/labels/{$label->id}");
        // can't be deleted if a label is still in use
        $this->assertResponseStatus(403);

        $a->delete();

        $child = LabelTest::create(['parent_id' => $label->id]);

        $this->beEditor();
        $this->json('DELETE', "/api/v1/labels/{$label->id}");
        // can't be deleted if label has children
        $this->assertResponseStatus(403);

        $child->delete();

        $this->assertNotNull($label->fresh());
        $this->json('DELETE', "/api/v1/labels/{$label->id}");
        $this->assertResponseOk();
        $this->assertNull($label->fresh());
    }

    public function testDestroyFormRequest()
    {
        $label = LabelTest::create();
        $label->tree->addMember($this->editor(), Role::$editor);

        $this->beEditor();
        $this->visit('/');
        $this->delete("/api/v1/labels/{$label->id}");
        $this->assertNull($label->fresh());
        $this->assertRedirectedTo('/');
        $this->assertSessionHas('deleted', true);

        $label = LabelTest::create();
        $label->tree->addMember($this->editor(), Role::$editor);

        $this->delete("/api/v1/labels/{$label->id}", [
            '_redirect' => 'settings',
        ]);
        $this->assertNull($label->fresh());
        $this->assertRedirectedTo('/settings');
        $this->assertSessionHas('deleted', true);
    }
}