<?php

declare(strict_types=1);

const VERSION_PATTERN = '/\A(?:v\d+\.\d+\.\d+|release\/\d+\.x|(?:feature|hotfix|docs)(?:\/\d+)?\/[a-z0-9]+(?:-[a-z0-9]+)*)\z/';

it('has a version file with exactly one valid line', function () {
    $path = dirname(__DIR__, 2).'/version';

    expect($path)->toBeFile();

    $contents = file_get_contents($path);

    expect($contents)->toEndWith("\n")
        ->and(substr_count($contents, "\n"))->toBe(1)
        ->and(rtrim($contents, "\n"))->toMatch(VERSION_PATTERN);
});

it('accepts valid version values', function (string $value) {
    expect($value)->toMatch(VERSION_PATTERN);
})->with([
    'tag' => 'v0.1.0',
    'tag with several digits' => 'v12.34.56',
    'release branch' => 'release/0.x',
    'latest major release branch' => 'release/12.x',
    'feature branch' => 'feature/add-route-cache',
    'feature branch from an older major' => 'feature/1/add-route-cache',
    'hotfix branch' => 'hotfix/fix-cache-key',
    'hotfix branch from an older major' => 'hotfix/1/fix-cache-key',
    'docs branch' => 'docs/init',
    'docs branch from an older major' => 'docs/2/init',
    'description with digits' => 'feature/php-84-support',
]);

it('rejects invalid version values', function (string $value) {
    expect($value)->not->toMatch(VERSION_PATTERN);
})->with([
    'empty' => '',
    'tag without patch' => 'v1.2',
    'tag without v' => '1.2.3',
    'tag with suffix' => 'v1.2.3-beta',
    'tag with four parts' => 'v1.2.3.4',
    'release without x' => 'release/1',
    'release with minor' => 'release/1.2',
    'release with uppercase x' => 'release/1.X',
    'unknown branch type' => 'fix/x',
    'branch without description' => 'feature',
    'branch with empty description' => 'feature/',
    'uppercase description' => 'feature/Foo',
    'snake case description' => 'feature/add_route_cache',
    'major written as 1.x' => 'feature/1.x/add-route-cache',
    'nested description' => 'feature/1/add/route-cache',
    'trailing hyphen' => 'feature/add-',
    'double hyphen' => 'feature/add--cache',
    'trailing newline' => "v1.2.3\n",
    'surrounding space' => ' v1.2.3',
    'main' => 'main',
]);
