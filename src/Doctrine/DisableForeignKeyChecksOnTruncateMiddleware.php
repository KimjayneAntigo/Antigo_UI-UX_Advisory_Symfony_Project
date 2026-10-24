<?php

namespace App\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsMiddleware;
use Doctrine\DBAL\Driver as DriverInterface;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

/**
 * DBAL middleware that temporarily disables foreign key checks during TRUNCATE TABLE statements
 * and seamlessly restores the active transaction that MySQL implicitly commits during DDL operations.
 * This allows `doctrine:fixtures:load --purge-with-truncate` to succeed reliably on MySQL 8.0.
 */
#[AsMiddleware]
final class DisableForeignKeyChecksOnTruncateMiddleware implements Middleware
{
    public function wrap(DriverInterface $driver): DriverInterface
    {
        return new class ($driver) extends AbstractDriverMiddleware {
            public function connect(array $params): DriverConnection
            {
                return new class (parent::connect($params)) extends AbstractConnectionMiddleware {
                    private bool $inTransaction = false;

                    public function beginTransaction(): void
                    {
                        $this->inTransaction = true;
                        parent::beginTransaction();
                    }

                    public function commit(): void
                    {
                        $this->inTransaction = false;
                        parent::commit();
                    }

                    public function rollBack(): void
                    {
                        $this->inTransaction = false;
                        parent::rollBack();
                    }

                    public function exec(string $sql): int|string
                    {
                        if (stripos(ltrim($sql), 'TRUNCATE') === 0) {
                            parent::exec('SET FOREIGN_KEY_CHECKS = 0');
                            try {
                                $res = parent::exec($sql);
                            } finally {
                                parent::exec('SET FOREIGN_KEY_CHECKS = 1');
                            }

                            if ($this->inTransaction) {
                                parent::beginTransaction();
                            }

                            return $res;
                        }

                        return parent::exec($sql);
                    }
                };
            }
        };
    }
}
