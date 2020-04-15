<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Пройтись в директории и вернуть сумму всех чисел из файла count
 */
class CountCommand extends Command
{
    private const COUNT_FILE = 'count.txt';

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:count';

    protected function configure()
    {
        $this
            ->addArgument('directory', InputArgument::REQUIRED, 'directory')
            ->setDescription('Sum in count.txt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        if (!is_dir($directory)) {
            $output->writeln('Argument is not directory');
            return 0;
        }
        $countFiles = $this->findCountFileInDirectory($directory);
        $sum = 0;

        foreach ($countFiles as $file) {
            $sum += is_integer(file_get_contents($file)) ? file_get_contents($file) : 0;
        }
        $output->writeln('Sum count = '.$sum);
        return $sum;
    }

    private function findCountFileInDirectory (string $directory): array {

        $countFiles = [];
        $files = array_diff(scandir($directory), ['..', '.']);

        foreach ($files as $file) {
            $fileWithPath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fileWithPath)) {
                $countFiles = array_merge($countFiles, $this->findCountFileInDirectory($fileWithPath));
            } elseif ($file === self::COUNT_FILE) {
                $countFiles[] = $fileWithPath;
            }
        }

        return $countFiles;
    }
}
