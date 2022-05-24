#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

require __DIR__ . '/vendor/autoload.php';

$application = new Application;
$application->setDefaultCommand('configure');

$application->add(
    new class extends Command {
        protected static $defaultName = 'configure';
        private InputInterface $input;
        private OutputInterface $output;

        protected function execute(InputInterface $input, OutputInterface $output): int
        {
            $this->input = $input;
            $this->output = $output;

            $stub = 'cp -r -f ' . escapeshellarg(__DIR__ . '/stubs') . ' ' . escapeshellarg(__DIR__);
            $stub .= ' && rm -rf ' . escapeshellarg(__DIR__ . '/stubs');

            $this->exec($stub);
            rename(__DIR__ . '/github', __DIR__ . '/.github');

            $author = $this->ask('Author Name', $this->exec('git config user.name'));
            $email = $this->ask('Author Email', $this->exec('git config user.email'));
            $vendor = $this->ask('Author Username', $this->getDefaultUsername());
            $name = $this->ask('Package Name', $this->title($slug = $this->slugify(basename((string) getcwd()))));
            $package = $this->ask('Package Slug', $slug);
            $description = $this->ask('Package Description', "This is my package {$name}.");
            $namespace = $this->ask('Package Namespace', $this->getDefaultNamespace($vendor, $package));

            $info = compact('author', 'email', 'vendor', 'package', 'name', 'description', 'namespace');

            $this->replaceComposer($info);

            $files = $this->isWin() ? $this->replaceForWindows() : $this->replaceForAllOtherOSes();

            foreach ($files as $file) {
                $this->replace($info, $file);
            }

            if (!file_exists(__DIR__ . '/.git') && $this->confirm('Init git repository?', true)) {
                $this->exec('git init');
            }

            $this->exec('composer update');

            unlink(__FILE__);

            return Command::SUCCESS;
        }

        /**
         * @param array<mixed> $info
         * @param string       $file
         */
        protected function replace(array $info, string $file): void
        {
            $content = (string) file_get_contents($file);
            $search = array_map(fn ($k) => "{{{$k}}}", array_keys($info));
            $search[] = 'Awuxtron\\PackageSkeleton';

            $replace = array_values($info);
            $replace[] = $info['namespace'];

            file_put_contents($file, str_replace($search, $replace, $content));
        }

        protected function isWin(): bool
        {
            return str_starts_with(strtoupper(PHP_OS), 'WIN');
        }

        /**
         * @return string[]
         */
        protected function replaceForAllOtherOSes(): array
        {
            $cmd = 'grep -E -r -l -i "{{|Awuxtron" --exclude-dir=vendor ./* ./.github/* | grep -v ' . basename(
                __FILE__
            );

            return explode(PHP_EOL, $this->exec($cmd));
        }

        /**
         * @return string[]
         */
        protected function replaceForWindows(): array
        {
            $cmd = 'dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | ';
            $cmd .= 'findstr /v /i ' . basename(__FILE__) . ' | findstr /r /i /M /F:/ "{{|Awuxtron"';

            return preg_split('/\\r\\n|\\r|\\n/', $this->exec($cmd)) ?: [];
        }

        /**
         * @param array<mixed> $info
         */
        protected function replaceComposer(array $info): void
        {
            $repo = $info['vendor'] . '/' . $info['package'];
            $content = (string) file_get_contents($path = __DIR__ . '/composer.json');

            $content = str_replace(
                ['awuxtron/php-package-skeleton', 'Awuxtron\\\\PackageSkeleton'],
                [$repo, rtrim(str_replace('\\', '\\\\', $info['namespace']), '\\')],
                $content
            );

            $composer = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            $composer['description'] = $info['description'];
            $composer['authors'][0]['name'] = $info['author'];
            $composer['authors'][0]['email'] = $info['email'];

            unset(
                $composer['authors'][0]['homepage'], $composer['require-dev']['symfony/console'], $composer['scripts']['post-create-project-cmd']
            );

            file_put_contents(
                $path,
                json_encode(
                    $composer,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                )
            );
        }

        protected function ask(string $question, mixed $default = '', string $type = Question::class, bool $required = true): mixed
        {
            if (!empty($default)) {
                $question .= " ({$default})";
            }

            $answer = $this->getHelper('question')->ask(
                $this->input,
                $this->output,
                new $type($question . ': ', $default)
            );

            if ($required && empty($answer)) {
                return $this->ask($question, $default, $type);
            }

            return $answer;
        }

        protected function confirm(string $question, bool $default = false): bool
        {
            return $this->ask($question, $default, ConfirmationQuestion::class, false);
        }

        protected function exec(string $command): string
        {
            return trim((string) shell_exec($command)); // @phpstan-ignore-line
        }

        protected function getDefaultUsername(): string
        {
            $cmd = $this->exec('git config remote.origin.url');

            if (empty($cmd)) {
                return '';
            }

            return basename(dirname(explode(':', $cmd)[1]));
        }

        protected function getDefaultNamespace(string $username, string $package): string
        {
            return $this->pascal($username) . '\\' . $this->pascal($package);
        }

        protected function slugify(string $str): string
        {
            return strtolower(trim((string) preg_replace('/[^A-Za-z\d-]+/', '-', $str), '-'));
        }

        protected function title(string $str): string
        {
            return ucwords(str_replace(['-', '_'], ' ', $str));
        }

        protected function pascal(string $str): string
        {
            return str_replace(' ', '', $this->title($str));
        }
    }
);

$application->run();
