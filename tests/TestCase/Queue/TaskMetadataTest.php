<?php
declare(strict_types=1);

namespace Queue\Test\TestCase\Queue;

use Cake\TestSuite\TestCase;
use Queue\Queue\TaskMetadata;
use TestApp\Queue\Task\InjectedTask;
use TestApp\Queue\Task\MetadataTask;

class TaskMetadataTest extends TestCase {

	/**
	 * Test that fromClass reads declared property defaults correctly.
	 *
	 * @return void
	 */
	public function testFromClassReadsPropertyDefaults(): void {
		$meta = TaskMetadata::fromClass(MetadataTask::class);

		$this->assertSame(MetadataTask::class, $meta->class);
		$this->assertSame(42, $meta->timeout);
		$this->assertSame(3, $meta->retries);
		$this->assertSame(5, $meta->rate);
		$this->assertSame(75, $meta->costs);
		$this->assertTrue($meta->unique);
	}

	/**
	 * Test that fromClass reads description() from tasks that override it.
	 *
	 * @return void
	 */
	public function testFromClassReadsDescription(): void {
		$meta = TaskMetadata::fromClass(MetadataTask::class);

		$this->assertSame('A task for testing metadata introspection', $meta->description);
	}

	/**
	 * Test that fromClass works for tasks with required constructor DI params
	 * that cannot be instantiated with new $className().
	 *
	 * @return void
	 */
	public function testFromClassWithDiConstructor(): void {
		$meta = TaskMetadata::fromClass(InjectedTask::class);

		$this->assertSame(InjectedTask::class, $meta->class);
		$this->assertSame(10, $meta->timeout);
		$this->assertNull($meta->retries);
		$this->assertSame(0, $meta->rate);
		$this->assertSame(0, $meta->costs);
		$this->assertFalse($meta->unique);
	}

}
