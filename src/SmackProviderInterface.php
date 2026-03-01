<?php declare(strict_types=1);

namespace Visifo\SmackClause;

interface SmackProviderInterface
{
    public function register(SmackRegistry $registry): void;
}
