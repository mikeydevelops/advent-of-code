<?php

namespace Mike\AdventOfCode\Console\Contracts;

interface AccessibleOutputFormatterStyleInterface
{
    /**
     * Checks to see if the style has only options set
     * and background and foreground colors are not.
     */
    public function hasOnlyOptions(): bool;

    /**
     * Merges colors from $style into current style.
     */
    public function mergeColorsWith(AccessibleOutputFormatterStyleInterface $style): static;

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
