<?php

use Dias\LabelSource;

class LabelSourceTest extends ModelTestCase
{
    /**
     * The model class this class will test.
     */
    protected static $modelClass = Dias\LabelSource::class;

    public function testAttributes()
    {
        $this->assertNotNull($this->model->name);
    }

    public function testNameRequired()
    {
        $this->model->name = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testNameUnique()
    {
        self::create(['name' => 'xyz']);
        $this->setExpectedException('Illuminate\Database\QueryException');
        self::create(['name' => 'xyz']);
    }

    public function testGetAdapter()
    {
        $mock = Mockery::mock();

        App::singleton('Dias\Services\LabelSourceAdapters\AbCdAdapter', function () use ($mock) {
            return $mock;
        });

        $source = self::create(['name' => 'ab_cd']);

        $this->assertEquals($mock, $source->getAdapter());
    }
}
