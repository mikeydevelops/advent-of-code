# Advent of Code

My Advent of Code solutions in PHP.

## Solutions

- [Year 2015](aoc/Year2015)
- [Year 2016](aoc/Year2016)

## How to Use

### Requirements

- `php >= 8.1` - only tested on php 8.1, not guaranteeing it works on lower versions, but it probably will if the type declarations are removed.
- `composer`

### Clone the repository

```bash
git clone https://github.com/mikeydevelops/advent-of-code.git
```

### Install Dependencies

```bash
composer install
```

### Prefetch specific day's input

`php fetch-input.php <year>-<day>`  where `<year>` is the year and `<day>` is 0 padded day number for the month of december so `01` to `25`.

```bash
php fetch-input.php 2016-02
```

Prefetching the day's input is not required, since each solution requires the input and when needed, it is automatically downloaded from `adventofcode.com` and cached locally, so that we don't spam the servers.

### Running the solution scripts

`php solution.php <year>-<day>` where `<year>` is the year and `<day>` is 0 padded day number for the month of december so `01` to `25`.

```bash
php solution.php 2016-02
```

## Directories & Files

- `/storage/inputs` holds the inputs for each day and year of the challenge.
- `/storage/session.aoc` holds the session cookie that is provided for input fetching and solution submission.

## Todo

 - [x] automatically fetch input.
 - [ ] submit solutions from cli.
 - [ ] download day description automatically.
