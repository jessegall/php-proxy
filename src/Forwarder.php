<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Call;
use JesseGall\Proxy\Interactions\Contract\ReturnResultContract;
use JesseGall\Proxy\Interactions\Get;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Set;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\ForwardCall;
use JesseGall\Proxy\Strategies\ForwardGet;
use JesseGall\Proxy\Strategies\ForwardSet;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class Forwarder
{

    /**
     * Mapping of the forward strategies
     *
     * @var array<class-string<Interaction>, class-string<ForwardStrategy>>
     */
    protected array $strategies = [
        Call::class => ForwardCall::class,
        Get::class => ForwardGet::class,
        Set::class => ForwardSet::class,
    ];

    /**
     * @var InterceptorContract[]
     */
    protected array $interceptors = [];

    /**
     * Register interceptor
     *
     * @param InterceptorContract|class-string<InterceptorContract> $interceptor
     * @return void
     */
    public function register(InterceptorContract|string $interceptor): void
    {
        if (is_string($interceptor)) {
            if (! is_subclass_of($interceptor, InterceptorContract::class)) {
                throw new \InvalidArgumentException('Class must be an instance of InterceptorContract');
            }

            $interceptor = new $interceptor;
        }

        $this->interceptors[] = $interceptor;
    }

    /**
     * Forward the interaction to the target and return concluded interaction.
     * Before forwarding notify interceptors about the interaction.
     * Cancel forwarding when the status is not pending.
     *
     * @param Interaction $interaction
     * @return ConcludedInteraction
     */
    public function forward(Interaction $interaction): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction);

        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction);
        }

        $strategy = $this->newForwardStrategy($interaction);

        $result = $strategy->execute();

        if ($interaction instanceof ReturnResultContract) {
            $interaction->setResult($result);
        }

        return new ConcludedInteraction($interaction);
    }

    /**
     * Create a new forward strategy for the given interaction
     *
     * @param Interaction $interaction
     * @return ForwardStrategy
     */
    private function newForwardStrategy(Interaction $interaction): ForwardStrategy
    {
        $type = $this->strategies[$interaction::class];

        return new $type($interaction);
    }

    /**
     * Notify interceptors about an interaction.
     *
     * @param Interaction $interaction
     * @return void
     */
    protected function notifyInterceptors(Interaction $interaction): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->intercept($interaction);
        }
    }

    # --- Getters and Setters ---

    /**
     * @return InterceptorContract[]
     */
    public function getInterceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * @param array $interceptors
     * @return $this
     */
    public function setInterceptors(array $interceptors): Forwarder
    {
        $this->interceptors = $interceptors;

        return $this;
    }

}