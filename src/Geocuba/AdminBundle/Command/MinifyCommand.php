<?php

namespace Geocuba\AdminBundle\Command;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class MinifyCommand
 * @package Geocuba\AdminBundle\Command
 */
class MinifyCommand extends ContainerAwareCommand
{
    const APP_VENDOR = "Geocuba";

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:minify')
            ->setDescription('Shrink CSS & JavaScript files.')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'The bundle name (optional)')
            ->addOption('compressed', 'c', InputOption::VALUE_NONE, 'Compress (gzip) the data. Requires extra configuration in the web server.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $bundlesIter = new \ArrayIterator($container->get('kernel')->getBundles());
        $bundleRef = null;

        $bundleName = $input->getArgument('bundle');
        $compressed = $input->getOption('compressed');

        while ($bundleRef = $bundlesIter->current()) {
            /** @var BundleInterface $bundleRef */

            $bundleRef = $bundlesIter->current();
            if (empty($bundleName) || $bundleRef->getName() === $bundleName) {
                if (strpos($bundleRef->getNamespace(), self::APP_VENDOR) !== false) {
                    $output->writeln(sprintf("\n Minifying CSS & JavaScript files in bundle \"%s\" (%s):\n", $bundleRef->getName(), $bundleRef->getNamespace()));

                    // Table
                    $table = (new Table($output))
                        ->setHeaders([['Type', 'Original', 'Modified' . ($compressed ? ' (GZIP)' : '')]])
                        ->setStyle('borderless');

                    // Rows
                    $this->addRows($table, 'JS', $this->minifyBundleFiles($container, $bundleRef->getPath(), 'js', $compressed));
                    $table->addRow(new TableSeparator());
                    $this->addRows($table, 'CSS', $this->minifyBundleFiles($container, $bundleRef->getPath(), 'css', $compressed));

                    $table->render();

                    if (!empty($bundleName)) {
                        break;
                    }
                }
            }

            $bundleRef = null;
            $bundlesIter->next();
        }

        if (!empty($bundleName) && !$bundleRef) {
            $output->writeln(sprintf('The bundle "%s" was not found.', $bundleName));
            return;
        }
    }

    /**
     * Search recursively the files in the directory excluding the files with '.min'
     *
     * @param ContainerInterface $container
     * @param string $path
     * @param string $ext
     * @param bool $compressed
     * @return array
     */
    private function minifyBundleFiles($container, $path, $ext, $compressed)
    {
        $fs = $container->get('filesystem');
        $searchPath = implode(DIRECTORY_SEPARATOR, [$path, 'Resources', 'public', $ext]);

        if (!$fs->exists($searchPath)) {
            return [];
        }

        $files = [];

        foreach (new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($searchPath)), '/^.+(?<!(\.min))\.' . $ext . '$/i', \RecursiveRegexIterator::GET_MATCH) as $file) {
            $file = $file[0];

            list($filename, $extension) = explode('.', basename($file));
            $minifiedFile = dirname($file) . DIRECTORY_SEPARATOR . $filename . '.min.' . $extension;

            $minifier = $ext === 'js' ? new JS() : new CSS();
            $minifier->add($file)->minify($minifiedFile);

            if ($compressed) {
                $minifier->gzip($minifiedFile . '.gz');
            }

            $fs->touch([$file, $minifiedFile]); // It is recommended that the modification date and time of original and compressed files be the same. (http://nginx.org/en/docs/http/ngx_http_gzip_static_module.html)

            $files[$this->getFileDescription($file)] = $this->getFileDescription($minifiedFile) . ($compressed ? '.gz' : '');
        }

        return $files;
    }

    /**
     * Get a partial file path.
     *
     * @param string $file
     * @return string
     */
    private function getFileDescription($file)
    {
        return basename(dirname($file)) . DIRECTORY_SEPARATOR . basename($file);
    }

    /**
     * Adds rows to the table.
     *
     * @param Table $table
     * @param string $type
     * @param array $files
     */
    private function addRows($table, $type, $files)
    {
        // First row
        $row = [new TableCell($type, ['rowspan' => count($files) === 0 ? 1 : count($files)])];
        if (!empty($files)) {
            $row = array_merge($row, [array_keys($files)[0], array_values($files)[0]]);
        } else {
            $row = array_merge($row, ['-', '-']);
        }
        $table->addRow($row);

        array_shift($files); // remove the first

        // Other rows
        foreach ($files as $file => $minifiedFile) {
            $table->addRow([$file, $minifiedFile]);
        }
    }
}
