<?php namespace Basset;

use Basset\Builder\Builder;
use Basset\Builder\FilesystemCleaner;
use Basset\Console\BuildCommand;
use Basset\Console\TidyUpCommand;
use Basset\Factory\FactoryManager;
use Basset\Manifest\Manifest;
use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\NullHandler;
use Monolog\Logger as MonologLogger;

class BassetServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Components to register on the provider.
	 *
	 * @var array
	 */
	protected $components = array(
		'AssetFinder',
		'Logger',
		'FactoryManager',
		'Server',
		'Manifest',
		'Builder',
		'Commands',
		'Basset'
	);

	/**
	 * Bootstrap the application events.
	 */
	public function boot() : void {
		$this->publishes([
			__DIR__ . '/../config/config.php' => config_path('basset.php')
		]);

		// If debugging is disabled we'll use a null handler to essentially send all logged
		// messages into a blackhole.
		if ( ! $this->app['config']->get('basset.debug', false)) {
			$handler = new NullHandler(MonologLogger::WARNING);

			$this->app['basset.log']->getMonolog()->pushHandler($handler);
		}

		$this->app->instance('basset.path.build', $this->app['path.public'].'/'.$this->app['config']->get('basset.build_path'));

		$this->registerBladeExtensions();

		// Collections can be defined as an array in the configuration file. We'll register
		// this array of collections with the environment.
		$this->app['basset']->collections($this->app['config']->get('basset.collections', []));

		// Load the local manifest that contains the fingerprinted paths to both production
		// and development builds.
		$this->app['basset.manifest']->load();
	}

	/**
	 * Register the Blade extensions with the compiler.
	 */
	protected function registerBladeExtensions() : void {
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		$blade->directive('javascripts', function($value){
			return "<?php echo basset_javascripts($value); ?>";
		});

		$blade->directive('stylesheets', function($value){
			return "<?php echo basset_stylesheets($value); ?>";
		});

		$blade->directive('assets', function($value){
			return "<?php echo basset_assets($value); ?>";
		});
	}

	/**
	 * Register the service provider.
	 */
	public function register() : void {
		foreach ($this->components as $component) {
			$this->{'register'.$component}();
		}
	}

	/**
	 * Register the asset finder.
	 */
	protected function registerAssetFinder() : void {
		$this->app->singleton('basset.finder', function($app) {
			return new AssetFinder($app['files'], $app['config'], base_path() . '/resources/assets');
		});
	}

	/**
	 * Register the collection server.
	 */
	protected function registerServer() : void {
		$this->app->singleton('basset.server', function($app) {
			return new Server($app);
		});
	}

	/**
	 * Register the logger.
	 */
	protected function registerLogger() : void {
		$this->app->singleton('basset.log', function($app) {
			return new Logger(new \Monolog\Logger('basset'), $app['events']);
		});
	}

	/**
	 * Register the factory manager.
	 */
	protected function registerFactoryManager() : void {
		$this->app->singleton('basset.factory', function($app)	{
			return new FactoryManager($app);
		});
	}

	/**
	 * Register the collection repository.
	 */
	protected function registerManifest() : void {
		$this->app->singleton('basset.manifest', function($app) {
			return new Manifest($app['files'], storage_path() . '/app');
		});
	}

	/**
	 * Register the collection builder.
	 */
	protected function registerBuilder() : void {
		$this->app->singleton('basset.builder', function($app) {
			return new Builder($app['files'], $app['basset.manifest'], $app['basset.path.build']);
		});

		$this->app->singleton('basset.builder.cleaner', function($app) {
			return new FilesystemCleaner($app['basset'], $app['basset.manifest'], $app['files'], $app['basset.path.build']);
		});
	}

	/**
	 * Register the basset environment.
	 */
	protected function registerBasset() : void {
		$this->app->singleton('basset', function($app) {
			return new Environment($app['basset.factory'], $app['basset.finder']);
		});
	}

	/**
	 * Register the commands.
	 */
	public function registerCommands() : void {
		// Register a command for basset
		$this->app->singleton('command.basset', function($app) {
			return new BuildCommand($app['basset'], $app['basset.builder'], $app['basset.builder.cleaner']);
		});

		$this->commands('command.basset');
	}

	/**
	 * Register the basset command.
	 *
	 * @return void
	 */
	protected function registerBassetCommand() {

	}

	/**
	 * Register the build command.
	 *
	 * @return void
	 */
	protected function registerBuildCommand() {

	}

}
