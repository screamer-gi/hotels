<?php

namespace Test\unit\Hotel;

use Codeception\Test\Unit;
use DateTimeImmutable;
use Hotel\IntervalValidator;
use UnitTester;

class IntervalValidatorTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /** @var IntervalValidator */
    private $validator;

    protected function _before()
    {
        $this->validator = new IntervalValidator();
    }

    public function testValidate()
    {
        $this->assertTrue($this->validator->validate([
            'date_start' => '2019-01-03',
            'date_end' => '2019-01-03',
            'price' => '123.45',
        ]));
    }

    public function testValidateStopOnErrors()
    {
        $this->assertFalse($this->validator->validate([
            'date_start' => '2019-01-03',
            'date_end' => '',
            'price' => 'wrong format float number is skipped',
        ]));
    }

    public function testValidateRequired()
    {
        $this->validator->setData(['price' => '123']);
        $this->validator->validateRequired('price');
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateRequiredFailed()
    {
        $this->validator->setData(['price' => '']);
        $this->validator->validateRequired(['price']);
        $this->assertEquals(['price' => ['`price` cannot be blank']], $this->validator->getErrors());
    }

    public function testValidateDate()
    {
        $this->validator->setData(['date_start' => '2019-03-01']);
        $this->validator->validateDate('date_start');
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateDateFailed()
    {
        $this->validator->setData(['date_end' => 'asdasd']);
        $this->validator->validateDate(['date_end']);
        $this->assertEquals(['date_end' => ['Wrong `date_end` date format']], $this->validator->getErrors());
    }

    public function testValidateDateOrder()
    {
        $this->validator->setData([
            'date_start' => '2019-03-01',
            'date_end' => '2019-03-05',
        ]);
        $this->validator->validateDateOrder(['date_start', 'date_end']);
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateDateOrderSameDates()
    {
        $this->validator->setData([
            'date_start' => '2019-03-01',
            'date_end' => '2019-03-01',
        ]);
        $this->validator->validateDateOrder(['date_start', 'date_end']);
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateDateOrderNoAttributes()
    {
        $this->validator->setData([
            'date_start' => '2019-03-06',
            'date_end' => '2019-03-05',
        ]);
        $this->validator->validateDateOrder([]);
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateDateOrderOneAttribute()
    {
        $this->validator->setData([
            'date_start' => '2019-03-06',
            'date_end' => '2019-03-05',
        ]);
        $this->validator->validateDateOrder(['date_start']);
        $this->assertEquals([], $this->validator->getErrors());
    }

    public function testValidateDateOrderFailed()
    {
        $this->validator->setData([
            'date_start' => '2019-03-06',
            'date_end' => '2019-03-05',
        ]);
        $this->validator->validateDateOrder(['date_start', 'date_end']);
        $this->assertEquals(['date_start' => ['`date_start` should be less than or equals to `date_end`']], $this->validator->getErrors());
    }

    public function testFilterFloatPoint()
    {
        $this->validator->setData(['price' => '123.45']);
        $this->validator->filterFloat(['price']);
        $this->assertEquals(123.45, $this->validator->getFilteredData()['price']);
    }

    public function testFilterFloatComma()
    {
        $this->validator->setData(['price' => '123,45']);
        $this->validator->filterFloat('price');
        $this->assertEquals(123.45, $this->validator->getFilteredData()['price']);
    }

    public function testSetData()
    {
        $this->validator->setData([
            'price' => '123,45',
            'date_start' => 'start_date',
            'date_end' => 'end_date',
            'extra' => 'extra',
        ]);

        $expectedData = [
            'price' => '123,45',
            'date_start' => 'start_date',
            'date_end' => 'end_date',
        ];

        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertEquals($expectedData, (function() { return $this->data; })->call($this->validator));
    }

    public function testGetFilteredData()
    {
        (function($data) { $this->data = $data; })->call($this->validator, ['data']);
        $this->assertEquals(['data'], $this->validator->getFilteredData());
    }

    public function testHasErrorsFalse()
    {
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testHasErrorsTrue()
    {
        $this->validator->addError('attribute', 'error');
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testGetErrors()
    {
        $this->validator->addError('attr1', 'error1-1');
        $this->validator->addError('attr1', 'error1-2');
        $this->validator->addError('attr2', 'error2-1');

        $this->assertEquals([
            'attr1' => ['error1-1', 'error1-2'],
            'attr2' => ['error2-1'],
        ], $this->validator->getErrors());
    }

    public function testGetErrorSummary()
    {
        $this->validator->addError('attr1', 'error1-1');
        $this->validator->addError('attr1', 'error1-2');
        $this->validator->addError('attr2', 'error2-1');

        $this->assertEquals([
            'error1-1',
            'error1-2',
            'error2-1',
        ], $this->validator->getErrorSummary());
    }

    public function testAddError()
    {
        $this->validator->addError('attr1', 'error1-1');
        $expexted = ['attr1' => ['error1-1']];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertEquals($expexted, (function() { return $this->errors; })->call($this->validator));
    }

    public function testDate()
    {
        $date = '2019-03-01';
        $expected = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $this->assertEquals($expected, $this->validator->date($date));
    }

    public function testDateNull()
    {
        $date = 'wrong date format';
        $this->assertNull($this->validator->date($date));
    }
}