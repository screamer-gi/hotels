<?php

namespace Hotel;

class HelloWorld
{
    /** @var string */
    private $foo;

    public function announce(): void
    {
        echo 'Hello, autoloaded world!';
    }

    public function __invoke(): void
    {
        echo "Hello, {$this->foo} world!";
        exit;
    }

    public function __construct(string $foo='cool')
    {
        $this->foo = $foo;
    }
}