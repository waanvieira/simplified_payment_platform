<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;

trait TestModels
{
    protected abstract function model() : Model;
    protected abstract function filleableAtributes() : array;
    protected abstract function casts() : array;
    // protected abstract function datesAttributes() : array;
    // protected abstract function tableName() : string;
    // protected abstract function primaryKeyName() : string;
    protected abstract function constantes() : array;
    protected abstract function relations() : array;
    protected abstract function traitsNeed() : array;

    public function testsFillableAttributes()
    {
        $this->assertEquals($this->filleableAtributes(), $this->model()->getFillable());
    }

    public function testCasts()
    {
        // if (empty($this->casts())) {
        //     $this->markTestSkipped(__METHOD__ . 'valores não informados para teste.');
        // }

        $this->assertEquals($this->casts(), $this->model()->getCasts());
    }

    // public function testIncrementing()
    // {
    //     // dd($this->model()->incrementing);
    //     // $this->assertFalse($this->model()->increment);
    // }

    // public function testDatesAttributes()
    // {
    //     $this->assertEqualsCanonicalizing($this->datesAttributes(), $this->model()->getDates());
    // }

    public function testConstantes()
    {
        if (empty($this->constantes())) {
            $this->markTestSkipped(__METHOD__ . 'valores não informados para teste.');
        }

        $constantes = $this->constantes();
        foreach ($constantes as $key => $value) {
            $this->assertEquals($key, $value);
        }
    }

    // public function testTableName()
    // {
    //     $this->assertEquals($this->model()->getTable(), $this->tableName());
    // }

    // public function testPrimaryKey()
    // {
    //     $this->assertEquals($this->model()->getKeyName(), $this->primaryKeyName());
    // }

    public function testRelations()
    {
        if (empty($this->relations())) {
            $this->markTestSkipped(__METHOD__ . 'valores não informados para teste.');
        }

        $relations = $this->relations();
        foreach ($relations as $methodName => $relation) {
            $integracaoSap = $this->model()->$methodName()->getRelated();
            $this->assertTrue($integracaoSap instanceof $relation);
        }
    }

    public function testIfUseTraits()
    {
        $this->traitsNeed();
        $traitsUsed = array_keys(class_uses($this->model()));
        $this->assertEquals(count($traitsUsed),count($this->traitsNeed()));
        $this->assertEmpty(array_diff($this->traitsNeed(), $traitsUsed));
    }
}
