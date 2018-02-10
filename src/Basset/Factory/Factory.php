<?php namespace Basset\Factory;

use Illuminate\Log\Logger;

abstract class Factory {

	/**
	 * Illuminate log writer instance.
	 * 
	 * @var \Illuminate\Log\Logger
	 */
	protected $log;

	/**
	 * Basset factory manager instance.
	 * 
	 * @var \Basset\Factory\FactoryManager
	 */
	protected $factory;

	/**
	 * Set the log writer instance.
	 * 
	 * @param  \Illuminate\Log\Logger  $log
	 * @return \Basset\Factory\Factory
	 */
	public function setLogger(Logger $log)
	{
		$this->log = $log;

		return $this;
	}

	/**
	 * Set the factory manager instance.
	 * 
	 * @param  \Basset\Factory\FactoryManager  $factory
	 * @return \Basset\Factory\Factory
	 */
	public function setFactoryManager(FactoryManager $factory)
	{
		$this->factory = $factory;

		return $this;
	}

}