<?php

namespace Test\Unit;

use JesseGall\Proxy\InteractionHash;
use JesseGall\Proxy\Interactions\Concerns\HasMethodAndParameters;
use JesseGall\Proxy\Interactions\Concerns\HasProperty;
use JesseGall\Proxy\Interactions\Concerns\HasValue;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithAndModifiesProperty;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithMethod;
use JesseGall\Proxy\Interactions\Interaction;
use Test\TestCase;
use Test\TestClasses\TestTarget;

class InteractionHashTest extends TestCase
{

    public function test_hashes_are_equal_when_interactions_are_equal()
    {
        $target = new TestTarget();

        $this->assertEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_equal_when_interactions_are_equal_but_target_instances_differ()
    {
        $this->assertEquals(

            (new InteractionHash(
                $this->newInteraction(new TestTarget())
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction(new TestTarget())
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_not_equal_when_targets_differ()
    {
        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction(new TestTarget())
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction(new \stdClass())
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_not_equal_when_methods_differ()
    {
        $target = new TestTarget();

        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('other method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_not_equal_when_parameters_differ()
    {
        $target = new TestTarget();

        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([3, 2, 1])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_not_equal_when_properties_differ()
    {
        $target = new TestTarget();

        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('other property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    public function test_hashes_are_not_equal_when_values_differ()
    {
        $target = new TestTarget();

        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('other value')
            ))->generate(),

        );
    }


    public function test_hashes_are_not_equal_when_interaction_class_differs()
    {
        $target = new TestTarget();

        $this->assertNotEquals(

            (new InteractionHash(
                $this->newInteraction($target)
                    ->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

            (new InteractionHash(
                (new class($target) extends Interaction implements
                    InteractsWithAndModifiesProperty,
                    InteractsWithMethod {
                    use HasProperty, HasValue, HasMethodAndParameters;
                })->setMethod('method')
                    ->setParameters([1, 2, 3])
                    ->setProperty('property')
                    ->setValue('value')
            ))->generate(),

        );
    }

    protected function newInteraction(object $target)
    {
        return new class($target) extends Interaction implements
            InteractsWithAndModifiesProperty,
            InteractsWithMethod {
            use HasProperty, HasValue, HasMethodAndParameters;
        };
    }

}