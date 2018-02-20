<?php

namespace ITE\FormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ClearTempFilesCommand
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClearTempFilesCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ite:form:clear-temp-files')
            ->setDescription('Clear temp files')
            ->addArgument('minutes', InputArgument::OPTIONAL, 'How many minutes ago file should be created for its deleting?', 60)
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $minutes = $input->getArgument('minutes');
        $minutes = ctype_digit($minutes) ? (int) $minutes : 60;

        $uploadDir = $this->getContainer()->getParameter('ite_form.component.ajax_file_upload.upload_dir');

        $time = time() - 60 * $minutes;

        $finder = new Finder();
        $finder
            ->files()
            ->in($uploadDir)
            ->depth(0)
            ->filter(function (\SplFileInfo $file) use ($time) {
                return $file->getCTime() < $time;
            });

        $count = count($finder);
        if (0 === $count) {
            $output->writeln('No files were found.');

            return;
        }

        $output->writeln(sprintf('<info>%d</info> files were found.', $count));
        $fs = new Filesystem();
        foreach ($finder as $file) {
            /** @var $file SplFileInfo */
            $output->writeln(sprintf('Removing <comment>%s</comment> file...', $file->getRealPath()));
            $fs->remove($file->getRealPath());
        }
    }
}
