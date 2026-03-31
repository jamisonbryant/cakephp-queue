<?php
declare(strict_types=1);

namespace Queue\Queue;

use ReflectionClass;
use Throwable;

/**
 * Reads task metadata (timeout, retries, etc.) from a task class
 * without instantiating it.
 *
 * This allows introspection of tasks that have required constructor
 * parameters (e.g. DI dependencies) where `new $className()` would fail.
 */
class TaskMetadata {

	public function __construct(
		public readonly string $class,
		public readonly ?int $timeout,
		public readonly ?int $retries,
		public readonly int $rate,
		public readonly int $costs,
		public readonly bool $unique,
		public readonly ?string $description,
	) {
	}

	/**
	 * Build metadata for a task class by reading declared property defaults
	 * and calling description() without a full constructor.
	 *
	 * @phpstan-param class-string<\Queue\Queue\Task> $className
	 *
	 * @param string $className
	 *
	 * @return self
	 */
	public static function fromClass(string $className): self {
		$reflection = new ReflectionClass($className);

		$timeout = static::propertyDefault($reflection, 'timeout');
		$retries = static::propertyDefault($reflection, 'retries');
		$rate = static::propertyDefault($reflection, 'rate') ?? 0;
		$costs = static::propertyDefault($reflection, 'costs') ?? 0;
		$unique = static::propertyDefault($reflection, 'unique') ?? false;

		$description = null;
		try {
			/** @var \Queue\Queue\Task $instance */
			$instance = $reflection->newInstanceWithoutConstructor();
			$description = $instance->description();
		} catch (Throwable) {
		}

		return new self($className, $timeout, $retries, $rate, $costs, $unique, $description);
	}

	/**
	 * @param \ReflectionClass<\Queue\Queue\Task> $reflection
	 * @param string $property
	 *
	 * @return mixed
	 */
	protected static function propertyDefault(ReflectionClass $reflection, string $property): mixed {
		if (!$reflection->hasProperty($property)) {
			return null;
		}

		$prop = $reflection->getProperty($property);
		if (!$prop->hasDefaultValue()) {
			return null;
		}

		return $prop->getDefaultValue();
	}

}
