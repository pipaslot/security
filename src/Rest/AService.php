<?php

namespace Pipas\Rest;

use Pipas\Rest\Result\IContract;

/**
 * Default abstract read-write repository
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
abstract class AService extends AReadOnlyService implements IService
{

	/**
	 * Create new record by entity
	 * @param IContract $entity
	 * @return IContract
	 */
	public function create(IContract $entity)
	{
		$arr = $entity->toArrayForCreate();
		if (isset($arr['id'])) unset($arr['id']);
		$entity->setId($this->context->create($this->getName(), $arr));
		return $entity;
	}

	/**
	 * Update existing record
	 * @param IContract $entity
	 * @return bool if update was successfully
	 * @throws RestException
	 */
	public function update(IContract $entity)
	{
		if (!$entity->getId()) throw new RestException("Missing entity ID");
		$arr = $entity->toArrayForUpdate();
		if (isset($arr['id'])) unset($arr['id']);
		return $this->context->update($this->getName() . '/' . $entity->getId(), $arr);
	}

	/**
	 * Delete record by entity
	 * @param IContract $entity
	 * @return bool if delete was successfully
	 * @throws \Pipas\Rest\RestException
	 */
	public function delete(IContract $entity)
	{
		if (!$entity->getId()) throw new RestException("Missing entity ID");
		return $this->context->delete($this->getName() . '/' . $entity->getId());
	}

}
