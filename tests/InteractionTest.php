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

}