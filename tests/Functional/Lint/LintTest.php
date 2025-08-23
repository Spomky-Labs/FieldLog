<?php

declare(strict_types=1);

namespace App\Tests\Functional\Lint;

use Ergebnis\PHPUnit\SlowTestDetector\Attribute\MaximumDuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use function assert;

class LintTest extends KernelTestCase
{
    #[Test]
    #[TestWith(['lint:container'])]
    #[TestWith(['lint:translations'])]
    #[TestWith([
        'lint:twig', [
            'filename' => [__DIR__ . '/../../../templates'],
        ]])]
    #[TestWith([
        'lint:xliff', [
            'filename' => [__DIR__ . '/../../../translations'],
        ]])]
    #[TestWith([
        'lint:yaml', [
            'filename' => [__DIR__ . '/../../../config'],
        ]])]
    #[MaximumDuration(5000)]
    public function theLintCommandAreSuccessful(string $commandName, array $args = []): void
    {
        // Given
        $application = new Application(self::bootKernel());
        $command = $application->find($commandName);
        $commandTester = new CommandTester($command);
        assert(self::$kernel !== null);

        // When
        $commandTester->execute($args);

        // Then
        $commandTester->assertCommandIsSuccessful();
    }
}
