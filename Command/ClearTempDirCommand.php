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
 * Class ClearTempDirCommand
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClearTempDirCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ite:form:clear-temp-dir')
            ->setDescription('Clear temp directory')
            ->addArgument('minutes', InputArgument::OPTIONAL, 'How many minutes ago directory should be created for its deleting?', 60)
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $minutes = $input->getArgument('minutes');
        $minutes = ctype_digit($minutes) ? (int) $minutes : 60;

        $webRoot = rtrim($this->getContainer()->getParameter('ite_form.file_manager.web_root'), '/');
        $tmpPrefix = trim($this->getContainer()->getParameter('ite_form.file_manager.tmp_prefix'), '/');

        $time = time() - 60 * $minutes;

        $finder = new Finder();
        $finder->directories()->in($webRoot . '/' . $tmpPrefix)->depth(0)
            ->filter(function(\SplFileInfo $dir) use ($time) {
                return $dir->getCTime() < $time;
            });

        $fs = new Filesystem();
        foreach ($finder as $dir) {
            /** @var $dir SplFileInfo */
            $fs->remove($dir->getRealPath());
        }
    }
}