<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;

    public function __construct($products = null)
    {
        $this->products = $products;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->products) {
            return $this->products;
        }
        
        return Product::with('supplier')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Description',
            'Prix',
            'Quantité',
            'Catégorie',
            'Fournisseur',
            'Seuil d\'alerte',
            'Valeur totale',
            'Date de création',
            'Dernière mise à jour'
        ];
    }

    /**
     * @param mixed $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->price,
            $product->quantity,
            $product->category,
            $product->supplier ? $product->supplier->name : 'Non défini',
            $product->alert_threshold,
            $product->price * $product->quantity,
            $product->created_at->format('d/m/Y'),
            $product->updated_at->format('d/m/Y')
        ];
    }
} 