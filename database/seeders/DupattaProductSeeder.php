<?php

namespace Database\Seeders;

use App\Models\Addproduct;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds the standalone DUPATTA product - a separate, individually sellable item
 * at a flat Rs.300. It is NOT part of the dress catalog, so it is deliberately
 * flagged has_dupatta = false and the POS never asks "With / Without dupatta"
 * when it is scanned or clicked.
 *
 *   php artisan db:seed --class=DupattaProductSeeder
 *
 * Safe to run repeatedly: an existing dupatta is restocked instead of duplicated.
 */
class DupattaProductSeeder extends Seeder
{
    public const NAME = 'Dupatta';
    public const PRICE = 300.0;
    public const STOCK = 50;
    public const COLOR = 'Multi';
    public const SIZE = 'Free';
    public const COLOR_CODE = 'MUL';

    public function run(): void
    {
        $brand = Brand::firstOrCreate(
            ['name' => 'Zyra'],
            ['abbreviation' => 'zy']
        );

        $existing = Addproduct::whereRaw('LOWER(product_name) = ?', [Str::lower(self::NAME)])->first();

        if ($existing) {
            $existing->update([
                'stock' => self::STOCK,
                'original_price' => self::PRICE,
                'mrp' => self::PRICE,
                'selling_price' => self::PRICE,
                'discount_amount' => 0,
                'has_dupatta' => false,
                'dupatta_discount' => self::PRICE,
            ]);
            $this->syncBrand($brand);
            $this->command?->info('Dupatta already seeded - price set to Rs.'.(int) self::PRICE.', stock set to '.self::STOCK.'.');

            return;
        }

        $productId = $this->nextProductId();
        $barcode = $this->nextBarcode();
        $sku = strtoupper(sprintf('%s-%s-%s-%s', $brand->abbreviation, $productId, self::COLOR_CODE, self::SIZE));

        if (Addproduct::where('sku', $sku)->exists() || Addproduct::where('barcode', $barcode)->exists()) {
            $this->command?->error("Could not seed: generated SKU {$sku} / barcode {$barcode} already in use.");

            return;
        }

        Addproduct::create([
            'product_name' => self::NAME,
            'product_id' => $productId,
            'brand_id' => $brand->id,
            'brand' => $brand->name,
            'product_type' => self::NAME,
            'color' => self::COLOR,
            'size' => self::SIZE,
            'sku' => $sku,
            'barcode' => $barcode,
            'stock' => self::STOCK,
            'original_price' => self::PRICE,
            'mrp' => self::PRICE,
            'selling_price' => self::PRICE,
            'discount_amount' => 0,
            'description' => 'Standalone dupatta sold separately.',
            'has_dupatta' => false,
            'dupatta_discount' => self::PRICE,
        ]);

        $this->syncBrand($brand, $barcode, $sku, $productId);
        $this->command?->info('Seeded dupatta: '.$sku.' | barcode '.$barcode.' | Rs.'.(int) self::PRICE.' | stock '.self::STOCK);
    }

    /**
     * Keep the brand row in sync with its total stock, and advance the
     * per-brand barcode counter when a new barcode was issued.
     */
    private function syncBrand(Brand $brand, ?string $barcode = null, ?string $sku = null, ?string $productId = null): void
    {
        $updates = [
            'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
        ];

        if ($barcode !== null) {
            $updates['barcode'] = $barcode;
        }
        if ($sku !== null) {
            $updates['sku'] = $sku;
        }
        if ($productId !== null) {
            $updates['product_id'] = $productId;
        }

        $brand->update($updates);

        if ($barcode !== null) {
            $brand->increment('barcode_count');
        }
    }

    /**
     * Next zero-padded product_id (001, 002, ...) off the shared counter.
     */
    private function nextProductId(): string
    {
        DB::table('counters')->updateOrInsert(
            ['name' => 'product_id_seq'],
            ['value' => DB::raw('value + 1')]
        );

        return str_pad((string) DB::table('counters')->where('name', 'product_id_seq')->value('value'), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Next auto barcode = ZY + running 8-digit number (e.g. ZY19821067).
     */
    private function nextBarcode(): string
    {
        $max = Addproduct::where('barcode', 'like', 'ZY%')
            ->pluck('barcode')
            ->map(fn ($b) => preg_match('/^ZY(\d{8})$/i', (string) $b) ? (int) substr($b, 2) : 0)
            ->max();

        return 'ZY'.str_pad((string) ($max > 0 ? $max + 1 : 19821001), 8, '0', STR_PAD_LEFT);
    }
}
