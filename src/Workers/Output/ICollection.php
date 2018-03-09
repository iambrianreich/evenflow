<?php

namespace Evenflow\Workers\Output;

interface ICollection
{
    public function split() : array;
}
