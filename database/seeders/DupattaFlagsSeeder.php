<?php

namespace Database\Seeders;

use App\Models\Addproduct;
use Illuminate\Database\Seeder;

/**
 * Marks which products ship with a dupatta (drives the POS "With / Without
 * Dupatta" popup, the card chip and the discounted line on the bill).
 *
 *   php artisan db:seed --class=DupattaFlagsSeeder
 *
 * The rules are keyed on product_type because that is how the catalog is
 * organised:
 *   - 2 Pcs set (kurthi &shawl)  -> the shawl IS the dupatta
 *   - maxi gown                   -> always sold with a dupatta
 *   - everything else             -> untouched (left to be set per product)
 *
 * Idempotent and non-destructive: it only ever turns the flag ON, so a product
 * an operator deliberately unticked stays unticked.
 */
class DupattaFlagsSeeder extends Seeder
{
    /** Product types that are sold WITH a dupatta. */
    public const DUPATTA_TYPES = [
        '2 Pcs set (kurthi &shawl)',
        'maxi gown',
    ];

    public function run(): void
    {
        $flagged = Addproduct::whereIn('product_type', self::DUPATTA_TYPES)
            ->where('has_dupatta', false)
            ->update(['has_dupatta' => true]);

        // Older rows can carry a NULL discount; the POS falls back to 300 anyway,
        // but store it so the bill and the popup agree on the amount.
        $priced = Addproduct::whereNull('dupatta_discount')
            ->update(['dupatta_discount' => 300]);

        $total = Addproduct::where('has_dupatta', true)->count();

        $this->command?->info("Flagged {$flagged} product(s) with a dupatta ({$priced} discount(s) defaulted).");
        $this->command?->info("{$total} of ".Addproduct::count().' products now have a dupatta.');
    }
}
