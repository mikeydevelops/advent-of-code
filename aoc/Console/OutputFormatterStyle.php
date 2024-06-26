<?php

namespace Mike\AdventOfCode\Console;

use Symfony\Component\Console\Color;
use Mike\AdventOfCode\Console\Contracts\InheritsOutputFormatterStyleInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

class OutputFormatterStyle implements InheritsOutputFormatterStyleInterface, OutputFormatterStyleInterface
{
    protected Color $color;
    protected string $foreground;
    protected string $background;
    protected array $options;
    protected ?string $href = null;
    protected bool $handlesHrefGracefully;

    /**
     * The properties to be inherited from previous style.
     */
    protected array $inherit;

    /**
     * Initializes output formatter style.
     *
     * @param string|null $foreground The style foreground color name
     * @param string|null $background The style background color name
     */
    public function __construct(?string $foreground = null, ?string $background = null, array $options = [], array $inherit = [])
    {
        $this->color = new Color($this->foreground = $foreground ?: '', $this->background = $background ?: '', $this->options = $options);
        $this->inherit = $inherit;
    }

    /**
     * @return void
     */
    public function setForeground(?string $color = null)
    {
        if (1 > \func_num_args()) {
            trigger_deprecation('symfony/console', '6.2', 'Calling "%s()" without any arguments is deprecated, pass null explicitly instead.', __METHOD__);
        }
        $this->color = new Color($this->foreground = $color ?: '', $this->background, $this->options);
    }

    /**
     * @return void
     */
    public function setBackground(?string $color = null)
    {
        if (1 > \func_num_args()) {
            trigger_deprecation('symfony/console', '6.2', 'Calling "%s()" without any arguments is deprecated, pass null explicitly instead.', __METHOD__);
        }
        $this->color = new Color($this->foreground, $this->background = $color ?: '', $this->options);
    }

    public function setHref(string $url): void
    {
        $this->href = $url;
    }

    /**
     * @return void
     */
    public function setOption(string $option)
    {
        $this->options[] = $option;
        $this->color = new Color($this->foreground, $this->background, $this->options);
    }

    /**
     * @return void
     */
    public function unsetOption(string $option)
    {
        $pos = array_search($option, $this->options);
        if (false !== $pos) {
            unset($this->options[$pos]);
        }

        $this->color = new Color($this->foreground, $this->background, $this->options);
    }

    /**
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->color = new Color($this->foreground, $this->background, $this->options = $options);
    }

    public function apply(string $text): string
    {
        $this->handlesHrefGracefully ??= 'JetBrains-JediTerm' !== getenv('TERMINAL_EMULATOR')
            && (!getenv('KONSOLE_VERSION') || (int) getenv('KONSOLE_VERSION') > 201100)
            && !isset($_SERVER['IDEA_INITIAL_DIRECTORY']);

        if (null !== $this->href && $this->handlesHrefGracefully) {
            $text = "\033]8;;$this->href\033\\$text\033]8;;\033\\";
        }

        return $this->color->apply($text);
    }

    public function getForeground(): string
    {
        return $this->foreground;
    }

    public function getBackground(): string
    {
        return $this->background;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function inheritFrom(InheritsOutputFormatterStyleInterface $style): static
    {
        if (in_array('foreground', $this->inherit) && ! empty($fg = $style->getForeground())) {
            $this->setForeground($fg);
        }

        if (in_array('background', $this->inherit) && ! empty($bg = $style->getBackground())) {
            $this->setBackground($bg);
        }

        if (in_array('options', $this->inherit) && ! empty($options = $style->getOptions())) {
            $this->setOptions($options);
        }

        return $this;
    }
}
