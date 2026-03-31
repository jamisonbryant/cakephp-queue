<?php
declare(strict_types=1);

namespace TestApp\Queue\Task;

use Queue\Queue\Task;

/**
 * Test task with explicit values for all metadata properties.
 *
 * Used to verify TaskMetadata reads the correct declared defaults
 * without instantiating the task.
 */
class MetadataTask extends Task {

	public ?int $timeout = 42;

	public ?int $retries = 3;

	public int $rate = 5;

	public int $costs = 75;

	public bool $unique = true;

	public function run(array $data, int $jobId): void {
	}

	public function description(): ?string {
		return 'A task for testing metadata introspection';
	}

}
