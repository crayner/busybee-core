<?php

namespace Busybee\DatabaseBundle\Model ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;

class DatabaseManager
{
	private $refresh ;
	private $file ;
	private $container ;
	private $yaml ;
	private $dumper ;
	private $messages ;
	private $filename;
	private $context;
	private $tableManager ;
	private $enumManager ;
	private $logger ;
	
	public function __construct( Container $container)
	{
		$this->refresh = false;
		$this->container = $container ;
		$this->messages = NULL;
		$this->logger = $this->container->get('monolog.logger.busybee');
		
		$this->context = array('File'=>__FILE__);
	}
	
	public function manageUpload($form)
	{
		$this->messages = array();
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		$this->refresh = (bool)$form->get('refresh')->getData();
		if ($this->refresh) 
		{
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->info($this->container->get('translator')->trans('database.load.refresh.on', array(), 'BusybeeDatabaseBundle'), $context);
		}
		$this->file = $form->get('name')->getData();
		$this->filename = $this->file->getClientOriginalName();
		$x = explode('.', $this->filename);
		$y = count($x) - 1 ;
		$extension = $x[$y];

        // Move the file to the directory where uploads are managed.
		$databaseDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/database';
        $fileName = md5(uniqid()) . '.' . $extension;
        $this->file->move($databaseDir, $fileName);
		$context['LINE'] = intval(__LINE__) + 1;
		$this->logger->info(sprintf("I opened file %s.", $this->filename), $context);
		
		if ($extension === 'zip')
		{
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->info(
				$this->container->get('translator')->trans('database.load.filetype.zip', array(
					'%name%' => $this->filename,
				), 
				'BusybeeDatabaseBundle'), 
				$context
			);
			$zip = new \ZipArchive;
			$res = $zip->open($databaseDir.'/'.$fileName);
			$this->removeAllYaml($databaseDir);
			if ($res) {
  				$zip->extractTo($databaseDir);
  				$zip->close();
			}
			unlink($databaseDir.'/'.$fileName);
			$this->processYamlFiles($databaseDir);
		} elseif ($extension === 'yml')
		{
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->info(
				$this->container->get('translator')->trans('database.load.filetype.yml', array(
					'%name%' => $this->filename,
				), 
				'BusybeeDatabaseBundle'), 
				$context
			);
			$contents = file_get_contents($databaseDir.'/'.$fileName);
			unlink($databaseDir.'/'.$fileName);
			$this->manageContents($contents);
		} else
		{
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->warning(
				$this->container->get('translator')->trans('database.load.filetype.fail', array(
					'%name%' => $this->filename,
				), 
				'BusybeeDatabaseBundle'), 
				$context
			);
			dump($fileName);
			throw new \InvalidArgumentException($this->container->get('translator')->trans('database.load.filetype.fail', array(
					'%name%' => $this->filename,
				), 
				'BusybeeDatabaseBundle'));
		}
		return $this->messages;
	}
	
	private function processYamlFiles($path)
	{
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ('.' === $file) continue;
				if ('..' === $file) continue;
				$x = pathinfo($path . '/' . $file);
				if ($x['extension'] === 'yml') 
				{
					$contents = file_get_contents($path . '/' . $file);
					unlink($path . '/' . $file);
					$this->filename = $file;
					$context['LINE'] = intval(__LINE__) + 1;
					$this->logger->info(sprintf('The file %s has been identified as a yml file.', $file), $context);
					$this->manageContents($contents);
				}
			}
			closedir($handle);
		}
	}
	
	private function removeAllYaml($path)
	{
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ('.' === $file) continue;
				if ('..' === $file) continue;
				$x = pathinfo($path . '/' . $file);
				if ($x['extension'] === 'yml')
					unlink($path . '/' . $file);
			}
			closedir($handle);
		}
	}
	
	private function manageContents($contents)
	{
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		$this->yaml = new Parser();
		$this->dumper = new Dumper();
		$this->content = $this->yaml->parse($contents);
		$this->messages[] = array('status' => 'info', 
			'message' => $this->container->get('translator')->trans('database.load.process.start', array(
				'%name%' => $this->filename,
			),
		'BusybeeDatabaseBundle'));
		$context['LINE'] = intval(__LINE__) + 1;
		$this->logger->info(
			$this->container->get('translator')->trans(
				'database.load.process.start', 
				array(
					'%name%' => $this->filename,
				),
				'BusybeeDatabaseBundle'),
			$context);
			
		foreach ($this->content as $name => $config)
		{
			switch (strtolower($name)) 
			{
				case 'tables':
					$this->tableManager = $this->container->get('database.table.manager')->manageTables($config, $this->refresh);
					break;
				case 'enumerators':
					$this->enumManager = $this->container->get('database.enumerator.manager')->manageEnumerators($config, $this->refresh);
					break;
				default:
					throw new \InvalidArgumentException(sprintf('What to do with %s?', $name));
			}
		}
		$this->saveDatabase();
	}
	
	private function saveDatabase()
	{
		$changed = false;
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		if (! empty($this->enumManager))
			$changed = $this->enumManager->saveEnumerators($changed);
		if (! empty($this->tableManager))
		{
			$changed = $this->tableManager->saveTables($changed);
			$changed = $this->tableManager->saveFields($changed);
		}
		if (! $changed) {
			$this->messages[] = array('status' => 'success', 'message' => $this->container->get('translator')->trans('database.load.saved.nochange', array(
					'%name%' => $this->filename
				), 'BusybeeDatabaseBundle'));
					$context['LINE'] = intval(__LINE__) + 1;
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->info(
				$this->container->get('translator')->trans(
					'database.load.saved.nochange', 
					array(
						'%name%' => $this->filename,
					),
					'BusybeeDatabaseBundle'),
				$context);
		} else {
			$context['LINE'] = intval(__LINE__) + 1;
			$this->logger->info(
				$this->container->get('translator')->trans(
					'database.load.saved.change', 
					array(
						'%name%' => $this->filename,
					),
					'BusybeeDatabaseBundle'),
				$context);
			$this->messages[] = array('status' => 'success', 'message' => $this->container->get('translator')->trans('database.load.saved.change', array(
					'%name%' => $this->filename
				), 'BusybeeDatabaseBundle'));
		}
	}		
}