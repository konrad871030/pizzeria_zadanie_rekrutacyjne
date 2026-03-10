<?php

namespace App\Request;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

final class CreateOrderPayload
{
    private function __construct(
        public readonly int $menuItemId,
        public readonly int $quantity,
        public readonly string $email,
        public readonly string $deliveryAddress,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $menuItemId = self::readPositiveInt($request->request->all()['menu_item_id'] ?? null, 'Rodzaj pizzy');
        $quantity = self::readPositiveInt($request->request->all()['quantity'] ?? null, 'Ilość');
        $email = self::readSafeString($request->request->all()['email'] ?? null, 'Adres e-mail', 180);
        $deliveryAddress = self::readSafeString($request->request->all()['delivery_address'] ?? null, 'Adres dostawy', 255);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Adres e-mail ma niepoprawny format.');
        }

        return new self($menuItemId, $quantity, $email, $deliveryAddress);
    }

    private static function readPositiveInt(mixed $value, string $label): int
    {
        if (!is_scalar($value)) {
            throw new InvalidArgumentException(sprintf('%s jest wymagane.', $label));
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            throw new InvalidArgumentException(sprintf('%s jest wymagane.', $label));
        }

        $intValue = filter_var($normalized, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($intValue === false) {
            throw new InvalidArgumentException(sprintf('%s ma niepoprawną wartość.', $label));
        }

        return $intValue;
    }

    private static function readSafeString(mixed $value, string $label, int $maxLength): string
    {
        if (!is_scalar($value)) {
            throw new InvalidArgumentException(sprintf('%s jest wymagane.', $label));
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            throw new InvalidArgumentException(sprintf('%s jest wymagane.', $label));
        }

        if (mb_strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException(sprintf('%s jest za długie.', $label));
        }

        if ($normalized !== strip_tags($normalized) || str_contains($normalized, '<') || str_contains($normalized, '>')) {
            throw new InvalidArgumentException(sprintf('%s zawiera niedozwolone znaki.', $label));
        }

        return $normalized;
    }
}
