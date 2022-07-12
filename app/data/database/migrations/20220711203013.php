<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class V20220711203013 extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('create table users
            (
                id          uuid                    not null constraint users_pk primary key,
                email       varchar                 not null,
                password    varchar                 not null,
                "lastLogin" timestamp,
                "createdAt" timestamp default now() not null,
                "updatedAt" timestamp default now() not null
            );
        ');
    }

    public function down(): void
    {
        $this->execute('drop table users');
    }
}
