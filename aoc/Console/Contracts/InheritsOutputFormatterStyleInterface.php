<?php

namespace Mike\AdventOfCode\Console\Contracts;

interface InheritsOutputFormatterStyleInterface
{
    /**
     * Merges properties $style into current style.
     */
    public function inheritFrom(InheritsOutputFormatterStyleInterface $style): static;

    /**
     * Get the background color.
     */
    public function getBackground(): string;

    /**
     * Get the foreground color.
     */
    public function getForeground(): string;

    /**
     * Get the options.
     */
    public function getOptions(): array;
}
