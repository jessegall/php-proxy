<?php

namespace Test;

use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contract\ReturnResultContract;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;
use PHPUnit\Framework\TestCase;
use Test\TestClasses\TestTarget;

class InteractionTest extends TestCase
{

    public function test_get_result_returns_null_when_status_is_cancelled_and_uses_has_result_trait()
    {
        $interaction = new class(new TestTarget()) extends Interaction implements ReturnResultContract {
            use HasResult;
        };

        $interaction->setResult('not expected');

        $interaction->setStatus(Status::CANCELLED);

        $this->assertNull($interaction->getResult());
    }

    public function test_get_result_returns_null_when_status_is_pending_and_uses_has_result_trait()
    {
        $interaction = new class(new TestTarget()) extends Interaction implements ReturnResultContract {
            use HasResult;
        };

        $interaction->setResult('not expected');

        $interaction->setStatus(Status::PENDING);

        $this->assertNull($interaction->getResult());
    }

    public function test_get_result_returns_expected_when_status_is_fulfilled_and_uses_has_result_trait()
    {
        $interaction = new class(new TestTarget()) extends Interaction implements ReturnResultContract {
            use HasResult;
        };

        $interaction->setResult('expected');

        $interaction->setStatus(Status::FULFILLED);

        $this->assertEquals(
            'expected',
            $interaction->getResult()
        );
    }

    public function test_set_and_get_target_change_and_return_expected_value()
    {
        $interaction = new class(new TestTarget()) extends Interaction {

        };

        $interaction->setTarget($expected = new \stdClass());

        $this->assertEquals($expected, $interaction->getTarget());
    }

    public function test_set_and_get_status_change_and_return_expected_value()
    {
        $interaction = new class(new TestTarget()) extends Interaction {

        };

        $interaction->setStatus($expected = Status::FAILED);

        $this->assertEquals($expected, $interaction->getStatus());
    }

}