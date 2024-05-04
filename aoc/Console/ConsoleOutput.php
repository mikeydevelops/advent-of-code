<?php

namespace Mike\AdventOfCode\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\WrappableOutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutput extends SymfonyConsoleOutput
{
    protected OutputFormatterInterface $formatter;

    /**
     * The maximum characters a line can print.
     */
    protected int $maxLineWidth = 0;

    /**
     * @param int                           $verbosity The verbosity level (one of the VERBOSITY constants in OutputInterface)
     * @param bool|null                     $decorated Whether to decorate messages (null for auto-guessing)
     * @param OutputFormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(int $verbosity = self::VERBOSITY_NORMAL, ?bool $decorated = null, ?OutputFormatterInterface $formatter = null)
    {
        $this->formatter = $formatter = $formatter ?? new OutputFormatter();

        parent::__construct($verbosity, $decorated, $formatter);
    }

    /**
     * Set the maximum line character width.
     */
    public function setMaxLineWith(int $width): static
    {
        $this->maxLineWidth = $width;

        return $this;
    }

    /**
     * @return void
     */
    public function write(string|iterable $messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        $types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
        $type = $types & $options ?: self::OUTPUT_NORMAL;

        $verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
        $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;

        if ($verbosity > $this->getVerbosity()) {
            return;
        }

        foreach ($messages as $message) {
            switch ($type) {
                case OutputInterface::OUTPUT_NORMAL:
                    $message = $this->formatter instanceof WrappableOutputFormatterInterface
                        ? $this->formatter->formatAndWrap($message, $this->maxLineWidth)
                        : $this->formatter->format($message);
                    break;
                case OutputInterface::OUTPUT_RAW:
                    break;
                case OutputInterface::OUTPUT_PLAIN:
                    $message = strip_tags($this->formatter instanceof WrappableOutputFormatterInterface
                        ? $this->formatter->formatAndWrap($message, $this->maxLineWidth)
                        : $this->formatter->format($message)
                    );
                    break;
            }

            $this->doWrite($message ?? '', $newline);
        }
    }
}
