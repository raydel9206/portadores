<?php

namespace Geocuba\AdminBundle\Command;

use Patchwork\JSqueeze;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class JSqueezeCommand
 * @package Geocuba\AdminBundle\Command
 */
class JSqueezeCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('app:jsqueeze')
            ->setDescription('Shrink JS files.')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var BundleInterface $bundle */
        $bundle = null;
        $bundles_iter = new \ArrayIterator($container->get('kernel')->getBundles());

        while ($bundles_iter->current()) {
            $_bundle = $bundles_iter->current();
            if ($_bundle->getName() === $input->getArgument('bundle')) {
                $bundle = $_bundle;
                break;
            }
            $bundles_iter->next();
        }

        if (!$bundle) {
            $output->writeln('Missing bundle');
            return;
        }

        $fs = $container->get('filesystem');
        $dir_path = $this->buildPath($bundle->getPath(), 'Resources', 'public', 'js');

        if (!$fs->exists($dir_path)) {
            $output->writeln(sprintf('The directory Resources/js was not found in bundle %s.', $input->getArgument('bundle')));
            return;
        }

        $files = $this->searchFiles($dir_path);
        $total = count($files);
        $jsqueeze = new JSqueeze();

        $output->writeln(sprintf("\nFound %d %s in directory %s:\n", $total, $total === 1 ? 'file' : 'files', $dir_path));

        foreach ($files as $index => $file) {
            list($filename, $ext) = explode('.', basename($file));
            $new_file = dirname($file) . DIRECTORY_SEPARATOR . $filename . '.min.' . $ext;

            $output->writeln(sprintf("  [%d] %s => %s.", $index + 1, basename(dirname($file)) . DIRECTORY_SEPARATOR . basename($file), basename(dirname($new_file)) . DIRECTORY_SEPARATOR . basename($new_file)));

            $fs->dumpFile($new_file, $jsqueeze->squeeze(file_get_contents($file)));
        }

        $output->writeln(sprintf("\n%d %s %s shrinked.", $total, $total === 1 ? 'file' : 'files', $total === 1 ? 'was' : 'were'));
    }

    /**
     * Build a path with the args.
     *
     * @return string
     */
    private function buildPath()
    {
        $path = '';
        foreach (func_get_args() as $index => $arg) {
            $path = $index !== 0 ? ($path . DIRECTORY_SEPARATOR . $arg) : $arg;
        }
        return $path;
    }

    /**
     * Search recursively the JS files in the directory and exclude the files with '.min'.
     *
     * @param string $dir_path
     * @param string $regex https://regex101.com/
     * @return array
     */
    private function searchFiles($dir_path, $regex = '/^.+(?<!(\.min))\.js$/i')
    {
        $files = [];
        foreach (new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir_path)), $regex, \RecursiveRegexIterator::GET_MATCH) as $file) {
            $files[] = $file[0];
        }
        return $files;
    }

}
