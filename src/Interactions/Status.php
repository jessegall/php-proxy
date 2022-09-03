<?php

namespace JesseGall\Proxy\Interactions;

enum Status
{
    case PENDING;
    case FULFILLED;
    case CANCELLED;
}
