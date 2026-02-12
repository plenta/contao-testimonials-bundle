<?php

declare(strict_types=1);

/**
 * Testimonials for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoTestimonialsBundle\Enum;

use Contao\CoreBundle\Translation\TranslatableLabelInterface;
use Symfony\Component\Translation\TranslatableMessage;

enum SortingOption: string implements TranslatableLabelInterface
{

    case indexAsc = 'id ASC';
    case indexDesc = 'id DESC';
    case sortingAsc = 'sorting ASC';
    case sortingDesc = 'sorting DESC';
    case tstampAsc = 'tstamp ASC';
    case tstampDesc = 'tstamp DESC';
    case publishedAtAsc = 'publishedAt ASC';
    case publishedAtDesc = 'publishedAt DESC';
    case random = 'RAND()';

    /**
     * Returns the backed values of all enum cases.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $c) => $c->value, self::cases());
    }

    /**
     * Normalize external value to a valid enum case, defaulting to indexAsc.
     */
    public static function normalizeByValue(?string $value): self
    {
        return self::tryFrom($value ?? '') ?? self::getDefault();
    }

    /**
     * Normalize the external name to a valid enum case, defaulting to indexAsc.
     */
    public static function normalizeByName(?string $name): self
    {
        if (!$name || !defined(SortingOption::class . '::' . $name)) {
            return self::getDefault();
        }

        return constant(SortingOption::class . '::' . $name);
    }

    public function label(): TranslatableMessage
    {
        return new TranslatableMessage(
            'TESTIMONIAL.sorting.' . $this->name,
            [],
            'contao_default',
        );
    }

    public static function getDefault(): self
    {
        return self::indexAsc;
    }
}
