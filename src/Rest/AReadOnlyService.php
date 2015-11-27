<?php

namespace Pipas\Rest;

use Nette\Utils\Strings;
use Pipas\Rest\Result\Contract;
use Pipas\Rest\Result\DataSet;
use Pipas\Rest\Result\ResultMapper;

/**
 * Read only repository for one concrete table
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
abstract class AReadOnlyService implements IReadOnlyService
{
	/**
	 * @var IContext
	 */
	protected $context;
	/**
	 * @var ResultMapper
	 */
	protected $resultMapper;

	function __construct(IContext $context)
	{
		$this->context = $context;
		$this->resultMapper = ResultMapper::get();
	}

	/**
	 * @return IContext
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Returns target service name<br>
	 * As default it convert name of class 'MyExtraService' to 'myExtra'
	 * @return string
	 */
	public function getName()
	{
		$class = get_called_class();
		$slashPos = strrpos($class, "\\");
		$className = substr($class, $slashPos);
		if (Strings::endsWith($className, self::NAME_SUFFIX)) {
			$className = substr($className, 0, strlen($className) - strlen(self::NAME_SUFFIX));
		}
		return lcfirst($className);
	}

	/**
	 * Name of Contract what is used in Service
	 * @return mixed
	 */
	protected abstract function getContractName();

	/**
	 * Find one by ID
	 * @param int $id
	 * @return Contract
	 */
	public function find($id)
	{
		return $this->resultMapper->mapEntity($this->context->find($this->getName(), $id), $this, $this->getContractName());
	}

	/**
	 * Returns all available records
	 * @return DataSet
	 */
	public function findAll()
	{
		return $this->resultMapper->convertDataSetToEntitySet($this->context->findAll($this->getName()), $this, $this->getContractName());
	}

	/**
	 * Returns all available records filtered by query
	 * @param array $query
	 * @return DataSet
	 */
	public function findBy(array $query)
	{
		return $this->resultMapper->convertDataSetToEntitySet($this->context->findBy($this->getName(), $query), $this, $this->getContractName());
	}

	/**
	 * Returns the first from available records filtered by query
	 * @param array $query
	 * @return Contract
	 */
	public function findOneBy(array $query = array())
	{
		return $this->resultMapper->mapEntity($this->context->findOneBy($this->getName(), $query), $this, $this->getContractName());
	}

}
