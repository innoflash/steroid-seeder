<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

final class ReflectionMapper
{
    public function fromMethodReturnType(\ReflectionMethod $method): Type
    {
        if (!$method->hasReturnType()) {
            return new UnknownType;
        }

        $returnType = $method->getReturnType();

        \assert($returnType instanceof \ReflectionNamedType || $returnType instanceof \ReflectionUnionType);

        if ($returnType instanceof \ReflectionNamedType) {
            if ($returnType->getName() === 'self') {
                return ObjectType::fromName(
                    $method->getDeclaringClass()->getName(),
                    $returnType->allowsNull()
                );
            }

            if ($returnType->getName() === 'parent') {
                $parentClass = $method->getDeclaringClass()->getParentClass();

                // @codeCoverageIgnoreStart
                if ($parentClass === false) {
                    throw new RuntimeException(
                        \sprintf(
                            '%s::%s() has a "parent" return type declaration but %s does not have a parent class',
                            $method->getDeclaringClass()->getName(),
                            $method->getName(),
                            $method->getDeclaringClass()->getName()
                        )
                    );
                }
                // @codeCoverageIgnoreEnd

                return ObjectType::fromName(
                    $parentClass->getName(),
                    $returnType->allowsNull()
                );
            }

            return Type::fromName(
                $returnType->getName(),
                $returnType->allowsNull()
            );
        }

        $types = [];

        foreach ($returnType->getTypes() as $type) {
            if ($type->getName() === 'self') {
                $types[] = ObjectType::fromName(
                    $method->getDeclaringClass()->getName(),
                    false
                );
            } else {
                $types[] = Type::fromName($type->getName(), false);
            }
        }

        return new UnionType(...$types);
    }
}
