<?php

namespace Mike\AdventOfCode\Console\Traits;

use Mike\AdventOfCode\Console\OutputStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

trait InputOutput
{
    /**
     * The input interface implementation.
     */
    protected InputInterface $input;

    /**
     * The output interface implementation.
     */
    protected ?OutputStyle $output = null;

    /**
     * The default verbosity of output commands.
     */
    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * The mapping between human readable verbosity levels and Symfony's OutputInterface.
     */
    protected array $verbosityMap = [
        'v' => OutputInterface::VERBOSITY_VERBOSE,
        'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv' => OutputInterface::VERBOSITY_DEBUG,
        'quiet' => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /**
     * The output sections.
     *
     * @var \Symfony\Component\Console\Output\ConsoleSectionOutput[]
     */
    protected array $consoleSectionOutputs = [];

    /**
     * Create new output section.
     *
     * @return \Symfony\Component\Console\Output\ConsoleSectionOutput
     */
    public function section(): ConsoleSectionOutput
    {
        return new ConsoleSectionOutput(
            $this->getOutput()->getOutput()->getStream(),
            $this->consoleSectionOutputs,
            $this->getOutput()->getVerbosity(),
            $this->getOutput()->isDecorated(),
            $this->getOutput()->getFormatter()
        );
    }

    /**
     * Confirm a question with the user.
     */
    public function confirm(string $question, bool $default = false): bool
    {
        return $this->output->confirm($question, $default);
    }

    /**
     * Prompt the user for input.
     */
    public function ask(string $question, string $default = null): string|null
    {
        return $this->output->ask($question, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     */
    public function anticipate(string $question, array|callable $choices, string $default = null): mixed
    {
        return $this->askWithCompletion($question, $choices, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     */
    public function askWithCompletion(string $question, array|callable $choices, string $default = null): mixed
    {
        $question = new Question($question, $default);

        is_callable($choices)
            ? $question->setAutocompleterCallback($choices)
            : $question->setAutocompleterValues($choices);

        return $this->output->askQuestion($question);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     */
    public function secret(string $question, bool $fallback = true): mixed
    {
        $question = new Question($question);

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * Give the user a single choice from an array of answers.
     */
    public function choice(string $question, array $choices, string|int $default = null, int $attempts = null, bool $multiple = false): string|array
    {
        $question = new ChoiceQuestion($question, $choices, $default);

        $question->setMaxAttempts($attempts)->setMultiselect($multiple);

        return $this->output->askQuestion($question);
    }

    /**
     * Format input to textual table.
     */
    public function table(array $headers, array $rows, TableStyle|string $tableStyle = 'default', array $columnStyles = []): Table
    {
        $table = new Table($this->output);

        $table->setHeaders((array) $headers)->setRows($rows)->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $table->render();

        return $table;
    }

    /**
     * Determine if the given argument is present.
     */
    public function hasArgument(string $name): bool
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command argument.
     */
    public function argument(string $key = null): array|string|bool|null
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Get all of the arguments passed to the command.
     */
    public function arguments(): array
    {
        return $this->argument();
    }

    /**
     * Determine if the given option is present.
     */
    public function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }

    /**
     * Get the value of a command option.
     */
    public function option(string $key = null): string|array|bool|null
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Get all of the options passed to the command.
     */
    public function options(): array
    {
        return $this->option();
    }

    /**
     * Write a string as standard output.
     */
    public function line(string $string, string $style = null, string|int $verbosity = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Write a string as information output.
     */
    public function info(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'info', $verbosity);
    }

    /**
     * Write a string as comment output.
     */
    public function comment(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'comment', $verbosity);
    }

    /**
     * Write a string as question output.
     */
    public function question(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'question', $verbosity);
    }

    /**
     * Write a string as error output.
     */
    public function error(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'error', $verbosity);
    }

    /**
     * Write a string as success output.
     */
    public function success(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'success', $verbosity);
    }

    /**
     * Write a string as warning output.
     */
    public function warn(string $string, string|int $verbosity = null): void
    {
        $this->line($string, 'warning', $verbosity);
    }

    /**
     * Write a string in an alert box.
     */
    public function alert(string $string, string|int $verbosity = null): void
    {
        $length = strlen(strip_tags($string)) + 12;

        $this->comment(str_repeat('*', $length), $verbosity);
        $this->comment('*     '.$string.'     *', $verbosity);
        $this->comment(str_repeat('*', $length), $verbosity);

        $this->comment('', $verbosity);
    }

    /**
     * Write a string as standard output.
     */
    public function write(string $string, bool $newline = false, string|int $verbosity = null): void
    {
        $this->output->write($string, $newline, $this->parseVerbosity($verbosity));
    }

    /**
     * Write a blank line.
     */
    public function newLine(int $count = 1): static
    {
        $this->output->newLine($count);

        return $this;
    }

    /**
     * Set the input interface implementation.
     */
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * Set the output interface implementation.
     *
     * @param  \Mike\AdventOfCode\Console\OutputStyle  $output
     * @return void
     */
    public function setOutput(OutputStyle $output): void
    {
        $this->output = $output;
    }

    /**
     * Get the output implementation.
     *
     * @return \Mike\AdventOfCode\Console\OutputStyle
     */
    public function getOutput(): OutputStyle|null
    {
        return $this->output;
    }

    /**
     * Get the input implementation.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput(): InputInterface|null
    {
        return $this->input;
    }

    /**
     * Set the verbosity level.
     *
     * @param  string|int  $level
     * @return void
     */
    protected function setVerbosity(string|int $level): void
    {
        $this->verbosity = $this->parseVerbosity($level);
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     */
    protected function parseVerbosity(string|int $level = null): int
    {
        if (isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (! is_int($level)) {
            $level = $this->verbosity;
        }

        return $level;
    }
}
