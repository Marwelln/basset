<?php namespace Basset\Console;

use Basset\Environment;
use Basset\Manifest\Manifest;
use Illuminate\Console\Command;
use Basset\Builder\FilesystemCleaner;
use Basset\BassetServiceProvider as Basset;
use Symfony\Component\Console\Input\InputOption;

class DeleteManifestCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'basset:delete-manifest';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete the collection manifest';

	/**
	 * Basset manifest instance.
	 *
	 * @var \Basset\Manifest\Manifest
	 */
	protected $manifest;

	/**
	 * Create a new basset command instance.
	 *
	 * @param  \Basset\Manifest\Manifest  $manifest
	 * @return void
	 */
	public function __construct(Manifest $manifest) {
		parent::__construct();

		$this->manifest = $manifest;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
 		if ($this->manifest->delete())
			$this->info('Manifest has been deleted. All collections will are now required to be rebuilt.');
		else
			$this->comment('Manifest does not exist or could not be deleted.');
	}

}