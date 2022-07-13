<?php

declare(strict_types=1);

namespace App\Infrastructure\InputFilter\User;

use Laminas\Filter\StringTrim;
use Laminas\Filter\ToNull;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;

final class Registration extends InputFilter
{
    public function init(): void
    {
        $this->add([
            'name' => 'identity',
            'validators' => [
                new EmailAddress(),
            ],
            'filters' => [
                new StringTrim(),
                new ToNull(),
            ],
        ]);

        $this->add([
            'name' => 'password',
            'validators' => [
                new NotEmpty(),
            ],
            'filters' => [
                new StringTrim(),
                new ToNull(),
            ],
        ]);

        $this->add([
            'name' => 'password_repeat',
            'validators' => [
                new NotEmpty(),
                (new Identical())
                    ->setToken('password'),
            ],
            'filters' => [
                new StringTrim(),
                new ToNull(),
            ],
        ]);
    }
}
