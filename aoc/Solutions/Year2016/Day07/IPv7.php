<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day07;

class IPv7
{
    /**
     * Sequence of characters representing the super net.
     */
    public string $superNet;

    /**
     * Sequence of characters representing the hyper net.
     */
    public string $hyperNet;

    /**
     * Create new instance of IPv7.
     */
    public function __construct(string $superNet, string $hyperNet)
    {
        $this->superNet = $superNet;
        $this->hyperNet = $hyperNet;
    }

    /**
     * Check to see if an IPv7 supports TLS (transport-layer snooping).
     */
    public function supportsTls(): bool
    {
        return !static::hasAbba($this->hyperNet) && static::hasAbba($this->superNet);
    }

    /**
     * Check to see if an IPv7 supports SSL (super-secret listening).
     */
    public function supportsSsl(): bool
    {
        $abas = $this->extractAbas();

        if (count($abas) == 0) {
            return false;
        }

        return count($this->extractBabs($abas)) > 0;
    }

    /**
     * Extract ABAs (Area-Broadcast Accessor) from given super net.
     *
     * @return string[]
     */
    public function extractAbas(): array
    {
        $triplets = array_sliding(str_split($this->superNet), 3);

        return array_values(array_filter($triplets, fn(array $t) => $t[0] === $t[2] && $t[0] !== $t[1]));
    }

    /**
     * Extract BABs (Byte Allocation Block) from given hyper net.
     *
     * @return string[]
     */
    public function extractBabs(array $abas = []): array
    {
        $abas = empty($abas) ? $this->extractAbas() : $abas;
        $triplets = array_sliding(str_split($this->hyperNet), 3);

        return array_values(array_filter($triplets, function ($t) use ($abas) {
            if ($t[0] !== $t[2] || $t[0] === $t[1]) {
                return false;
            }

            $aba = [$t[1], $t[0], $t[1]];

            return in_array($aba, $abas);
        }));
    }

    /**
     * Check to see if a string has Autonomous Bridge Bypass Annotation, or ABBA.
     */
    public static function hasAbba(string $string): bool
    {
        $pairs = array_sliding(str_split($string));

        foreach ($pairs as $idx => $pair) {
            if (count(array_unique($pair)) != 2 || !isset($pairs[$idx + 2])) {
                continue;
            }

            if ($pairs[$idx + 2] === array_reverse($pair)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create new instance from string.
     */
    public static function fromString(string $ip): static
    {
        preg_match_all('/([a-z]+)(?:\[([a-z]+)\])?/i', $ip, $matches);

        return new static(
            superNet: implode(':-', $matches[1]),
            hyperNet: implode(':-', array_filter($matches[2])),
        );
    }

    /**
     * Convert the object to string.
     */
    public function toString(): string
    {
        $super = explode(':-', $this->superNet);
        $hyper = array_map(fn(string $str) => "[$str]", explode(':-', $this->hyperNet));

        $string = '';

        foreach ($super as $i => $s) {
            $string .= $s . ($hyper[$i] ?? '');
        }

        return $string;
    }

    /**
     * PHP magic method to convert the object to string.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
