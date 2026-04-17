<?php

declare(strict_types=1);

namespace Marwa\Support\Validation;

use Marwa\Support\Validation\Contracts\RuleInterface;
use Marwa\Support\Validation\Rules\BetweenRule;
use Marwa\Support\Validation\Rules\ConfirmedRule;
use Marwa\Support\Validation\Rules\DateRule;
use Marwa\Support\Validation\Rules\DateFormatRule;
use Marwa\Support\Validation\Rules\DeclinedRule;
use Marwa\Support\Validation\Rules\EmailRule;
use Marwa\Support\Validation\Rules\InRule;
use Marwa\Support\Validation\Rules\MaxRule;
use Marwa\Support\Validation\Rules\MinRule;
use Marwa\Support\Validation\Rules\NumericRule;
use Marwa\Support\Validation\Rules\RequiredRule;
use Marwa\Support\Validation\Rules\SameRule;
use Marwa\Support\Validation\Rules\StringRule;
use Marwa\Support\Validation\Rules\UrlRule;
use Marwa\Support\Validation\Rules\AcceptedRule;
use Marwa\Support\Validation\Rules\ArrayRule;
use Marwa\Support\Validation\Rules\BooleanRule;
use Marwa\Support\Validation\Rules\IntegerRule;
use Marwa\Support\Validation\Rules\RegexRule;

final class RuleRegistry
{
    /**
     * @var array<string, class-string<RuleInterface>>
     */
    private array $rules = [];

    public function __construct()
    {
        $this->registerDefaultRules();
    }

    public function register(string $name, string $class): void
    {
        $this->rules[$name] = $class;
    }

    /**
     * @param array<string, class-string<RuleInterface>> $rules
     */
    public function registerMany(array $rules): void
    {
        foreach ($rules as $name => $class) {
            $this->register($name, $class);
        }
    }

    public function get(string $name): ?string
    {
        return $this->rules[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    /**
     * @return array<string, class-string<RuleInterface>>
     */
    public function all(): array
    {
        return $this->rules;
    }

    public function resolve(string $name, string $params = ''): ?RuleInterface
    {
        $class = $this->get($name);

        if ($class === null) {
            return null;
        }

        return $this->instantiateRule($class, $params);
    }

    private function instantiateRule(string $class, string $params): RuleInterface
    {
        $rule = new $class($params);

        if (!$rule instanceof RuleInterface) {
            throw new \InvalidArgumentException(
                sprintf('Class %s must implement %s', $class, RuleInterface::class)
            );
        }

        return $rule;
    }

    private function registerDefaultRules(): void
    {
        $this->registerMany([
            'required' => RequiredRule::class,
            'email' => EmailRule::class,
            'string' => StringRule::class,
            'integer' => IntegerRule::class,
            'numeric' => NumericRule::class,
            'boolean' => BooleanRule::class,
            'array' => ArrayRule::class,
            'url' => UrlRule::class,
            'accepted' => AcceptedRule::class,
            'declined' => DeclinedRule::class,
            'confirmed' => ConfirmedRule::class,
            'same' => SameRule::class,
            'in' => InRule::class,
            'min' => MinRule::class,
            'max' => MaxRule::class,
            'between' => BetweenRule::class,
            'date' => DateRule::class,
            'date_format' => DateFormatRule::class,
            'regex' => RegexRule::class,
        ]);
    }
}